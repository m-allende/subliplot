<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Models\Phone;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Rules\ValidRut;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('store.profile.index', compact('user'));
    }

    // ============================
    // DATOS PERSONALES
    // ============================
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->only(["name", "rut"]),[
            'name'  => 'required|string|max:255',
            'rut'   => [
                'nullable',
                'string',
                'max:20',
                new ValidRut
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }
         
        $data = $validator->validated();

        // (Opcional) Normaliza y guarda el RUT sin puntos y con guion
        if (!empty($data['rut'])) {
            $data['rut'] = strtoupper(str_replace(['.', ' '], '', $data['rut']));
        }

        $user->update($data);

        return response()->json([
            'status'  => 200,
            'message' => 'Datos actualizados correctamente.'
        ]);
    }


    // ============================
    // AVATAR
    // ============================
    public function avatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|max:2048'
        ]);

        $file = $request->file('avatar');
        $path = $file->store('users', 'public_uploads');

        // Elimina la foto anterior si existe
        if ($user->photos()->where('is_primary', true)->exists()) {
            $old = $user->photos()->where('is_primary', true)->first();
            if ($old && Storage::disk($old->disk)->exists($old->path)) {
                Storage::disk($old->disk)->delete($old->path);
            }
            $old->delete();
        }

        $user->photos()->create([
            'disk' => 'public_uploads',
            'path' => $path,
            'is_primary' => true
        ]);

        return response()->json([
            'status' => 200,
            'avatar_url' => Storage::disk('public_uploads')->url($path),
        ]);
    }

    // ============================
    // CONTRASEÑA
    // ============================
    public function password(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status'=>400, 'message'=>'Contraseña actual incorrecta.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['status'=>200, 'message'=>'Contraseña actualizada.']);
    }

    // ============================
    // DIRECCIONES
    // ============================
    public function addresses()
    {
        $user = Auth::user();
        $data = $user->addresses()->with(['country','region','commune'])->get();

        return response()->json(['status'=>200, 'data'=>$data]);
    }

    public function showAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->with(['country','region','commune'])->findOrFail($id);

        return response()->json(['status'=>200, 'data'=>$address]);
    }

    public function storeAddress(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'id'          => 'nullable|integer',
            'line1'       => 'required|string|max:255',
            'line2'       => 'nullable|string|max:255',
            'reference'   => 'nullable|string|max:255',
            'country_id'  => 'required|integer',
            'region_id'   => 'required|integer',
            'commune_id'  => 'required|integer',
            'is_primary'  => 'boolean',
        ]);

        if (!empty($data['id'])) {
            $address = $user->addresses()->findOrFail($data['id']);
            $address->update($data);
        } else {
            $address = $user->addresses()->create($data);
        }

        if ($data['is_primary'] ?? false) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_primary'=>false]);
        }

        return response()->json(['status'=>200, 'message'=>'Dirección guardada.']);
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        $address->delete();

        return response()->json(['status'=>200, 'message'=>'Dirección eliminada.']);
    }

    // ============================
    // TELÉFONOS
    // ============================
    public function phones()
    {
        $user = Auth::user();
        $data = $user->phones()->get();
        return response()->json(['status'=>200, 'data'=>$data]);
    }

    public function showPhone($id)
    {
        $user = Auth::user();
        $phone = $user->phones()->findOrFail($id);
        return response()->json(['status'=>200, 'data'=>$phone]);
    }

    public function storePhone(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'id'         => 'nullable|integer',
            'number'     => 'required|string|max:30',
            'is_default' => 'boolean',
        ]);

        if (!empty($data['id'])) {
            $phone = $user->phones()->findOrFail($data['id']);
            $phone->update($data);
        } else {
            $phone = $user->phones()->create($data);
        }

        if ($data['is_default'] ?? false) {
            $user->phones()->where('id', '!=', $phone->id)->update(['is_default'=>false]);
        }

        return response()->json(['status'=>200, 'message'=>'Teléfono guardado.']);
    }

    public function deletePhone($id)
    {
        $user = Auth::user();
        $phone = $user->phones()->findOrFail($id);
        $phone->delete();

        return response()->json(['status'=>200, 'message'=>'Teléfono eliminado.']);
    }

    public function check(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['logged' => false]);
        }

        $u = Auth::user()->loadMissing([
            'addresses' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            'phones'    => fn($q) => $q->orderByDesc('is_default')->orderBy('id'),
        ]);

        return response()->json([
            'logged' => true,
            'user'   => [
                'name'  => $u->name,
                'email' => $u->email,
                'rut'   => $u->rut,
            ],
            'primary_address' => optional($u->primaryAddress())->only(['id','line1','line2','reference','commune_id','region_id','country_id']),
            'primary_phone'   => optional($u->primaryPhone())->only(['id','number']),
        ]);
    }

    public function document(string $uid)
    {
        $order = Order::where('public_uid', $uid)
            ->with(['items','documents'])
            ->where('user_id', Auth::id()) // seguridad: solo dueño
            ->firstOrFail();

        // Toma el documento más reciente (boleta/factura) asociado
        $doc = $order->documents()->latest('id')->first();

        if (!$doc) {
            // Si por algún motivo no existe el registro de documento, responde 404 “amigable”
            abort(404, 'Documento no disponible para esta orden.');
        }

        // Path deseado (mismo esquema que indicaste, pero en public_uploads)
        $path = $doc->pdf_path ?: 'orders/'.$order->id.'/document_'.$doc->type.'_'.$doc->id.'.pdf';

        // Si el archivo no existe en disco, lo generamos ahora
        if (!$doc->pdf_path || !Storage::disk('public_uploads')->exists($path)) {

            // Asegura que options_display sea array al renderizar
            $order->items->transform(function($it){
                if (is_string($it->options_display)) {
                    $it->options_display = json_decode($it->options_display, true) ?: [];
                }
                return $it;
            });

            $pdf = Pdf::loadView('pdf.documents.tax', [
                'order' => $order,
                'doc'   => $doc,
            ]);

            Storage::disk('public_uploads')->put($path, $pdf->output());

            // Actualiza el doc
            $doc->update([
                'pdf_path'  => $path,
                'status'    => $doc->status ?: 'issued',
                'issued_at' => $doc->issued_at ?: now(),
            ]);
        }

        // Entrega el archivo: puedes forzar descarga o abrir en nueva pestaña.
        // a) Abrir en pestaña nueva (redirige a la URL pública del archivo)
        return redirect()->away(Storage::disk('public_uploads')->url($path));

        // b) Si prefieres forzar descarga, usa:
        // return Storage::disk('public_uploads')->download($path, 'comprobante_'.$order->id.'.pdf');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Phone;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use DataTables;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = null;
            if (is_array($request->search) && $request->search['value'] != null) $search = $request->search['value'];
            elseif ($request->search && !is_array($request->search)) $search = $request->search;

            // UserController@index (solo el bloque AJAX)
            $values = User::query()
                ->with([
                    'roles:id,name',
                    // dirección primaria + nombres
                    'addresses' => function($q) {
                        $q->where('is_primary', true)
                        ->with([
                            'country:id,name',
                            'region:id,name',
                            'commune:id,name',
                        ]);
                    },
                    // opcional: teléfono/foto primaria
                    'phones'  => fn($q)=>$q->where('is_default',true),
                    'photos'  => fn($q)=>$q->where('is_primary',true),
                ])
                ->when($search, function($q) use ($search) {
                    $q->where(function($qq) use ($search){
                        $qq->where('name','like',"%$search%")
                        ->orWhere('email','like',"%$search%")
                        ->orWhere('rut','like',"%$search%");
                    });
                })
                ->get();

            return datatables()->of($values)->toJson();

        }

        return view('user.index');
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request, 0);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            // User base
            $data = $request->only(['name','email','rut','password']);
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            // Rol
            $role = Role::whereId($request->input('role_id'))->first();
            if ($role) $user->assignRole($role->name);

            // Teléfono (opcional)
            if ($request->filled('phone_number')) {
                $user->phones()->create([
                    'kind'         => $request->input('phone_kind','mobile'),
                    'country_code' => $request->input('phone_country','+56'),
                    'number'       => $request->input('phone_number'),
                    'is_primary'   => true,
                ]);
            }

            // Dirección (opcional)
            if ($request->filled('addr_line1') || $request->filled('commune_id')) {
                $user->addresses()->create([
                    'line1'        => $request->input('addr_line1'),
                    'line2'        => $request->input('addr_line2'),
                    'reference'    => $request->input('addr_reference'),
                    'country_id'   => $request->input('country_id'),
                    'region_id'    => $request->input('region_id'),
                    'commune_id'   => $request->input('commune_id'),
                    'postal_code'  => $request->input('addr_postal'),
                    'is_primary'   => true,
                ]);
            }

            // Avatar (opcional) - campo "avatar" del FormData
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $path = $file->store('users', 'public_uploads');
                $user->photos()->create([
                    'disk'          => 'public_uploads',
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'is_primary'    => true,
                    'sort_order'    => 0,
                    'title'         => 'Avatar',
                ]);
            }

            DB::commit();
            return response()->json(['status'=>200,'errors'=>[]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function update(Request $request, User $user)
    {
        $validator = $this->validator($request, $user->id);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            $input = $request->only(['name','email','rut','password']);
            if (array_key_exists('password', $input)) {
                if (!$input['password']) unset($input['password']);
                else $input['password'] = Hash::make($input['password']);
            }
            $user->update($input);

            // Rol
            $role = Role::whereId($request->input('role_id'))->first();
            if ($role) $user->syncRoles([$role->name]);

            // Teléfono (update/create)
            if ($request->filled('phone_number')) {
                $phone = $user->phones()->where('is_default', true)->first();
                $dataPhone = [
                    'kind'         => $request->input('phone_kind','mobile'),
                    'country_code' => $request->input('phone_country','+56'),
                    'number'       => $request->input('phone_number'),
                    'is_default'   => true,
                ];
                $phone ? $phone->update($dataPhone) : $user->phones()->create($dataPhone);
            }

            // Dirección (update/create)
            if ($request->filled('addr_line1') || $request->filled('commune_id')) {
                $addr = $user->addresses()->where('is_primary', true)->first();
                $dataAddr = [
                    'line1'        => $request->input('addr_line1'),
                    'line2'        => $request->input('addr_line2'),
                    'reference'    => $request->input('addr_reference'),
                    'country_id'   => $request->input('country_id'),
                    'region_id'    => $request->input('region_id'),
                    'commune_id'   => $request->input('commune_id'),
                    'postal_code'  => $request->input('addr_postal'),
                    'is_primary'   => true,
                ];
                $addr ? $addr->update($dataAddr) : $user->addresses()->create($dataAddr);
            }

            // Avatar (opcional)
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $path = $file->store('users', 'public_uploads');

                // Desmarcar anteriores
                $user->photos()->update(['is_primary' => false]);

                $user->photos()->create([
                    'disk'          => 'public_uploads',
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'is_primary'    => true,
                    'sort_order'    => 0,
                    'title'         => 'Avatar',
                ]);
            }

            DB::commit();
            return response()->json(['status'=>200,'errors'=>[]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $deleted = $user->delete();
        if ($deleted) {
            return response()->json(['status'=>200,'message'=>"Eliminado Correctamente"]);
        }
    }

    public function validator(Request $request, $id)
    {
        $rules = [
            'name'     => ['required', Rule::unique('users','name')->ignore($id)],
            'email'    => ['required','email', Rule::unique('users','email')->ignore($id)],
            'rut'      => ['nullable','max:20', Rule::unique('users','rut')->ignore($id)],
            'role_id'  => ['required'],
            // avatar opcional (cuando venga archivo)
            'avatar'   => ['nullable','image','mimes:jpeg,jpg,png,webp','max:3072'],
        ];
        $rules['password'] = $id==0 ? ['required','min:6'] : ['nullable','min:6'];

        $messages = [
            'name.required'     => 'Debe ingresar Nombre',
            'email.required'    => 'Debe ingresar Email',
            'email.email'       => 'Email tiene formato incorrecto',
            'password.required' => 'Debe ingresar contraseña',
            'password.min'      => 'Contraseña debe contener mínimo 6 caracteres',
            'role_id.required'  => 'Debe ingresar Rol',
            'rut.unique'        => 'RUT ya existe',
            'avatar.image'      => 'El avatar debe ser una imagen válida',
            'avatar.mimes'      => 'Formatos permitidos: jpeg, jpg, png, webp',
            'avatar.max'        => 'El avatar no debe superar 3MB',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}

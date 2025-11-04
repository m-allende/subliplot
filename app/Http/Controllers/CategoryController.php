<?php
// app/Http/Controllers/CategoryController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = null;
            if (is_array($request->search) && $request->search['value'] != null) $search = $request->search['value'];
            elseif ($request->search && !is_array($request->search)) $search = $request->search;

            $values = Category::query()
                ->with(['photos' => fn($q) => $q->where('is_primary', true)])
                ->when($search, function($q) use ($search) {
                    $q->where(function($qq) use ($search){
                        $qq->where('name','like',"%{$search}%");
                    });
                })
                ->get();

            return datatables()->of($values)->toJson();
        }

        return view('category.index');
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request, 0);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            $category = Category::create($request->all());

            // Imagen recortada en base64 desde Croppie (campo "image")
            $this->saveCroppedImageIfAny($category, $request->input('image'));

            DB::commit();
            return response()->json(['status'=>200,'errors'=>$validator->messages()]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function update(Request $request, Category $category)
    {
        $validator = $this->validator($request, $category->id);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            $category->update($request->all());

            // Si viene nueva imagen base64, reemplaza primaria
            $this->saveCroppedImageIfAny($category, $request->input('image'), true);

            DB::commit();
            return response()->json(['status'=>200,'errors'=>$validator->messages()]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function destroy(Category $category)
    {
        $deleted = $category->delete();
        if ($deleted) {
            return response()->json(['status'=>200,'message'=>"Eliminado Correctamente"]);
        }
    }

    private function saveCroppedImageIfAny(Category $category, ?string $base64, bool $replacePrimary = false): void
    {
        if (!$base64) return;

        // si reemplazamos, borrar primaria anterior (registro + archivo)
        if ($replacePrimary) {
            if ($prev = $category->photos()->where('is_primary', true)->first()) {
                // archivo físico (si existía)
                $abs = public_path('uploads/'.ltrim($prev->path,'/'));
                if (is_file($abs)) @unlink($abs);
                $prev->delete();
            }
        }

        // Decodificar base64 (Croppie default "image/png" pero forzamos .jpg para ahorrar)
        $payload = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
        $bin     = base64_decode(str_replace(' ', '+', $payload));
        if (!$bin) return;

        // Guardar en /public/uploads/categories/
        $dir = public_path('uploads/categories');
        if (!File::isDirectory($dir)) File::makeDirectory($dir, 0775, true, true);

        $filename = 'cat_'.date('Ymd_His').'_' . Str::random(6) . '.jpg';
        $relPath  = 'categories/'.$filename;                 // relativo a /public/uploads
        $absPath  = public_path('uploads/'.$relPath);

        file_put_contents($absPath, $bin);

        // Registrar Photo (disk lógico "public_uploads")
        $photo = new Photo();
        $photo->disk       = 'public_uploads';               // si usas ese disco; si no, deja null
        $photo->path       = $relPath;                       // ej: categories/cat_*.jpg
        $photo->is_primary = true;
        $photo->title      = 'Imagen categoría';

        $category->photos()->save($photo);
    }

    private function validator(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'name' => ['required', Rule::unique('categories')->ignore($id)],
        ], [
            'name.required' => 'Debe ingresar Nombre',
        ]);
    }
}

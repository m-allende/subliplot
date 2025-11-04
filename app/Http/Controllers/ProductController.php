<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = null;
            if (is_array($request->search) && $request->search['value'] != null) $search = $request->search['value'];
            elseif ($request->search && !is_array($request->search)) $search = $request->search;

            $values = Product::query()
                ->with(['category:id,name', 'photos'])
                ->when($search, function($q) use ($search){
                    $q->where(function($qq) use ($search){
                        $qq->where('name','like',"%{$search}%")
                           ->orWhere('description','like',"%{$search}%");
                    });
                })
                ->get();

            return datatables()->of($values)->toJson();
        }

        return view('product.index');
    }

    public function store(Request $request)
    {
        $flags = [
            'active','uses_size','uses_paper','uses_bleed','uses_finish',
            'uses_material','uses_shape','uses_print_side','uses_mounting',
            'uses_rolling','uses_holes', 'uses_quantity',
        ];

        foreach ($flags as $f) {
            $request->merge([$f => $request->boolean($f)]);
        }

        $validator = $this->validator($request, 0);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            $product = Product::create($request->all());

            $images = $request->input('images', []); // array base64
            $this->saveImages($product, $images, true);

            DB::commit();
            return response()->json(['status'=>200,'errors'=>$validator->messages()]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function update(Request $request, Product $product)
    {
        
        $flags = [
            'active','uses_size','uses_paper','uses_bleed','uses_finish',
            'uses_material','uses_shape','uses_print_side','uses_mounting',
            'uses_rolling','uses_holes', 'uses_quantity',
        ];

        foreach ($flags as $f) {
            $request->merge([$f => $request->boolean($f)]);
        }

        $validator = $this->validator($request, $product->id);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'errors'=>$validator->messages()]);
        }

        DB::beginTransaction();
        try {
            $product->update($request->all());

            $images = $request->input('images', []);
            $replacePrimary = $request->boolean('replace_primary', false);
            $this->saveImages($product, $images, $replacePrimary);

            DB::commit();
            return response()->json(['status'=>200,'errors'=>$validator->messages()]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>400,'errors'=>$e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        $deleted = $product->delete();
        if ($deleted) {
            return response()->json(['status'=>200,'message'=>"Eliminado Correctamente"]);
        }
    }

    private function validator(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'category_id' => ['required','exists:categories,id'],
            'name'        => ['required', Rule::unique('products')->ignore($id)],
            'active'      => ['nullable','boolean'],
        ],[
            'category_id.required' => 'Debe seleccionar Categoría',
            'category_id.exists'   => 'Categoría inválida',
            'name.required'        => 'Debe ingresar Nombre',
        ]);
    }

    private function saveImages(Product $product, array $images, bool $replacePrimary = false): void
    {
        if (empty($images)) return;

        $dir = public_path('uploads/products');
        if (!File::isDirectory($dir)) File::makeDirectory($dir, 0775, true, true);

        if ($replacePrimary) {
            if ($prev = $product->photos()->where('is_primary', true)->first()) {
                $abs = public_path('uploads/'.ltrim($prev->path,'/'));
                if (is_file($abs)) @unlink($abs);
                $prev->delete();
            }
        }

        foreach ($images as $idx => $base64) {
            $payload = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
            $bin     = base64_decode(str_replace(' ', '+', $payload));
            if (!$bin) continue;

            $filename = 'prod_'.date('Ymd_His').'_' . Str::random(6) . '.jpg';
            $relPath  = 'products/'.$filename;
            $absPath  = public_path('uploads/'.$relPath);

            file_put_contents($absPath, $bin);

            $product->photos()->create([
                'disk'       => 'public_uploads',
                'path'       => $relPath,
                'is_primary' => ($replacePrimary && $idx === 0)
                                ? true
                                : (!$product->photos()->where('is_primary',true)->exists() && $idx===0),
                'title'      => 'Foto producto',
                'sort_order' => $product->photos()->max('sort_order') + 1,
            ]);
        }
    }
}



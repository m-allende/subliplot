<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AttributeType;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductAttributeController extends Controller
{
    /**
     * Devuelve TODOS los grupos de atributos con sus opciones y
     * cuáles están seleccionadas por el producto.
     * Formato JSON:
     * [
     *   { id, code, name_es, options:[{id,name_es}], selected:[ids...] },
     *   ...
     * ]
     */
    public function show(Product $product)
    {
        // Mapear códigos -> flags del producto para fallback
        $codeToFlag = [
            'size'        => 'uses_size',
            'paper'       => 'uses_paper',
            'bleed'       => 'uses_bleed',
            'finish'      => 'uses_finish',
            'material'    => 'uses_material',
            'shape'       => 'uses_shape',
            'print_side'  => 'uses_print_side',
            'mounting'    => 'uses_mounting',
            'rolling'     => 'uses_rolling',
            'holes'       => 'uses_holes',
            'quantity'    => 'uses_quantity',
        ];

        // Tipos + valores activos
        $types = AttributeType::with(['values' => fn($q) => $q->where('active', true)->orderBy('sort_order')])
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        // Settings del producto (clave por type_id)
        $settings = $product->attributeSettings()->get()->keyBy('attribute_type_id');

        // Valores ya asignados
        //$selected = $product->attributeValues()->pluck('attribute_value_id')->all();
        $selected = $product->attributeValues()->allRelatedIds()->all();
        $selectedSet = array_flip($selected);

        $groups = $types->map(function ($type) use ($settings, $product, $codeToFlag, $selected) {
            $st = $settings->get($type->id);

            // enabled: 1) setting; 2) fallback flag del producto por code; 3) false
            $fallbackFlag = $codeToFlag[$type->code] ?? null;
            $enabled = $st ? (bool)$st->enabled : ($fallbackFlag ? (bool)$product->{$fallbackFlag} : false);

            return [
                'id'         => $type->id,
                'code'       => $type->code,
                'name'       => $type->name,                 // visible (ES)
                'enabled'    => $enabled,
                'required'   => (bool)($st->required ?? false),
                'multi'      => (bool)($st->multi_select ?? false),
                'show_as'    => $st->show_as ?? 'select',
                'sort_order' => (int)($st->sort_order ?? 0),
                'options'    => $type->values->map(fn($v) => [
                                    'id'   => $v->id,
                                    'text' => $v->name,      // <- usa 'text' para Select2
                                ])->values(),
                'selected'   => $type->values
                            ->whereIn('id', $selected)
                            ->pluck('id')
                            ->values(),
            ];
        })->sortBy('sort_order')->values();

        return response()->json([
            'status'  => 200,
            'product' => ['id' => $product->id, 'name' => $product->name],
            'groups'  => $groups,
        ]);
    }

    /**
     * Actualiza el conjunto total de AttributeOption asignadas al producto.
     * Espera: options[] = [attribute_option_id,...] (lista aplanada)
     */
    public function update(Request $request, Product $product)
    {
        $v = Validator::make($request->all(), [
            'options'   => ['array'],
            'options.*' => ['integer', Rule::exists('attribute_values','id')],
        ], [
            'options.*.exists' => 'Valor de atributo inválido.',
        ]);

        if ($v->fails()) {
            return response()->json(['status'=>400, 'errors'=>$v->messages()]);
        }

        // Sincroniza todas las selecciones (valor por valor, a nivel del pivot)
        $valueIds = $request->input('options', []);
        $product->attributeValues()->sync($valueIds);

        return response()->json(['status'=>200]);
    }

}

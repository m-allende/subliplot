<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\AttributeType;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class ProductConfigController extends Controller
{
    /**
     * Devuelve los grupos de atributos que aplican al producto
     * con sus opciones (sólo las ASIGNADAS al producto) y las
     * preselecciones por defecto (pivot is_default).
     *
     * Respuesta:
     * {
     *   product: { id, name },
     *   groups: [
     *     {
     *       code, name, multi, placeholder,
     *       options:   [{ id, text }],
     *       selected:  [ids]   // defaults (si no hay defaults, [] )
     *     }, ...
     *   ],
     *   photos: [urls...]
     * }
     */
    public function show(Product $product)
    {
        // Mapa code -> flag del producto (fallback si no hay setting)
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

        // Settings por tipo para este producto (enabled, multi, orden, etc.)
        $settings = $product->attributeSettings()->get()->keyBy('attribute_type_id');

        // Tipos + todos sus valores ACTIVOS (para poder filtrarlos por los asignados)
        $types = AttributeType::with(['values' => fn($q) => $q->where('active', true)->orderBy('sort_order')])
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        // Valores ASIGNADOS al producto (por tipo) + flag de default desde el pivot
        // => agrupamos por attribute_type_id para filtrar por grupo
        $assignedByType = $product->attributeValues()
            ->select('attribute_values.id', 'attribute_values.attribute_type_id', 'product_attribute_values.is_default')
            ->get()
            ->groupBy('attribute_type_id');

        $groups = [];
        foreach ($types as $type) {
            $st = $settings->get($type->id);

            // ¿Este grupo aplica? 1) setting.enabled; 2) fallback flag del producto; 3) false
            $fallbackFlag = $codeToFlag[$type->code] ?? null;
            $enabled = $st ? (bool) $st->enabled
                           : ($fallbackFlag ? (bool) $product->{$fallbackFlag} : false);

            if (! $enabled) {
                continue; // sólo los grupos que aplican al producto
            }

            // IDs asignados a este producto para este tipo (puede estar vacío)
            $assignedForType = $assignedByType->get($type->id) ?? collect();
            $assignedIds     = $assignedForType->pluck('id')->all();

            // Filtramos los valores del tipo dejando SOLO los asignados
            $values = $type->values->filter(fn($v) => in_array($v->id, $assignedIds))->values();

            // Preselecciones = los que en el pivot tengan is_default = true
            $selectedDefaults = $assignedForType
                ->where('is_default', true)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->values();

            $groups[] = [
                'code'        => $type->code,
                'name'        => $type->name,                         // visible (ES)
                'multi'       => (bool) ($st->multi_select ?? false),
                'placeholder' => 'Seleccione ' . $type->name,
                'options'     => $values->map(fn($v) => [
                    'id'   => (string) $v->id,
                    'text' => $v->name,
                ])->values(),
                'selected'    => $selectedDefaults,                   // [] si no hay defaults
            ];
        }

        // URLs de fotos (para el carrusel del modal)
        $photos = $product->photos->map(function ($ph) {
            return $ph->url ?? ($ph->path ? asset($ph->path) : null);
        })->filter()->values();

        return response()->json([
            'product' => ['id' => $product->id, 'name' => $product->name],
            'groups'  => $groups,
            'photos'  => $photos,
        ]);
    }


    public function price(Request $request, Product $product)
    {
        $attributes = [
            'size_id', 'paper_id', 'bleed_id', 'finish_id',
            'material_id', 'shape_id', 'print_side_id',
            'mounting_id', 'rolling_id', 'hole_id', 'quantity_id'
        ];

        $query = ProductPrice::where('product_id', $product->id);

        foreach ($attributes as $attr) {
            if ($request->filled($attr)) {
                $query->where($attr, $request->input($attr));
            } else {
                $query->whereNull($attr);
            }
        }

        $priceRow = $query->first();

        if (!$priceRow || $priceRow->price <= 0) {
            return response()->json(['status' => 404, 'message' => 'No existe precio para esta combinación.']);
        }

        return response()->json([
            'status' => 200,
            'price'  => (int) $priceRow->price,
        ]);
    }


    public function quantities(Product $product, Request $request)
    {
        if (! $product->uses_quantity) {
            return response()->json(['status' => 200, 'data' => []]);
        }

        $quantityType = \App\Models\AttributeType::where('code', 'quantity')->first();
        if (! $quantityType) {
            return response()->json(['status' => 404, 'message' => 'No existe tipo de atributo "quantity"']);
        }

        // Obtener valores asignados a este producto para el tipo "quantity"
        $assignedValues = $product->attributeValues()
            ->where('attribute_type_id', $quantityType->id)
            ->select('attribute_values.id', 'attribute_values.name')
            ->get();

        // Base de consulta de precios
        $attributes = [
            'size_id', 'paper_id', 'bleed_id', 'finish_id',
            'material_id', 'shape_id', 'print_side_id',
            'mounting_id', 'rolling_id', 'hole_id'
        ];

        // Construimos el filtro base con todos los atributos seleccionados
        $filters = ['product_id' => $product->id];
        foreach ($attributes as $attr) {
            $filters[$attr] = $request->filled($attr) ? $request->input($attr) : null;
        }

        // Iteramos sobre cada cantidad y buscamos su precio
        $result = $assignedValues->map(function ($val) use ($filters) {
            $query = \App\Models\ProductPrice::where($filters)
                ->where('quantity_id', $val->id);

            $priceRow = $query->first();
            $price = $priceRow?->price ?? null;

            return [
                'id'    => (string) $val->id,
                'name'  => $val->name . ($price ? ' — $' . number_format($price, 0, ',', '.') : ''),
                'price' => (int) $price,
            ];
        });

        return response()->json(['status' => 200, 'data' => $result]);
    }

}

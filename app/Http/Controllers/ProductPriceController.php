<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductPrice;

class ProductPriceController extends Controller
{
    /**
     * Muestra la vista principal (Blade)
     */
    public function index()
    {
        return view('productprice.index');
    }

    /**
     * Carga todas las combinaciones posibles según los atributos
     * activos del producto seleccionado.
     */
    public function loadCombinations($productId)
    {
        $product = Product::findOrFail($productId);

        // === 1️⃣ Detectar atributos activos según flags ===
        $activeFlags = [
            'size'        => $product->uses_size,
            'paper'       => $product->uses_paper,
            'bleed'       => $product->uses_bleed,
            'finish'      => $product->uses_finish,
            'material'    => $product->uses_material,
            'shape'       => $product->uses_shape,
            'print_side'  => $product->uses_print_side,
            'mounting'    => $product->uses_mounting,
            'rolling'     => $product->uses_rolling,
            'hole'        => $product->uses_holes,
            'quantity'    => $product->uses_quantity,
        ];

        // === 2️⃣ Obtener valores de cada tipo activo ===
        $attributes = [];
        $typeLabels = []; // Aquí guardamos los nombres en español
        foreach ($activeFlags as $code => $enabled) {
            if ($enabled) {
                $values = $product->attributesByType($code);
                if (!empty($values)) {
                    $attributes[$code] = $values;
                    // obtenemos el nombre del tipo desde la tabla attribute_types
                    $type = \App\Models\AttributeType::where('code', $code)->first();
                    $typeLabels[$code] = $type?->name ?? ucfirst($code);
                }
            }
        }

        if (empty($attributes)) {
            return response()->json([
                'status' => 204,
                'message' => 'El producto no tiene atributos configurados.'
            ]);
        }

        // === 3️⃣ Generar combinaciones ===
        $keys   = array_keys($attributes);
        $arrays = array_values($attributes);
        $combinations = $this->combineAttributes($arrays);

        // === 4️⃣ Cargar precios existentes ===
        $existing = ProductPrice::where('product_id', $productId)->get();

        // === 5️⃣ Mapear filas para el front ===
        $rows = [];
        foreach ($combinations as $combo) {
            $row = ['product_id' => $productId, 'price' => 0];

            foreach ($keys as $i => $key) {
                $row["{$key}_id"]   = $combo[$i]['id'];
                $row["{$key}_name"] = $combo[$i]['name'];
            }

            // Buscar si existe precio registrado
            $match = $existing->first(function ($p) use ($row, $keys) {
                foreach ($keys as $key) {
                    $col = "{$key}_id";
                    if (!is_null($p->$col) && $p->$col != ($row[$col] ?? null)) {
                        return false;
                    }
                }
                return true;
            });

            if ($match) $row['price'] = $match->price;
            $rows[] = $row;
        }

        return response()->json([
            'status' => 200,
            'data' => $rows,
            'labels' => $typeLabels  // 👈 enviamos los títulos amigables al front
        ]);
    }

    /**
     * Combina múltiples arrays en todas las permutaciones posibles.
     */
    private function combineAttributes(array $arrays): array
    {
        if (empty($arrays)) return [];
        $result = [[]];
        foreach ($arrays as $property) {
            $temp = [];
            foreach ($result as $res) {
                foreach ($property as $item) {
                    $temp[] = array_merge($res, [$item]);
                }
            }
            $result = $temp;
        }
        return $result;
    }

    /**
     * Guarda o actualiza todos los precios enviados desde el front.
     */
    public function store(Request $request)
    {
        $rows = $request->input('rows', []);

        if (empty($rows)) {
            return response()->json(['status' => 400, 'message' => 'No se recibieron filas.']);
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $price = (int) round($row['price'] ?? 0);

                $data = [
                    'product_id' => $row['product_id'],
                    'price'      => $price,
                ];

                $attributes = [
                    'size_id', 'paper_id', 'bleed_id', 'finish_id',
                    'material_id', 'shape_id', 'print_side_id',
                    'mounting_id', 'rolling_id', 'hole_id', 'quantity_id',
                ];

                foreach ($attributes as $attr) {
                    if (array_key_exists($attr, $row)) {
                        $data[$attr] = $row[$attr] ?: null;
                    }
                }

                $match = ['product_id' => $row['product_id']];
                foreach ($attributes as $attr) {
                    if (isset($data[$attr])) {
                        $match[$attr] = $data[$attr];
                    }
                }

                ProductPrice::updateOrCreate($match, ['price' => $data['price']]);
            }
        });

        return response()->json(['status' => 200, 'message' => 'Precios guardados correctamente.']);
    }

}

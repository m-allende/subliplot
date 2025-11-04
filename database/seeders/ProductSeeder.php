<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('products')->count() > 0) {
            $this->command?->info('products ya tiene datos — se omite ProductSeeder.');
            return;
        }

        $now  = Carbon::now();
        $cats = DB::table('categories')->pluck('id','name'); // ['Papelería'=>1, ...]

        // --- Helper: flags pseudo-aleatorios pero determinísticos por nombre ---
        $randFlags = function(string $name): array {
            // genera un número de 0..255 en base al nombre
            $n = hexdec(substr(md5($name), 0, 2));
            return [
                'uses_size'        => (bool)($n & 1),
                'uses_paper'       => (bool)($n & 2),
                'uses_bleed'       => (bool)($n & 4),
                'uses_finish'      => (bool)($n & 8),
                'uses_material'    => (bool)($n & 16),
                'uses_shape'       => (bool)($n & 32),
                'uses_print_side'  => (bool)($n & 64),
                'uses_mounting'    => (bool)($n & 128),
                // extras menos comunes: los determinamos con otros bits del hash
                'uses_rolling'     => (bool)(hexdec(substr(md5('r'.$name),0,2)) & 1),
                'uses_holes'       => (bool)(hexdec(substr(md5('h'.$name),0,2)) & 1),
            ];
        };

        // --- Helper: compone un row con TODAS las columnas ---
        $makeRow = function(array $item) use ($cats, $now, $randFlags) {
            if (empty($item['category_name']) || empty($item['name'])) return null;
            $catName = $item['category_name'];
            if (!isset($cats[$catName])) return null;

            $flags = $randFlags($item['name']); // flags determinísticos
            // Permite forzar flags en el array de entrada si quisieras
            foreach ($flags as $k => $v) {
                if (array_key_exists($k, $item)) $flags[$k] = (bool)$item[$k];
            }

            return [
                'category_id'     => $cats[$catName],
                'name'            => $item['name'],
                'subtitle'        => $item['subtitle']   ?? null,
                'description'     => $item['description'] ?? null,

                'uses_size'       => $flags['uses_size'],
                'uses_paper'      => $flags['uses_paper'],
                'uses_bleed'      => $flags['uses_bleed'],
                'uses_finish'     => $flags['uses_finish'],
                'uses_material'   => $flags['uses_material'],
                'uses_shape'      => $flags['uses_shape'],
                'uses_print_side' => $flags['uses_print_side'],
                'uses_mounting'   => $flags['uses_mounting'],
                'uses_rolling'    => $flags['uses_rolling'],
                'uses_holes'      => $flags['uses_holes'],

                'active'          => true,
                'sort_order'      => (int)($item['sort_order'] ?? 0),
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        };

        // -------- LISTA COMPLETA (según imagen) --------
        $items = [
            // PAPELERÍA
            ['category_name'=>'Papelería','name'=>'Tarjetas de presentación','sort_order'=>1],
            ['category_name'=>'Papelería','name'=>'Flyers','sort_order'=>2],
            ['category_name'=>'Papelería','name'=>'Agendas','sort_order'=>3],
            ['category_name'=>'Papelería','name'=>'Diplomas','sort_order'=>4],
            ['category_name'=>'Papelería','name'=>'Tarjetones','sort_order'=>5],
            ['category_name'=>'Papelería','name'=>'Impresiones simples','sort_order'=>6],

            // STICKERS
            ['category_name'=>'Stickers','name'=>'Cuadrado','sort_order'=>1],
            ['category_name'=>'Stickers','name'=>'Rectangular','sort_order'=>2],
            ['category_name'=>'Stickers','name'=>'Irregulares','sort_order'=>3],
            ['category_name'=>'Stickers','name'=>'Circulares','sort_order'=>4],

            // GRÁFICA PUBLICITARIA
            ['category_name'=>'Gráfica Publicitaria','name'=>'Adhesivo de muro','sort_order'=>1],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Adhesivo de piso','sort_order'=>2],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Frosted (empavonado)','sort_order'=>3],
            ['category_name'=>'Gráfica Publicitaria','name'=>'W. vision','sort_order'=>4],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Tela PVC','sort_order'=>5],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Tela Canvas','sort_order'=>6],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Film Backlight','sort_order'=>7],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Adhesivo vehicular','sort_order'=>8],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Malla Mesh','sort_order'=>9],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Adhesivo Blackout','sort_order'=>10],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Adhesivo transparente','sort_order'=>11],
            ['category_name'=>'Gráfica Publicitaria','name'=>'Latex','sort_order'=>12],

            // PENDONES
            ['category_name'=>'Pendones','name'=>'Paloma publicitaria','sort_order'=>1],
            ['category_name'=>'Pendones','name'=>'Bastidores','sort_order'=>2],
            ['category_name'=>'Pendones','name'=>'Pendones Roller','sort_order'=>3],
            ['category_name'=>'Pendones','name'=>'Pendones MINI roller','sort_order'=>4],
            ['category_name'=>'Pendones','name'=>'Pendones X','sort_order'=>5],
            ['category_name'=>'Pendones','name'=>'Pendón T','sort_order'=>6],

            // SUBLIMACIÓN
            ['category_name'=>'Sublimación','name'=>'Tazas','sort_order'=>1],
            ['category_name'=>'Sublimación','name'=>'Choperos','sort_order'=>2],
            ['category_name'=>'Sublimación','name'=>'Mousepad','sort_order'=>3],
            ['category_name'=>'Sublimación','name'=>'Lanyard','sort_order'=>4],
            ['category_name'=>'Sublimación','name'=>'Acrílico','sort_order'=>5],

            // TEXTIL
            ['category_name'=>'Textil','name'=>'DTF','sort_order'=>1],

            // SEÑALÉTICAS
            ['category_name'=>'Señalética','name'=>'DTF','sort_order'=>1],
        ];

        $rows = [];
        foreach ($items as $it) {
            if ($row = $makeRow($it)) $rows[] = $row;
        }

        if (!empty($rows)) {
            DB::table('products')->insert($rows);
            $this->command?->info('ProductSeeder: productos base insertados ('.count($rows).').');
        } else {
            $this->command?->warn('ProductSeeder: no se insertó nada (faltan categorías).');
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        // Si ya hay datos de valores, no corre.
        if (DB::table('attribute_values')->count() > 0) {
            $this->command?->info('attribute_values ya tiene datos. Seeder omitido.');
            return;
        }

        $now = now();
        $typeId = fn(string $code) => DB::table('attribute_types')->where('code',$code)->value('id');

        $sizeId = $typeId('size');
        $paperId = $typeId('paper');
        $bleedId = $typeId('bleed');
        $finishId = $typeId('finish');
        $materialId = $typeId('material');
        $shapeId = $typeId('shape');
        $sideId = $typeId('print_side');
        $mountId = $typeId('mounting');
        $rollId = $typeId('rolling');
        $holesId = $typeId('holes');

        $sizes = [
            ['name'=>'9×5 cm (Tarjeta)', 'code'=>'9x5cm', 'width_mm'=>90, 'height_mm'=>50],
            ['name'=>'A6 (105×148 mm)',  'code'=>'A6',    'width_mm'=>105, 'height_mm'=>148],
            ['name'=>'A5 (148×210 mm)',  'code'=>'A5',    'width_mm'=>148, 'height_mm'=>210],
            ['name'=>'A4 (210×297 mm)',  'code'=>'A4',    'width_mm'=>210, 'height_mm'=>297],
            ['name'=>'A3 (297×420 mm)',  'code'=>'A3',    'width_mm'=>297, 'height_mm'=>420],
        ];
        $papers = [
            ['name'=>'Couché 150 g', 'code'=>'couche_150', 'weight_gsm'=>150],
            ['name'=>'Couché 300 g', 'code'=>'couche_300', 'weight_gsm'=>300],
            ['name'=>'Opalina 240 g','code'=>'opalina_240','weight_gsm'=>240],
            ['name'=>'Bond 100 g',   'code'=>'bond_100',   'weight_gsm'=>100],
        ];
        $bleeds = [
            ['name'=>'Con sangrado', 'code'=>'with_bleed'],
            ['name'=>'Sin sangrado', 'code'=>'no_bleed'],
        ];
        $finishes = [
            ['name'=>'Laminado Mate',       'code'=>'lam_matte'],
            ['name'=>'Laminado Brillante',  'code'=>'lam_gloss'],
            ['name'=>'Sin laminado',        'code'=>'no_lam'],
            ['name'=>'Esquinas redondeadas','code'=>'round_corners'],
        ];
        $materials = [
            ['name'=>'PVC espumado 3 mm',   'code'=>'pvc_3mm'],
            ['name'=>'Acrílico 3 mm',       'code'=>'acrylic_3mm'],
            ['name'=>'Lona Frontlite 13 oz','code'=>'frontlite_13oz'],
            ['name'=>'Vinilo adhesivo',     'code'=>'vinyl_adh'],
            ['name'=>'Tela PVC',            'code'=>'pvc_fabric'],
            ['name'=>'Malla Mesh',          'code'=>'mesh'],
            ['name'=>'Backlight Film',      'code'=>'backlight_film'],
            ['name'=>'Frosted (empavonado)','code'=>'frosted'],
        ];
        $shapes = [
            ['name'=>'Cuadrado',      'code'=>'square'],
            ['name'=>'Rectangular',   'code'=>'rect'],
            ['name'=>'Circular',      'code'=>'circle'],
            ['name'=>'Irregular (troquel)','code'=>'diecut'],
        ];
        $sides = [
            ['name'=>'Una cara',  'code'=>'single'],
            ['name'=>'Doble cara','code'=>'double'],
        ];
        $mountings = [
            ['name'=>'Bastidor madera', 'code'=>'frame_wood'],
            ['name'=>'Autoadhesivo muro','code'=>'adh_wall'],
            ['name'=>'Imán',            'code'=>'magnet'],
            ['name'=>'Sin montaje',     'code'=>'no_mount'],
        ];
        $rollings = [
            ['name'=>'Pendón Roller',      'code'=>'roller'],
            ['name'=>'Pendón Mini Roller', 'code'=>'mini_roller'],
            ['name'=>'Sin roller',         'code'=>'no_roller'],
            ['name'=>'Tubo/cartón envío',  'code'=>'tube'],
        ];
        $holes = [
            ['name'=>'Sin perforaciones',     'code'=>'holes_0'],
            ['name'=>'2 perforaciones',       'code'=>'holes_2'],
            ['name'=>'4 perforaciones',       'code'=>'holes_4'],
            ['name'=>'Ojetillos cada 50 cm',  'code'=>'grommets_50cm'],
        ];

        $insert = function (?int $typeId, array $rows) use ($now) {
            if (!$typeId) return;
            foreach ($rows as $i => $r) {
                DB::table('attribute_values')->insert([
                    'attribute_type_id' => $typeId,
                    'name'        => $r['name'],
                    'code'        => $r['code'] ?? null,
                    'width_mm'    => $r['width_mm']    ?? null,
                    'height_mm'   => $r['height_mm']   ?? null,
                    'weight_gsm'  => $r['weight_gsm']  ?? null,
                    'color_hex'   => $r['color_hex']   ?? null,
                    'extra_json'  => $r['extra_json']  ?? null,
                    'sort_order'  => $i + 1,
                    'active'      => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        };

        $insert($sizeId,     $sizes);
        $insert($paperId,    $papers);
        $insert($bleedId,    $bleeds);
        $insert($finishId,   $finishes);
        $insert($materialId, $materials);
        $insert($shapeId,    $shapes);
        $insert($sideId,     $sides);
        $insert($mountId,    $mountings);
        $insert($rollId,     $rollings);
        $insert($holesId,    $holes);

        $this->command?->info('attribute_values sembrado OK.');
    }
}

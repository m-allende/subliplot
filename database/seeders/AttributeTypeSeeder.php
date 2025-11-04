<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Si la tabla ya tiene registros, no hace nada.
        if (DB::table('attribute_types')->count() > 0) {
            $this->command?->info('attribute_types ya tiene datos. Seeder omitido.');
            return;
        }

        $now = now();

        $types = [
            ['code'=>'size',        'name'=>'Tamaño',              'description'=>'Medidas y formatos',               'sort_order'=>10],
            ['code'=>'paper',       'name'=>'Tipo de papel',       'description'=>'Gramajes y sustratos de papel',    'sort_order'=>20],
            ['code'=>'bleed',       'name'=>'Sangrado',            'description'=>'Corte excedente / sangrado',       'sort_order'=>30],
            ['code'=>'finish',      'name'=>'Terminación',         'description'=>'Acabados y laminados',             'sort_order'=>40],
            ['code'=>'material',    'name'=>'Material',            'description'=>'PVC, acrílico, lonas, vinilos',    'sort_order'=>50],
            ['code'=>'shape',       'name'=>'Forma',               'description'=>'Corte/contorno',                   'sort_order'=>60],
            ['code'=>'print_side',  'name'=>'Caras de impresión',  'description'=>'Simple o doble cara',              'sort_order'=>70],
            ['code'=>'mounting',    'name'=>'Montaje',             'description'=>'Bastidores, adhesivos, etc.',      'sort_order'=>80],
            ['code'=>'rolling',     'name'=>'Enrollado',           'description'=>'Roller, mini-roller, tubos',       'sort_order'=>90],
            ['code'=>'holes',       'name'=>'Perforaciones',       'description'=>'Cantidad/posición de perforaciones','sort_order'=>100],
        ];

        foreach ($types as $t) {
            DB::table('attribute_types')->insert([
                'code'        => $t['code'],
                'name'        => $t['name'],
                'description' => $t['description'],
                'sort_order'  => $t['sort_order'],
                'active'      => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $this->command?->info('attribute_types sembrado OK.');
    }
}

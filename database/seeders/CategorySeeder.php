<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Si ya hay datos, no resembrar
        if (DB::table('categories')->count() > 0) {
            $this->command?->info('categories ya tiene datos — se omite CategorySeeder.');
            return;
        }

        $now = now();

        $data = [
            // sort_order define el orden que verás en el storefront
            ['name' => 'Papelería',           'description' => 'Tarjetas, flyers, afiches, diplomas, agendas, tarjetones, impresiones simples.', 'sort_order' => 1, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Stickers',            'description' => 'Cuadrados, rectangulares, circulares, irregulares; troquel y rollos.',          'sort_order' => 2, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Gráfica Publicitaria','description' => 'Muro/piso, frosted, one way vision, backlight, PVC/canvas, blackout, mesh.',   'sort_order' => 3, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Pendones',            'description' => 'Roller, mini roller, X, T, palomas publicitarias y bastidores.',                'sort_order' => 4, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Sublimación',         'description' => 'Tazas, choperos, mousepads, lanyards, acrílico y más.',                        'sort_order' => 5, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Textil',              'description' => 'Impresión textil (DTF) para poleras, buzos y otros.',                           'sort_order' => 6, 'created_at'=>$now,'updated_at'=>$now],
            ['name' => 'Señalética',          'description' => 'Señaléticas en PVC, acrílico, aluminio para interior/exterior.',                'sort_order' => 7, 'created_at'=>$now,'updated_at'=>$now],
        ];

        DB::table('categories')->insert($data);

        $this->command?->info('CategorySeeder: categorías base insertadas.');
    }
}

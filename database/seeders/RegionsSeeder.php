<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RegionsSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::firstWhere('iso2', 'CL');
        if (!$country) {
            $this->command?->warn('RegionsSeeder: Chile no existe. Ejecuta CountriesSeeder primero.');
            return;
        }

        // Si ya hay regiones asociadas a Chile, no hacemos nada
        if (Region::where('country_id', $country->id)->exists()) {
            $this->command?->info('RegionsSeeder: Regiones para CL ya existen. Saltando…');
            return;
        }

        $now = Carbon::now();

        $regions = [
            ['code' => 'XV',  'name' => 'Arica y Parinacota',                       'ordinal' => 1],
            ['code' => 'I',   'name' => 'Tarapacá',                                  'ordinal' => 2],
            ['code' => 'II',  'name' => 'Antofagasta',                               'ordinal' => 3],
            ['code' => 'III', 'name' => 'Atacama',                                   'ordinal' => 4],
            ['code' => 'IV',  'name' => 'Coquimbo',                                  'ordinal' => 5],
            ['code' => 'V',   'name' => 'Valparaíso',                                'ordinal' => 6],
            ['code' => 'RM',  'name' => 'Región Metropolitana de Santiago',          'ordinal' => 7],
            ['code' => 'VI',  'name' => "Libertador General Bernardo O'Higgins",     'ordinal' => 8],
            ['code' => 'VII', 'name' => 'Maule',                                     'ordinal' => 9],
            ['code' => 'XVI', 'name' => 'Ñuble',                                     'ordinal' => 10],
            ['code' => 'VIII','name' => 'Biobío',                                    'ordinal' => 11],
            ['code' => 'IX',  'name' => 'La Araucanía',                              'ordinal' => 12],
            ['code' => 'XIV', 'name' => 'Los Ríos',                                  'ordinal' => 13],
            ['code' => 'X',   'name' => 'Los Lagos',                                 'ordinal' => 14],
            ['code' => 'XI',  'name' => 'Aysén del General Carlos Ibáñez del Campo', 'ordinal' => 15],
            ['code' => 'XII', 'name' => 'Magallanes y de la Antártica Chilena',      'ordinal' => 16],
        ];

        // Insert masivo (rápido) con timestamps
        Region::insert(array_map(function ($r) use ($country, $now) {
            return [
                'country_id' => $country->id,
                'code'       => $r['code'],
                'name'       => $r['name'],
                'ordinal'    => $r['ordinal'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $regions));

        $this->command?->info('RegionsSeeder: Regiones de Chile insertadas.');
    }
}

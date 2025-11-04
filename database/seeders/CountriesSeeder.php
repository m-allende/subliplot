<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        // Si ya existe Chile, no hacemos nada
        if (Country::where('iso2', 'CL')->exists()) {
            $this->command?->info('CountriesSeeder: Chile ya existe. Saltando…');
            return;
        }

        Country::create([
            'name'       => 'Chile',
            'iso2'       => 'CL',
            'iso3'       => 'CHL',
            'phone_code' => '+56',
            'active'     => true,
        ]);

        $this->command?->info('CountriesSeeder: Chile insertado.');
    }
}

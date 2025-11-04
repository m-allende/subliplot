<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- import it at the top
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AdminUserSeeder::class,
            CountriesSeeder::class,
            RegionsSeeder::class,
            CommunesSeeder::class,
            
            CategorySeeder::class,
            ProductSeeder::class,

            AttributeTypeSeeder::class,
            AttributeValueSeeder::class,
        ]);
    }

}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'description' => 'admin']);
        Role::firstOrCreate(['name' => 'buyer', 'description' => 'comprador']);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'alejandro@subliplot.cl'],
            ['name' => 'Admin', 'password' => bcrypt('admin1234')]
        );

        $user->syncRoles(['admin']); // asegúrate que exista por RolesSeeder
    }
}

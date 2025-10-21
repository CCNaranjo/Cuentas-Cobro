<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //LLamar seeders
        $this->call([
            ModulosSeeder::class,
            PermisosSeeder::class,
            RolesSeeder::class,
            AdminGlobalSeeder::class,
            AlcaldiaEjemploSeeder::class,
            ContratoSeeder::class,
        ]);
    }
}

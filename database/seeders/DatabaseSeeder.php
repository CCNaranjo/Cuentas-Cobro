<?php

namespace Database\Seeders;

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
            AdminOrganizacionSeeder::class,
            ContratoSeeder::class,
            CuentaCobroSeeder::class,
        ]);
    }
}

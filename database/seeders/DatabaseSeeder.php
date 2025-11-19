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
            BancosSeeder::class,
            AlcaldiaEjemploSeeder::class,
            AdminOrganizacionSeeder::class,
            UsuariosOrganizacionSeeder::class,
            ContratoSeeder::class,
            CuentaCobroSeeder::class,
            ParametrosSistemaSeeder::class,
            OrganizacionConfiguracionSeeder::class,
            DatosFinancierosContratistaSeeder::class,
            CuentasBancariasOrgSeeder::class,
            OrdenesPagoSeeder::class,
        ]);
    }
}

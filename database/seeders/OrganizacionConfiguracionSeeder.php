<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\OrganizacionConfiguracion;

class OrganizacionConfiguracionSeeder extends Seeder
{
    /**
     * Crear configuración para organizaciones existentes sin configuración
     */
    public function run(): void
    {
        // Obtener organizaciones sin configuración
        $organizaciones = Organizacion::doesntHave('configuracion')->get();
        
        foreach ($organizaciones as $organizacion) {
            $this->crearConfiguracionDefecto($organizacion->id);
        }
        
        $this->command->info("Configuración creada para {$organizaciones->count()} organización(es)");
    }
    
    /**
     * Crear configuración por defecto para una organización
     */
    public function crearConfiguracionDefecto(int $organizacionId)
    {
        OrganizacionConfiguracion::create([
            'organizacion_id' => $organizacionId,
            
            // Branding - valores por defecto
            'logo_url' => null,
            'color_principal_hex' => '#004AAD',
            'nombre_dominio_local' => null,
            
            // Legal/Fiscal - debe ser configurado por el admin
            'nit_organizacion' => null,
            'direccion_principal' => null,
            'vigencia_fiscal_fecha_inicio' => date('Y') . '-01-01',
            'vigencia_fiscal_fecha_fin' => date('Y') . '-12-31',
            
            // Financiero Local - valores estándar de Colombia
            'porcentaje_retencion_local' => null, // Si es null, usa la global
            'porcentaje_retencion_ica' => 0.966, // 0.966% estándar
            'porcentaje_retencion_fuente' => 11.00, // 11% estándar
            'umbral_validacion_doble_monto' => 10000000, // $10 millones
            'dias_plazo_pago' => 30, // 30 días calendario
            
            // Contactos - debe ser configurado
            'correo_notificacion_tesoreria' => null,
            'contacto_facturacion_nombre' => null,
            'contacto_facturacion_email' => null,
            'contacto_facturacion_telefono' => null,
            
            // Flujo de Trabajo - conservador por defecto
            'requerir_paz_salvo_contratistas' => false,
            'habilitar_aprobacion_multiple' => false,
        ]);
    }
}
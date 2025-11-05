<?php

namespace App\Observers;

use App\Models\Organizacion;
use App\Models\OrganizacionConfiguracion;

class OrganizacionObserver
{
    /**
     * Handle the Organizacion "created" event.
     * Crea la configuración por defecto cuando se crea una organización
     */
    public function created(Organizacion $organizacion): void
    {
        OrganizacionConfiguracion::create([
            'organizacion_id' => $organizacion->id,
            
            // Branding
            'logo_url' => null,
            'color_principal_hex' => '#004AAD',
            'nombre_dominio_local' => null,
            
            // Legal/Fiscal
            'nit_organizacion' => $organizacion->nit ?? null,
            'direccion_principal' => $organizacion->direccion ?? null,
            'vigencia_fiscal_fecha_inicio' => date('Y') . '-01-01',
            'vigencia_fiscal_fecha_fin' => date('Y') . '-12-31',
            
            // Financiero Local - valores estándar
            'porcentaje_retencion_local' => null,
            'porcentaje_retencion_ica' => 0.966,
            'porcentaje_retencion_fuente' => 11.00,
            'umbral_validacion_doble_monto' => 10000000,
            'dias_plazo_pago' => 30,
            
            // Contactos
            'correo_notificacion_tesoreria' => null,
            'contacto_facturacion_nombre' => null,
            'contacto_facturacion_email' => null,
            'contacto_facturacion_telefono' => null,
            
            // Flujo de Trabajo
            'requerir_paz_salvo_contratistas' => false,
            'habilitar_aprobacion_multiple' => false,
        ]);
    }

    /**
     * Handle the Organizacion "updated" event.
     * Limpia la caché cuando se actualiza una organización
     */
    public function updated(Organizacion $organizacion): void
    {
        OrganizacionConfiguracion::limpiarCache($organizacion->id);
    }

    /**
     * Handle the Organizacion "deleted" event.
     * Limpia la caché cuando se elimina una organización
     */
    public function deleted(Organizacion $organizacion): void
    {
        OrganizacionConfiguracion::limpiarCache($organizacion->id);
    }
}
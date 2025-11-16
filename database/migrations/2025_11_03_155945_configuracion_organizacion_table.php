<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('organizacion_configuracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->unique()->constrained('organizaciones')->onDelete('cascade');

            // ============ PERSONALIZACIÓN (BRANDING) ============
            $table->string('logo_url')->nullable()->comment('URL del logo oficial de la Alcaldía');
            $table->string('color_principal_hex', 7)->default('#004AAD')->comment('Color principal de la interfaz');
            $table->string('nombre_dominio_local')->nullable()->comment('Dominio personalizado (e.g., alcaldiax.arca-d.com)');

            // ============ INFORMACIÓN LEGAL/FISCAL ============
            $table->string('nit_organizacion', 50)->nullable()->comment('NIT de la Alcaldía');
            $table->string('direccion_principal')->nullable()->comment('Dirección oficial de contacto');
            $table->date('vigencia_fiscal_fecha_inicio')->nullable()->comment('Inicio del año fiscal');
            $table->date('vigencia_fiscal_fecha_fin')->nullable()->comment('Fin del año fiscal');

            // ============ REGLAS FINANCIERAS LOCALES ============
            $table->decimal('porcentaje_retencion_local', 5, 2)->nullable()->comment('Tasa de retención local (anula la global) (%)');
            $table->decimal('porcentaje_retencion_ica', 5, 3)->default(0.966)->comment('Retención ICA (%)');
            $table->decimal('porcentaje_retencion_fuente', 5, 2)->default(11.00)->comment('Retención en la fuente (%)');
            $table->decimal('umbral_validacion_doble_monto', 15, 2)->default(10000000)->comment('Monto para doble aprobación');
            $table->integer('dias_plazo_pago')->default(30)->comment('Días de plazo para realizar pagos');

            // ============ CONTACTOS Y NOTIFICACIÓN ============
            $table->string('correo_notificacion_tesoreria')->nullable()->comment('Correos de Tesorería (separados por coma)');
            $table->string('contacto_facturacion_nombre')->nullable()->comment('Nombre encargado de facturas');
            $table->string('contacto_facturacion_email')->nullable()->comment('Email encargado de facturas');
            $table->string('contacto_facturacion_telefono', 20)->nullable()->comment('Teléfono encargado de facturas');

            // ============ FLUJO DE TRABAJO LOCAL ============
            $table->boolean('requerir_paz_salvo_contratistas')->default(false)->comment('Exigir paz y salvo antes de radicación');
            $table->boolean('habilitar_aprobacion_multiple')->default(false)->comment('Requerir aprobación de múltiples supervisores');

            $table->timestamps();

            // Índices
            $table->index('organizacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizacion_configuracion');
    }
};

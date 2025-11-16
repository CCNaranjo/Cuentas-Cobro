<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_cobro', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->string('numero_cuenta_cobro')->unique();
            
            $table->date('fecha_radicacion')->nullable();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            
            $table->decimal('valor_bruto', 18, 2)->default(0);
            $table->json('retenciones_calculadas')->nullable();
            $table->decimal('valor_neto', 18, 2)->default(0);
            
            $table->boolean('pila_verificada')->default(false);
            
            // FLUJO COMPLETO DE ESTADOS (Real para Alcaldías)
            $table->enum('estado', [
                'borrador',
                'radicada',
                'en_correccion_supervisor',
                'certificado_supervisor',
                'en_correccion_contratacion',
                'verificado_contratacion',
                'verificado_presupuesto',
                'aprobada_ordenador',
                'en_proceso_pago',
                'pagada',
                'anulada'
            ])->default('borrador');
            
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->constrained('usuarios');
            
            // Campos de pago final
            $table->date('fecha_pago_real')->nullable();
            $table->string('numero_comprobante_pago')->nullable();
            
            $table->timestamps();
            
            $table->index(['contrato_id', 'estado']);
            $table->index('fecha_radicacion');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};


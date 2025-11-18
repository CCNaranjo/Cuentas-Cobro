<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('ordenes_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->string('numero_op')->unique();
            
            // Vínculo con el origen del dinero
            $table->foreignId('cuenta_origen_id')->constrained('cuentas_bancarias_org');
            
            $table->decimal('valor_total_neto', 15, 2);
            $table->date('fecha_emision');
            
            // Auditoría de aprobación y ejecución
            $table->boolean('aprobada_por_ordenador')->default(false);
            $table->foreignId('ordenador_id')->nullable()->constrained('usuarios');
            
            $table->enum('estado', [
                'creada',               // Tesorero generó la OP
                'autorizada',           // Ordenador del Gasto aprobó
                'pagada_registrada',    // Tesorero confirmó el pago manual
                'anulada'
            ])->default('creada');
            
            $table->foreignId('created_by')->constrained('usuarios'); // Tesorero que inicia la OP
            $table->timestamps();
        });

        Schema::create('op_cuentas_cobro', function (Blueprint $table) {
            $table->foreignId('orden_pago_id')->constrained('ordenes_pago')->onDelete('cascade');
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            
            $table->primary(['orden_pago_id', 'cuenta_cobro_id']);
            
            // Campos que capturan el detalle del pago final para la CC específica
            $table->date('fecha_pago_efectivo')->nullable();
            $table->string('comprobante_bancario_id')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago');
        Schema::dropIfExists('op_cuentas_cobro');
    }
};

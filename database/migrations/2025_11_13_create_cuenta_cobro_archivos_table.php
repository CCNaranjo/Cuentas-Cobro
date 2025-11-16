<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuenta_cobro_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->foreignId('subido_por')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombre_original');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('tipo_archivo', 10);
            $table->string('mime_type', 100);
            $table->bigInteger('tamaño');
            $table->enum('tipo_documento', [
                'cuenta_cobro',
                'acta_recibido',
                'informe',
                'foto_evidencia',
                'planilla',
                'soporte_pago',
                'factura',
                'otro'
            ])->default('cuenta_cobro');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->index(['cuenta_cobro_id', 'tipo_documento']);
            $table->index('cuenta_cobro_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobro_archivos');
    }
};

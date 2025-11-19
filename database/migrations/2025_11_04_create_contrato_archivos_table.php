<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->foreignId('subido_por')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombre_original');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('tipo_archivo', 10);
            $table->string('mime_type', 100);
            $table->bigInteger('tamaño');
            $table->enum('tipo_documento', [
                'contrato_firmado',
                'acta_inicio', 
                'cronograma',           // ← Agregar este
                'presupuesto',          // ← Agregar este
                'estudio_previo',
                'certificacion_presupuestal',
                'cdp',
                'acta_liquidacion',
                'otro'
            ])->default('otro');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->index(['contrato_id', 'tipo_documento']);
            $table->index('contrato_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_archivos');
    }
};
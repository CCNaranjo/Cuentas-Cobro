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
        Schema::create('contrato_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            $table->foreignId('subido_por')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombre_original');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('tipo_archivo', 10);
            $table->string('mime_type', 100);
            $table->bigInteger('tamaño'); // Nota: sin tilde en el código
            $table->enum('tipo_documento', [
                'contrato_firmado',
                'adicion',
                'suspension',
                'acta_inicio',
                'acta_liquidacion',
                'otro'
            ])->default('contrato_firmado');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->index(['contrato_id', 'tipo_documento']);
            $table->index('contrato_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_archivos');
    }
};

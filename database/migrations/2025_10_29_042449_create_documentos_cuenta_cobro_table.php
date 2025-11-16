<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('documentos_soporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->enum('tipo_documento', [
                'acta_recibido',
                'informe',
                'foto_evidencia',
                'planilla',
                'otro'
            ]);
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->integer('tamano_kb');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->index('cuenta_cobro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_soporte');
    }
};

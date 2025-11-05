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
            $table->date('fecha_radicacion');
            $table->string('periodo_cobrado')->nullable();
            $table->decimal('valor_bruto', 15, 2);
            $table->json('retenciones_calculadas')->nullable();
            $table->decimal('valor_neto', 15, 2);
            $table->enum('estado', [
                'borrador',
                'radicada',
                'en_revision',
                'aprobada',
                'rechazada',
                'pagada',
                'anulada'
            ])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('numero_cuenta_cobro');
            $table->index(['contrato_id', 'estado']);
            $table->index('fecha_radicacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};


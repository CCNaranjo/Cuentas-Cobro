<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_cuenta_cobro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->text('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('valor_unitario', 15, 2);
            $table->decimal('valor_total', 15, 2);
            $table->decimal('porcentaje_avance', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index('cuenta_cobro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_cuenta_cobro');
    }
};

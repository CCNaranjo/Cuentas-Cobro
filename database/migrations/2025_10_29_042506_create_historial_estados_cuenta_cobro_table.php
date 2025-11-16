<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->onDelete('cascade');
            $table->string('estado_anterior');
            $table->string('estado_nuevo');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->index(['cuenta_cobro_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
    }
};

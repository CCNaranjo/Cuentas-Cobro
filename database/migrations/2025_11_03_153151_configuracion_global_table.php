<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('parametros_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique()->comment('Identificador único del parámetro (slug)');
            $table->text('valor')->nullable()->comment('Valor del parámetro');
            $table->string('tipo_dato', 20)->default('string')->comment('Tipo: string, integer, boolean, decimal, json, url, date');
            $table->string('categoria', 50)->comment('Categoría: integraciones_api, seguridad, flujo_trabajo, financiero, plataforma');
            $table->string('descripcion')->comment('Descripción del parámetro');
            $table->boolean('es_cifrado')->default(false)->comment('Si el valor debe estar cifrado');
            $table->timestamps();

            // Índices
            $table->index('clave');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_sistema');
    }
};

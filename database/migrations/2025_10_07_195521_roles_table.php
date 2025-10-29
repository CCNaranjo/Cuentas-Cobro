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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->tinyInteger('nivel_jerarquico')->default(5);
            $table->boolean('es_sistema')->default(false);
            $table->timestamps();
        });

        Schema::create('usuario_organizacion_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->onDelete('set null');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->foreignId('asignado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->timestamps();
            
            $table->unique(['usuario_id', 'organizacion_id']);
            $table->index(['organizacion_id', 'estado']);
        });


        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->foreignId('modulo_id')->constrained('modulos')->onDelete('cascade');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('modulo_id');
        });

        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['rol_id', 'permiso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('usuario_organizacion_rol');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('rol_permisos');
    }
};

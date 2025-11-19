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
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_oficial');
            $table->string('nit')->unique();
            $table->string('departamento');
            $table->string('municipio');
            $table->string('direccion')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->string('email_institucional')->nullable();
            $table->string('codigo_vinculacion')->unique();
            $table->json('dominios_email')->nullable();
            $table->string('logo_url')->nullable();
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->foreignId('admin_global_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();

            $table->index('codigo_vinculacion');
            $table->index('nit');
        });


        Schema::create('vinculaciones_pendientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->string('codigo_vinculacion_usado')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->string('token_verificacion')->nullable();
            $table->timestamp('expira_en')->nullable();
            $table->timestamps();

            $table->index(['usuario_id', 'organizacion_id', 'estado']);
        });

        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrato')->unique();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->foreignId('contratista_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->text('objeto_contractual');
            $table->decimal('valor_total', 15, 2);
            $table->decimal('valor_pagado', 15, 2)->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('porcentaje_retencion_fuente', 5, 2)->default(0);
            $table->decimal('porcentaje_estampilla', 5, 2)->default(0);
            $table->enum('estado', [
                'borrador',
                'activo',
                'terminado',
                'suspendido',
                'liquidado'
            ])->default('borrador');
            $table->foreignId('vinculado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();

            $table->index('numero_contrato');
            $table->index(['organizacion_id', 'estado']);
            $table->index('contratista_id');
        });
        
        Schema::create('cuentas_bancarias_org', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->onDelete('cascade');
            $table->foreignId('banco_id')->constrained('bancos');
            $table->string('numero_cuenta')->unique();
            $table->enum('tipo_cuenta', ['ahorros', 'corriente']);
            $table->string('titular_cuenta');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
        Schema::dropIfExists('vinculaciones_pendientes');
        Schema::dropIfExists('contratos');
        Schema::dropIfExists('cuentas_bancarias_org');
    }
};

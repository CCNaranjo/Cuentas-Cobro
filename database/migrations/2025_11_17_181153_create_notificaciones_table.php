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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            // Usuario que recibe la notificación
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');

            // Tipo de notificación
            $table->enum('tipo', [
                'cuenta_cobro_radicada',
                'cuenta_cobro_aprobada',
                'cuenta_cobro_rechazada',
                'cuenta_cobro_requiere_correccion',
                'cuenta_cobro_en_proceso_pago',
                'cuenta_cobro_pagada',
                'cuenta_cobro_anulada',
            ]);

            // Información de la notificación
            $table->string('titulo');
            $table->text('mensaje');

            // Referencia a la cuenta de cobro
            $table->foreignId('cuenta_cobro_id')->nullable()->constrained('cuentas_cobro')->onDelete('cascade');

            // Referencia al usuario que generó la acción (opcional)
            $table->foreignId('generado_por')->nullable()->constrained('usuarios')->onDelete('set null');

            // Estado de la notificación
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_en')->nullable();

            // Datos adicionales en JSON (opcional)
            $table->json('datos_adicionales')->nullable();

            // Prioridad
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal');

            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['usuario_id', 'leida', 'created_at']);
            $table->index('cuenta_cobro_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};

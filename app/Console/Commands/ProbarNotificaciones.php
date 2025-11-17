<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CuentaCobro;
use App\Models\Notificacion;
use App\Models\Usuario;

class ProbarNotificaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:probar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de notificaciones de cuentas de cobro';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== PRUEBA DEL SISTEMA DE NOTIFICACIONES ===');
        $this->newLine();

        // 1. Obtener una cuenta de cobro en estado borrador
        $cuenta = CuentaCobro::where('estado', 'borrador')->first();

        if (!$cuenta) {
            $this->error('No se encontró ninguna cuenta de cobro en estado borrador');
            return 1;
        }

        $this->info("Cuenta de cobro: {$cuenta->numero_cuenta_cobro}");
        $this->info("Estado actual: {$cuenta->estado}");
        $this->info("Contrato: {$cuenta->contrato->numero_contrato}");
        $this->info("Supervisor: {$cuenta->contrato->supervisor->nombre}");
        $this->newLine();

        // 2. Simular el flujo completo de notificaciones
        $flujo = [
            ['estado' => 'radicada', 'quien' => 'Contratista', 'notifica_a' => 'Supervisor'],
            ['estado' => 'certificado_supervisor', 'quien' => 'Supervisor', 'notifica_a' => 'Revisor Contratación'],
            ['estado' => 'verificado_contratacion', 'quien' => 'Revisor Contratación', 'notifica_a' => 'Tesorero'],
            ['estado' => 'verificado_presupuesto', 'quien' => 'Tesorero', 'notifica_a' => 'Ordenador del Gasto'],
            ['estado' => 'aprobada_ordenador', 'quien' => 'Ordenador del Gasto', 'notifica_a' => 'Tesorero y Contratista'],
        ];

        foreach ($flujo as $paso) {
            $this->info("► Cambiando a estado: {$paso['estado']}");
            $this->line("  Acción realizada por: {$paso['quien']}");
            $this->line("  Debe notificar a: {$paso['notifica_a']}");

            // Cambiar estado (esto dispara el evento)
            $cuenta->cambiarEstado(
                $paso['estado'],
                $cuenta->created_by,
                "Cambio de prueba a {$paso['estado']}"
            );

            // Esperar un poco para que se procese (si el listener está en cola)
            sleep(1);

            // Verificar notificaciones creadas
            $ultimasNotificaciones = Notificacion::where('cuenta_cobro_id', $cuenta->id)
                ->where('datos_adicionales->estado_nuevo', $paso['estado'])
                ->get();

            if ($ultimasNotificaciones->count() > 0) {
                $this->line("  ✓ Se crearon {$ultimasNotificaciones->count()} notificación(es):", 'fg=green');
                foreach ($ultimasNotificaciones as $notif) {
                    $usuario = Usuario::find($notif->usuario_id);
                    $this->line("    - Para: {$usuario->nombre} ({$usuario->email})");
                    $this->line("      Título: {$notif->titulo}");
                    $this->line("      Prioridad: {$notif->prioridad}");
                }
            } else {
                $this->line('  ✗ No se crearon notificaciones', 'fg=red');
            }

            $this->newLine();
        }

        // 3. Resumen final
        $totalNotificaciones = Notificacion::where('cuenta_cobro_id', $cuenta->id)->count();
        $this->info("=== RESUMEN ===");
        $this->info("Total de notificaciones creadas: {$totalNotificaciones}");

        // Mostrar notificaciones por usuario
        $this->newLine();
        $this->info("Notificaciones por usuario:");
        $notificacionesPorUsuario = Notificacion::where('cuenta_cobro_id', $cuenta->id)
            ->selectRaw('usuario_id, count(*) as total')
            ->groupBy('usuario_id')
            ->get();

        foreach ($notificacionesPorUsuario as $stats) {
            $usuario = Usuario::find($stats->usuario_id);
            $noLeidas = Notificacion::where('cuenta_cobro_id', $cuenta->id)
                ->where('usuario_id', $stats->usuario_id)
                ->where('leida', false)
                ->count();
            $this->line("  - {$usuario->nombre}: {$stats->total} notificaciones ({$noLeidas} no leídas)");
        }

        $this->newLine();
        $this->info('✓ Prueba completada exitosamente!');

        return 0;
    }
}

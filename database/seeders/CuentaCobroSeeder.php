<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaCobro;
use App\Models\ItemCuentaCobro;
use App\Models\Contrato;
use App\Models\Usuario;
use App\Models\HistorialEstado;
use Illuminate\Support\Facades\DB;

class CuentaCobroSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener datos reales del sistema
        $organizacionChia = \App\Models\Organizacion::where('nombre_oficial', 'Alcaldía Municipal de Chía')->first();
        if (!$organizacionChia) {
            $this->command->error('No se encontró la Alcaldía de Chía. Ejecuta los seeders previos.');
            return;
        }

        // 2. Obtener contratista real
        $contratista = $this->getUsuarioPorRol('contratista', $organizacionChia->id);
        $supervisor = $this->getUsuarioPorRol('supervisor', $organizacionChia->id);
        $ordenador = $this->getUsuarioPorRol('ordenador_gasto', $organizacionChia->id);
        $tesorero = $this->getUsuarioPorRol('tesorero', $organizacionChia->id);

        if (!$contratista || !$supervisor || !$ordenador || !$tesorero) {
            $this->command->error('Faltan usuarios con roles necesarios. Ejecuta EjemploUsuariosOrganizacionSeeder.');
            return;
        }

        // 3. Obtener contratos reales
        $contrato1 = Contrato::where('numero_contrato', 'CONT-2025-001')->first();
        $contrato2 = Contrato::where('numero_contrato', 'CONT-2025-002')->first();

        if (!$contrato1 || !$contrato2) {
            $this->command->error('No se encontraron los contratos. Ejecuta ContratoSeeder primero.');
            return;
        }

        $this->command->info("Generando cuentas de cobro para contratista: {$contratista->nombre}");
        $this->command->info("Contratos encontrados: {$contrato1->numero_contrato}, {$contrato2->numero_contrato}");

        // Limpiar cuentas previas (opcional)
        // CuentaCobro::truncate();

        // ===================================================================
        // 1. Cuenta en Borrador (Contratista está creando)
        // ===================================================================
        $this->crearCuentaConItems($contrato1, $contratista, [
            'estado' => 'borrador',
            'periodo_inicio' => '2025-10-01',
            'periodo_fin' => '2025-10-31',
            'items' => [
                ['desc' => 'Construcción fase inicial', 'cant' => 1, 'unit' => 120000000, 'avance' => 30],
                ['desc' => 'Suministro de materiales', 'cant' => 1, 'unit' => 45000000, 'avance' => 100],
            ],
            'obs' => 'Cuenta en proceso de elaboración',
        ]);

        // ===================================================================
        // 2. Cuenta Radicada (Enviada al Supervisor)
        // ===================================================================
        $cuentaRadicada = $this->crearCuentaConItems($contrato1, $contratista, [
            'estado' => 'radicada',
            'fecha_radicacion' => '2025-11-01',
            'periodo_inicio' => '2025-10-01',
            'periodo_fin' => '2025-10-31',
            'items' => [
                ['desc' => 'Avance obra civil - 40%', 'cant' => 1, 'unit' => 200000000, 'avance' => 100],
            ],
            'obs' => 'Cuenta radicada esperando revisión técnica',
        ]);
        $this->cambiarEstado($cuentaRadicada, 'radicada', $contratista->id, 'Cuenta radicada por el contratista');

        // ===================================================================
        // 3. Cuenta Certificada por Supervisor
        // ===================================================================
        $cuentaCertificada = $this->crearCuentaConItems($contrato2, $contratista, [
            'estado' => 'certificado_supervisor',
            'fecha_radicacion' => '2025-10-20',
            'periodo_inicio' => '2025-09-16',
            'periodo_fin' => '2025-10-15',
            'items' => [
                ['desc' => 'Mantenimiento vías vereda La Balsa', 'cant' => 12, 'unit' => 8000000, 'avance' => 100],
            ],
        ]);
        $this->cambiarEstado($cuentaCertificada, 'certificado_supervisor', $supervisor->id, 'Certificado por supervisor técnico');

        // ===================================================================
        // 4. Cuenta Aprobada por Ordenador del Gasto
        // ===================================================================
        $cuentaAprobada = $this->crearCuentaConItems($contrato1, $contratista, [
            'estado' => 'aprobada_ordenador',
            'fecha_radicacion' => '2025-09-10',
            'periodo_inicio' => '2025-08-01',
            'periodo_fin' => '2025-08-31',
            'items' => [
                ['desc' => 'Adquisición de equipos', 'cant' => 5, 'unit' => 15000000, 'avance' => 100],
            ],
        ]);
        $this->cambiarEstado($cuentaAprobada, 'aprobada_ordenador', $ordenador->id, 'Aprobada por ordenador del gasto');

        // ===================================================================
        // 5. Cuenta Pagada (Con comprobante)
        // ===================================================================
        $cuentaPagada = $this->crearCuentaConItems($contrato2, $contratista, [
            'estado' => 'pagada',
            'fecha_radicacion' => '2025-08-05',
            'periodo_inicio' => '2025-07-01',
            'periodo_fin' => '2025-07-31',
            'fecha_pago_real' => '2025-09-20',
            'numero_comprobante_pago' => 'OP-2025-0891',
            'pila_verificada' => true,
            'items' => [
                ['desc' => 'Mantenimiento preventivo', 'cant' => 1, 'unit' => 85000000, 'avance' => 100],
            ],
        ]);
        $this->cambiarEstado($cuentaPagada, 'pagada', $tesorero->id, 'Pago ejecutado y registrado');

        $this->command->newLine();
        $this->command->info('Se crearon 5 cuentas de cobro reales en diferentes estados');
        $this->command->info('Listo para pruebas de flujo completo');
    }

    private function crearCuentaConItems($contrato, $contratista, $data)
    {
        $cuenta = CuentaCobro::create([
            'contrato_id' => $contrato->id,
            'numero_cuenta_cobro' => (new CuentaCobro())->generarNumero(),
            'fecha_radicacion' => $data['fecha_radicacion'] ?? null,
            'periodo_inicio' => $data['periodo_inicio'],
            'periodo_fin' => $data['periodo_fin'],
            'valor_bruto' => 0,
            'valor_neto' => 0,
            'pila_verificada' => $data['pila_verificada'] ?? false,
            'estado' => $data['estado'],
            'observaciones' => $data['obs'] ?? null,
            'created_by' => $contratista->id,
            'fecha_pago_real' => $data['fecha_pago_real'] ?? null,
            'numero_comprobante_pago' => $data['numero_comprobante_pago'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            ItemCuentaCobro::create([
                'cuenta_cobro_id' => $cuenta->id,
                'descripcion' => $item['desc'],
                'cantidad' => $item['cant'],
                'valor_unitario' => $item['unit'],
                'porcentaje_avance' => $item['avance'],
            ]);
        }

        // Recalcular valores
        $cuenta->fresh();
        $cuenta->calcularRetenciones();

        $this->command->info("Cuenta creada: {$cuenta->numero_cuenta_cobro} → {$cuenta->estadoNombre}");

        return $cuenta;
    }

    private function cambiarEstado($cuenta, $estado, $usuarioId, $comentario)
    {
        $cuenta->cambiarEstado($estado, $usuarioId, $comentario);
    }

    private function getUsuarioPorRol($rol, $orgId)
    {
        return Usuario::whereHas('roles', function ($q) use ($rol, $orgId) {
            $q->where('roles.nombre', $rol)
              ->where('usuario_organizacion_rol.organizacion_id', $orgId)
              ->where('usuario_organizacion_rol.estado', 'activo');
        })->first();
    }
}
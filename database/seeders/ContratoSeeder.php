<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contrato;
use App\Models\ContratoArchivo;
use App\Models\Organizacion;
use App\Models\Usuario;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener organización de Chía
        $organizacion = Organizacion::where('nombre_oficial', 'Alcaldía Municipal de Chía')->first();
        if (!$organizacion) {
            $this->command->error('No se encontró la organización de Chía. Ejecuta AlcaldiaEjemploSeeder primero.');
            return;
        }

        $this->command->info("Organización: {$organizacion->nombre_oficial} (ID: {$organizacion->id})");

        // 2. Obtener usuarios reales por rol
        $contratista = $this->getUsuarioPorRol('contratista', $organizacion->id);
        $supervisor = $this->getUsuarioPorRol('supervisor', $organizacion->id);
        $vinculadoPor = $this->getUsuarioPorRol('admin_organizacion', $organizacion->id) 
                        ?? Usuario::where('tipo_vinculacion', 'global_admin')->first();

        if (!$contratista || !$supervisor || !$vinculadoPor) {
            $this->command->error('Faltan usuarios necesarios. Ejecuta EjemploUsuariosOrganizacionSeeder primero.');
            return;
        }

        $this->command->info("Usuarios asignados:");
        $this->command->info("   Contratista: {$contratista->nombre} (ID: {$contratista->id})");
        $this->command->info("   Supervisor: {$supervisor->nombre} (ID: {$supervisor->id})");
        $this->command->info("   Vinculado por: {$vinculadoPor->nombre} (ID: {$vinculadoPor->id})");

        // 3. Crear 2 contratos reales
        $contratos = [
            [
                'numero' => 'CONT-2025-001',
                'objeto' => 'Construcción de infraestructura educativa en el sector rural del municipio',
                'valor' => 850000000,
                'inicio' => '2025-02-01',
                'fin' => '2025-12-20',
                'retencion' => 11.00,
                'estampilla' => 2.50,
                'archivos' => [
                    ['tipo' => 'contrato_firmado', 'nombre' => 'Contrato_Firmado_CONT-2025-001.pdf', 'desc' => 'Contrato principal firmado'],
                    ['tipo' => 'acta_inicio', 'nombre' => 'Acta_Inicio_CONT-2025-001.pdf', 'desc' => 'Acta de inicio de obra'],
                    ['tipo' => 'cronograma', 'nombre' => 'Cronograma_CONT-2025-001.xlsx', 'desc' => 'Cronograma de actividades'],
                ],
            ],
            [
                'numero' => 'CONT-2025-002',
                'objeto' => 'Mantenimiento y adecuación de vías terciarias en veredas del municipio',
                'valor' => 420000000,
                'inicio' => '2025-03-15',
                'fin' => '2025-10-30',
                'retencion' => 10.00,
                'estampilla' => 2.00,
                'archivos' => [
                    ['tipo' => 'contrato_firmado', 'nombre' => 'Contrato_Firmado_CONT-2025-002.pdf', 'desc' => 'Contrato principal firmado'],
                    ['tipo' => 'presupuesto', 'nombre' => 'Presupuesto_Detallado_CONT-2025-002.xlsx', 'desc' => 'Presupuesto detallado'],
                ],
            ],
        ];

        foreach ($contratos as $data) {
            // Eliminar contrato previo si existe
            Contrato::where('numero_contrato', $data['numero'])->delete();

            $contrato = Contrato::create([
                'numero_contrato' => $data['numero'],
                'organizacion_id' => $organizacion->id,
                'contratista_id' => $contratista->id,
                'supervisor_id' => $supervisor->id,
                'objeto_contractual' => $data['objeto'],
                'valor_total' => $data['valor'],
                'fecha_inicio' => $data['inicio'],
                'fecha_fin' => $data['fin'],
                'porcentaje_retencion_fuente' => $data['retencion'],
                'porcentaje_estampilla' => $data['estampilla'],
                'estado' => 'activo',
                'vinculado_por' => $vinculadoPor->id,
            ]);

            $this->command->info("Contrato creado: {$contrato->numero_contrato} (ID: {$contrato->id})");

            // Subir archivos
            foreach ($data['archivos'] as $archivo) {
                $this->subirArchivoContrato($contrato, $archivo, $vinculadoPor->id);
            }
        }

        $this->command->newLine();
        $this->command->info('Seeder de contratos y archivos ejecutado con éxito');
        $this->command->info('Archivos en FTP: storage/ftp/contratos/' . $organizacion->id . '/');
    }

    private function getUsuarioPorRol(string $rolNombre, int $organizacionId): ?Usuario
    {
    return Usuario::whereHas('roles', function ($query) use ($rolNombre, $organizacionId) {
        $query->where('roles.nombre', $rolNombre)
            ->where('usuario_organizacion_rol.organizacion_id', $organizacionId)
            ->where('usuario_organizacion_rol.estado', 'activo');
    })->first();
    }

    private function subirArchivoContrato(Contrato $contrato, array $datos, int $subidoPorId): void
    {
        $extension = pathinfo($datos['nombre'], PATHINFO_EXTENSION);
        $nombreUnico = $contrato->numero_contrato . '_' . $datos['tipo'] . '_' . time() . '_' . Str::random(6) . '.' . $extension;
        $ruta = "contratos/{$contrato->organizacion_id}/{$nombreUnico}";

        $contenido = $this->generarContenidoArchivo($datos['tipo'], $contrato);

        try {
            // Intentar usar FTP, si falla usar public
            try {
                $subido = Storage::disk('ftp')->put($ruta, $contenido);
            } catch (\Exception $ftpError) {
                $this->command->warn("FTP no disponible, usando almacenamiento local");
                $subido = Storage::disk('public')->put($ruta, $contenido);
            }

            if ($subido) {
                ContratoArchivo::create([
                    'contrato_id' => $contrato->id,
                    'subido_por' => $subidoPorId,
                    'nombre_original' => $datos['nombre'],
                    'nombre_archivo' => $nombreUnico,
                    'ruta' => $ruta,
                    'tipo_archivo' => $extension,
                    'mime_type' => $this->getMimeType($extension),
                    'tamaño' => strlen($contenido),
                    'tipo_documento' => $datos['tipo'],
                    'descripcion' => $datos['desc'],
                ]);

                $this->command->info("   Archivo: {$datos['nombre']}");
            } else {
                $this->command->error("   Falló subida: {$datos['nombre']}");
            }
        } catch (\Exception $e) {
            $this->command->error("   Error FTP: {$datos['nombre']} → {$e->getMessage()}");
        }
    }

    private function generarContenidoArchivo(string $tipo, Contrato $contrato): string
    {
        return match ($tipo) {
            'contrato_firmado' => $this->pdfBasico("CONTRATO FIRMADO\n\nN°: {$contrato->numero_contrato}\nValor: $" . number_format($contrato->valor_total, 0, ',', '.') . "\nObjeto: {$contrato->objeto_contractual}"),
            'acta_inicio' => $this->pdfBasico("ACTA DE INICIO\n\nContrato: {$contrato->numero_contrato}\nFecha: {$contrato->fecha_inicio}\nPartes: Alcaldía de Chía y Contratista"),
            'cronograma', 'presupuesto' => $this->excelBasico("Cronograma de Actividades\nContrato: {$contrato->numero_contrato}"),
            default => $this->pdfBasico("Documento: {$tipo}\nContrato: {$contrato->numero_contrato}"),
        };
    }

    private function pdfBasico(string $texto): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj 4 0 obj<</Length " . strlen($texto) . ">>stream\nBT /F1 12 Tf 100 700 Td ($texto) Tj ET endstream endobj xref\n0 5\n0000000000 65535 f \n0000000015 00000 n \n0000000079 00000 n \n0000000146 00000 n \n0000000250 00000 n \ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n400\n%%EOF";
    }

    private function excelBasico(string $titulo): string
    {
        return "$titulo\n\nActividad,Inicio,Fin,Estado\nConstrucción,2025-02-01,2025-06-30,En progreso\nAcabados,2025-07-01,2025-12-20,Pendiente";
    }

    private function getMimeType(string $ext): string
    {
        return match (strtolower($ext)) {
            'pdf' => 'application/pdf',
            'xlsx', 'xls' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }
}
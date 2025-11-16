<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContratoArchivo;
use App\Models\Contrato;
use Illuminate\Support\Facades\Storage;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contrato::create([
            'numero_contrato' => 'CONT-2024-001',
            'organizacion_id' => 1,
            'contratista_id' => 1,
            'supervisor_id' => 1,
            'objeto_contractual' => 'Construcción de infraestructura educativa en el municipio para la mejora de espacios académicos',
            'valor_total' => 500000000,
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-12-15',
            'porcentaje_retencion_fuente' => 10.00,
            'porcentaje_estampilla' => 2.00,
            'estado' => 'activo',
            'vinculado_por' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('🔄 Creando archivos de prueba para contratos...');

        // Buscar el contrato de prueba
        $contrato = Contrato::where('numero_contrato', 'CONT-2024-001')->first();

        if (!$contrato) {
            $this->command->error('❌ No se encontró el contrato CONT-2024-001. Ejecuta primero el ContratoSeeder.');
            return;
        }

        // Crear archivos de prueba en el servidor FTP
        $archivos = [
            [
                'nombre_original' => 'Contrato_Firmado_2024.pdf',
                'tipo_documento' => 'contrato_firmado',
                'descripcion' => 'Contrato principal firmado por ambas partes',
                'tipo_archivo' => 'pdf',
                'mime_type' => 'application/pdf',
                'contenido' => $this->generarContenidoPDF('Contrato Firmado - ' . $contrato->numero_contrato),
            ],
            [
                'nombre_original' => 'Adicion_Presupuestal.pdf',
                'tipo_documento' => 'adicion',
                'descripcion' => 'Adición al contrato por incremento de presupuesto',
                'tipo_archivo' => 'pdf',
                'mime_type' => 'application/pdf',
                'contenido' => $this->generarContenidoPDF('Adición Presupuestal'),
            ],
            [
                'nombre_original' => 'Acta_Inicio_Obra.pdf',
                'tipo_documento' => 'acta_inicio',
                'descripcion' => 'Acta de inicio de la obra del 15 de enero de 2024',
                'tipo_archivo' => 'pdf',
                'mime_type' => 'application/pdf',
                'contenido' => $this->generarContenidoPDF('Acta de Inicio'),
            ],
            [
                'nombre_original' => 'Cronograma_Actividades.xlsx',
                'tipo_documento' => 'otro',
                'descripcion' => 'Cronograma detallado de actividades del proyecto',
                'tipo_archivo' => 'xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'contenido' => $this->generarContenidoExcel('Cronograma'),
            ],
        ];

        foreach ($archivos as $archivoData) {
            try {
                // Generar nombre único para el archivo
                $nombreArchivo = $contrato->numero_contrato . '_' .
                                $archivoData['tipo_documento'] . '_' .
                                time() . '_' .
                                uniqid() . '.' .
                                $archivoData['tipo_archivo'];

                // Definir ruta en el servidor FTP
                $ruta = 'contratos/' . $contrato->organizacion_id . '/' . $nombreArchivo;

                // Subir archivo al servidor FTP
                $resultado = Storage::disk('ftp')->put($ruta, $archivoData['contenido']);

                if ($resultado) {
                    // Crear registro en la base de datos
                    ContratoArchivo::create([
                        'contrato_id' => $contrato->id,
                        'subido_por' => 1, // Usuario admin
                        'nombre_original' => $archivoData['nombre_original'],
                        'nombre_archivo' => $nombreArchivo,
                        'ruta' => $ruta,
                        'tipo_archivo' => $archivoData['tipo_archivo'],
                        'mime_type' => $archivoData['mime_type'],
                        'tamaño' => strlen($archivoData['contenido']),
                        'tipo_documento' => $archivoData['tipo_documento'],
                        'descripcion' => $archivoData['descripcion'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("✅ Archivo '{$archivoData['nombre_original']}' creado");
                } else {
                    $this->command->error("❌ Error al subir '{$archivoData['nombre_original']}'");
                }

            } catch (\Exception $e) {
                $this->command->error("❌ Error con '{$archivoData['nombre_original']}': {$e->getMessage()}");
            }

            // Pequeña pausa para evitar conflictos de nombre
            usleep(100000); // 0.1 segundos
        }

        $this->command->newLine();
        $this->command->info('🎉 Seeder de archivos de contratos ejecutado exitosamente');
        $this->command->info('📂 Archivos creados en: contratos/' . $contrato->organizacion_id . '/');
    }

    /**
     * Generar contenido de prueba para un PDF
     */
    private function generarContenidoPDF($titulo)
    {
        return <<<PDF
%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj
4 0 obj
<<
/Length 100
>>
stream
BT
/F1 24 Tf
100 700 Td
($titulo) Tj
0 -50 Td
(Documento de prueba generado automáticamente) Tj
0 -30 Td
(Fecha: {date('Y-m-d H:i:s')}) Tj
ET
endstream
endobj
5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj
xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000423 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
521
%%EOF
PDF;
    }

    /**
     * Generar contenido de prueba para un Excel (CSV simple)
     */
    private function generarContenidoExcel($titulo)
    {
        return <<<EXCEL
Actividad,Fecha Inicio,Fecha Fin,Responsable,Estado
Planificación del proyecto,2024-01-15,2024-01-30,Juan Pérez,Completado
Diseño arquitectónico,2024-02-01,2024-03-15,María García,En progreso
Construcción fase 1,2024-03-16,2024-06-30,Carlos Rodríguez,Pendiente
Construcción fase 2,2024-07-01,2024-10-31,Carlos Rodríguez,Pendiente
Acabados finales,2024-11-01,2024-12-15,Ana Martínez,Pendiente
EXCEL;
    }
}

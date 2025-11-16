<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestFtpConnection extends Command
{
    protected $signature = 'ftp:test';
    protected $description = 'Probar la conexión FTP';

    public function handle()
    {
        $this->info('🔄 Probando conexión FTP...');
        $this->newLine();

        try {
            // Intentar crear un archivo de prueba
            $contenido = 'Prueba de conexión FTP - ' . now()->format('Y-m-d H:i:s');
            $resultado = Storage::disk('ftp')->put('test_conexion.txt', $contenido);

            if ($resultado) {
                $this->info('✅ Archivo de prueba creado exitosamente');

                // Verificar que el archivo existe
                if (Storage::disk('ftp')->exists('test_conexion.txt')) {
                    $this->info('✅ Archivo verificado en el servidor FTP');

                    // Leer el contenido
                    $contenidoLeido = Storage::disk('ftp')->get('test_conexion.txt');
                    $this->info('✅ Contenido leído: ' . $contenidoLeido);

                    // Eliminar el archivo de prueba
                    Storage::disk('ftp')->delete('test_conexion.txt');
                    $this->info('✅ Archivo de prueba eliminado');
                } else {
                    $this->error('❌ El archivo no se encontró en el servidor');
                }

                $this->newLine();
                $this->info('🎉 ¡Conexión FTP exitosa!');

            } else {
                $this->error('❌ No se pudo crear el archivo de prueba');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error de conexión FTP:');
            $this->error($e->getMessage());
            $this->newLine();

            $this->warn('💡 Verifica:');
            $this->line('1. Que las credenciales FTP sean correctas en tu .env');
            $this->line('2. Que el servidor FTP esté activo');
            $this->line('3. Que el firewall permita la conexión');
            $this->line('4. Que el usuario tenga permisos de escritura');

            return 1;
        }

        return 0;
    }
}

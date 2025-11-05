<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | FTP Disk - Para archivos de Contratos
        |--------------------------------------------------------------------------
        |
        | Este disco se utiliza para almacenar archivos de contratos en un servidor
        | FTP. Los archivos se organizan por organizacion_id en la carpeta 'contratos'.
        |
        | Configuración requerida en .env:
        | - FTP_HOST: Dirección del servidor FTP (ej: 127.0.0.1 para local)
        | - FTP_USERNAME: Usuario FTP
        | - FTP_PASSWORD: Contraseña del usuario FTP
        | - FTP_PORT: Puerto FTP (por defecto 21)
        | - FTP_ROOT: Carpeta raíz en el servidor (/ para la raíz)
        | - FTP_SSL: true/false para usar FTPS (conexión segura)
        |
        | Ejemplo de uso:
        | Storage::disk('ftp')->put('ruta/archivo.pdf', $contenido);
        | Storage::disk('ftp')->get('ruta/archivo.pdf');
        | Storage::disk('ftp')->delete('ruta/archivo.pdf');
        |
        */
        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            
            // Puerto del servidor FTP (por defecto 21)
            // IMPORTANTE: Convertir a integer con (int)
            'port' => (int) env('FTP_PORT', 21),
            
            // Directorio raíz en el servidor FTP
            // Si dejas '/' los archivos se guardarán en la raíz del servidor
            // Ejemplo: '/contratos' guardará en una subcarpeta llamada contratos
            'root' => env('FTP_ROOT', '/'),
            
            // Modo pasivo (recomendado para servidores detrás de firewalls)
            // Si tienes problemas de conexión, intenta cambiar a false
            'passive' => true,
            
            // Usar SSL/TLS (FTPS) para conexiones seguras
            // Requiere que el servidor FTP soporte FTPS
            'ssl' => env('FTP_SSL', false),
            
            // Timeout de conexión en segundos
            'timeout' => 30,
            
            // Manejo de errores
            'throw' => false,
            'report' => false,
            
            // Opciones adicionales de FTP (avanzado)
            // 'ignorePassiveAddress' => true, // Útil para NAT/Firewalls
            // 'timestampsOnUnixListingsEnabled' => true, // Para servidores Unix
            // 'recurseManually' => true, // Para crear carpetas recursivamente
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
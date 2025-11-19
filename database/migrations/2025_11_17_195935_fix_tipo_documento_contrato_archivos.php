<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Primero hacer un backup temporal de los datos si es necesario
        $backupExists = Schema::hasTable('contrato_archivos_backup');
        if (!$backupExists) {
            DB::statement('CREATE TABLE contrato_archivos_backup AS SELECT * FROM contrato_archivos');
        }

        // Modificar la columna con los tipos correctos
        DB::statement("ALTER TABLE contrato_archivos 
            MODIFY COLUMN tipo_documento ENUM(
                'contrato_firmado',
                'acta_inicio', 
                'cronograma',
                'presupuesto',
                'estudio_previo',
                'certificacion_presupuestal',
                'cdp',
                'acta_liquidacion',
                'otro'
            ) NOT NULL DEFAULT 'otro'");
    }

    public function down()
    {
        // Revertir a tipos básicos
        DB::statement("ALTER TABLE contrato_archivos 
            MODIFY COLUMN tipo_documento ENUM(
                'contrato_firmado',
                'acta_inicio',
                'otro'
            ) NOT NULL DEFAULT 'otro'");
    }
};
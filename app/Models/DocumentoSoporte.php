<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentoSoporte extends Model
{
    use HasFactory;

    protected $table = 'documentos_soporte';

    protected $fillable = [
        'cuenta_cobro_id',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'tamano_kb',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'tamano_kb' => 'integer',
    ];

    // ==================== RELACIONES ====================

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    // ==================== MÉTODOS AUXILIARES ====================

    public function getUrlDescarga()
    {
        return Storage::url($this->ruta_archivo);
    }

    public function getTamanoFormateado()
    {
        if ($this->tamano_kb < 1024) {
            return $this->tamano_kb . ' KB';
        }
        return round($this->tamano_kb / 1024, 2) . ' MB';
    }

    public function getTipoDocumentoNombre()
    {
        $tipos = [
            'acta_recibido' => 'Acta de Recibido',
            'informe' => 'Informe',
            'foto_evidencia' => 'Foto de Evidencia',
            'planilla' => 'Planilla',
            'otro' => 'Otro',
        ];

        return $tipos[$this->tipo_documento] ?? $this->tipo_documento;
    }

    public function eliminarArchivo()
    {
        if (Storage::exists($this->ruta_archivo)) {
            return Storage::delete($this->ruta_archivo);
        }
        return false;
    }

    // ==================== EVENTOS ====================

    protected static function boot()
    {
        parent::boot();

        // Eliminar archivo físico al eliminar el registro
        static::deleting(function ($documento) {
            $documento->eliminarArchivo();
        });
    }

    // ==================== ACCESSORS ====================

    public function getExtensionAttribute()
    {
        return pathinfo($this->nombre_archivo, PATHINFO_EXTENSION);
    }

    public function getIconoAttribute()
    {
        $iconos = [
            'pdf' => 'file-pdf',
            'doc' => 'file-word',
            'docx' => 'file-word',
            'xls' => 'file-excel',
            'xlsx' => 'file-excel',
            'jpg' => 'file-image',
            'jpeg' => 'file-image',
            'png' => 'file-image',
            'zip' => 'file-archive',
            'rar' => 'file-archive',
        ];

        return $iconos[$this->extension] ?? 'file';
    }
}

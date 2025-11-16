<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CuentaCobroArchivo extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobro_archivos';

    protected $fillable = [
        'cuenta_cobro_id',
        'subido_por',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo_archivo',
        'mime_type',
        'tamaño',
        'tipo_documento',
        'descripcion',
    ];

    protected $casts = [
        'tamaño' => 'integer',
    ];

    // Relaciones
    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(Usuario::class, 'subido_por');
    }

    // Métodos auxiliares
    public function getTamañoFormateadoAttribute()
    {
        $bytes = $this->tamaño;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getUrlDescargaAttribute()
    {
        return route('cuentas-cobro.archivos.descargar', $this->id);
    }

    public function getTipoDocumentoNombreAttribute()
    {
        $tipos = [
            'cuenta_cobro' => 'Cuenta de Cobro',
            'acta_recibido' => 'Acta de Recibido',
            'informe' => 'Informe',
            'foto_evidencia' => 'Foto de Evidencia',
            'planilla' => 'Planilla',
            'soporte_pago' => 'Soporte de Pago',
            'factura' => 'Factura',
            'otro' => 'Otro',
        ];

        return $tipos[$this->tipo_documento] ?? ucfirst($this->tipo_documento);
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

        return $iconos[$this->tipo_archivo] ?? 'file';
    }

    /**
     * Eliminar archivo del FTP al eliminar el registro
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($archivo) {
            // Eliminar del servidor FTP
            try {
                Storage::disk('ftp')->delete($archivo->ruta);
            } catch (\Exception $e) {
                \Log::error('Error al eliminar archivo del FTP: ' . $e->getMessage());
            }
        });
    }
}

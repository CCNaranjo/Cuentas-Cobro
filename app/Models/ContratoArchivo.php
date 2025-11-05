<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContratoArchivo extends Model
{
    use HasFactory;

    protected $table = 'contrato_archivos';

    protected $fillable = [
        'contrato_id',
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
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
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
        return route('contratos.archivos.descargar', $this->id);
    }

    /**
     * Eliminar archivo del FTP al eliminar el registro
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($archivo) {
            // Eliminar del servidor FTP
            Storage::disk('ftp')->delete($archivo->ruta);
        });
    }
}
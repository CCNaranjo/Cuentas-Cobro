<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class ConfiguracionGlobal extends Model
{
    protected $table = 'parametros_sistema';
    
    protected $fillable = [
        'clave',
        'valor',
        'tipo_dato',
        'categoria',
        'descripcion',
        'es_cifrado',
    ];
    
    protected $casts = [
        'es_cifrado' => 'boolean',
    ];
    
    /**
     * Obtener un parámetro por su clave
     */
    public static function obtener(string $clave, $default = null)
    {
        return Cache::remember("parametro_sistema_{$clave}", 3600, function () use ($clave, $default) {
            $parametro = self::where('clave', $clave)->first();
            
            if (!$parametro) {
                return $default;
            }
            
            return self::convertirValor($parametro);
        });
    }
    
    /**
     * Establecer un parámetro
     */
    public static function establecer(string $clave, $valor)
    {
        $parametro = self::where('clave', $clave)->first();
        
        if (!$parametro) {
            throw new \Exception("Parámetro '{$clave}' no existe en el sistema");
        }
        
        // Cifrar si es necesario
        if ($parametro->es_cifrado && $valor !== null) {
            $valor = Crypt::encryptString($valor);
        }
        
        // Convertir booleanos
        if ($parametro->tipo_dato === 'boolean') {
            $valor = $valor ? 'true' : 'false';
        }
        
        $parametro->update(['valor' => $valor]);
        
        // Limpiar caché
        Cache::forget("parametro_sistema_{$clave}");
        
        return true;
    }
    
    /**
     * Obtener todos los parámetros de una categoría
     */
    public static function obtenerCategoria(string $categoria)
    {
        return Cache::remember("parametros_categoria_{$categoria}", 3600, function () use ($categoria) {
            $parametros = self::where('categoria', $categoria)->get();
            
            $resultado = [];
            foreach ($parametros as $param) {
                $resultado[$param->clave] = self::convertirValor($param);
            }
            
            return $resultado;
        });
    }
    
    /**
     * Convertir valor según tipo de dato
     */
    protected static function convertirValor($parametro)
    {
        $valor = $parametro->valor;
        
        // Descifrar si es necesario
        if ($parametro->es_cifrado && $valor !== null) {
            try {
                $valor = Crypt::decryptString($valor);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        // Convertir según tipo
        switch ($parametro->tipo_dato) {
            case 'boolean':
                return $valor === 'true' || $valor === '1' || $valor === 1;
            case 'integer':
                return (int) $valor;
            case 'decimal':
                return (float) $valor;
            case 'json':
                return json_decode($valor, true);
            default:
                return $valor;
        }
    }
    
    /**
     * Limpiar toda la caché de parámetros
     */
    public static function limpiarCache()
    {
        Cache::flush(); // O más específico si prefieres
    }
}
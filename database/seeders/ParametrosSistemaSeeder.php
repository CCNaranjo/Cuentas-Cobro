<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrosSistemaSeeder extends Seeder
{
    public function run(): void
    {
        $parametros = [
            // ============ INTEGRACIONES API ============
            [
                'clave' => 'api_pagos_url_base',
                'valor' => 'https://api.pagos.ejemplo.com',
                'tipo_dato' => 'url',
                'categoria' => 'integraciones_api',
                'descripcion' => 'URL base del proveedor de pagos externo (e.g., PSE, banco)',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'api_facturacion_url_base',
                'valor' => 'https://api.facturacion.ejemplo.com',
                'tipo_dato' => 'url',
                'categoria' => 'integraciones_api',
                'descripcion' => 'URL base del proveedor de Facturación Electrónica',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'api_integracion_key_global',
                'valor' => null,
                'tipo_dato' => 'string',
                'categoria' => 'integraciones_api',
                'descripcion' => 'Clave de autenticación global para APIs de terceros (CIFRADA)',
                'es_cifrado' => true,
            ],
            [
                'clave' => 'api_pila_habilitada',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'categoria' => 'integraciones_api',
                'descripcion' => 'Habilitar validación de Planilla Integrada de Liquidación de Aportes (PILA)',
                'es_cifrado' => false,
            ],

            // ============ SEGURIDAD ============
            [
                'clave' => 'min_longitud_contrasena',
                'valor' => '8',
                'tipo_dato' => 'integer',
                'categoria' => 'seguridad',
                'descripcion' => 'Longitud mínima obligatoria para contraseñas de usuario',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'expiracion_sesion_minutos',
                'valor' => '120',
                'tipo_dato' => 'integer',
                'categoria' => 'seguridad',
                'descripcion' => 'Tiempo (minutos) que una sesión permanece activa antes de requerir login',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'forzar_2fa_global',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'categoria' => 'seguridad',
                'descripcion' => 'Obligar a todos los usuarios a usar autenticación de dos factores (2FA)',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'requiere_mayuscula_contrasena',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'categoria' => 'seguridad',
                'descripcion' => 'Contraseña debe contener al menos una mayúscula',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'requiere_numero_contrasena',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'categoria' => 'seguridad',
                'descripcion' => 'Contraseña debe contener al menos un número',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'requiere_caracter_especial_contrasena',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'categoria' => 'seguridad',
                'descripcion' => 'Contraseña debe contener al menos un carácter especial',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'intentos_maximos_login',
                'valor' => '5',
                'tipo_dato' => 'integer',
                'categoria' => 'seguridad',
                'descripcion' => 'Intentos máximos de login antes de bloquear la cuenta',
                'es_cifrado' => false,
            ],

            // ============ FLUJO DE TRABAJO ============
            [
                'clave' => 'dias_maximos_certificacion',
                'valor' => '5',
                'tipo_dato' => 'integer',
                'categoria' => 'flujo_trabajo',
                'descripcion' => 'Días límite para que un supervisor certifique un cumplimiento (SLAs)',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'dias_maximos_revision_financiera',
                'valor' => '10',
                'tipo_dato' => 'integer',
                'categoria' => 'flujo_trabajo',
                'descripcion' => 'Días límite para que un ordenador de gasto revise una cuenta radicada',
                'es_cifrado' => false,
            ],

            // ============ FINANCIERO POR DEFECTO ============
            [
                'clave' => 'porcentaje_iva_default',
                'valor' => '19.00',
                'tipo_dato' => 'decimal',
                'categoria' => 'financiero',
                'descripcion' => 'Tasa de IVA por defecto para todo el país/plataforma (%)',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'porcentaje_retencion_base',
                'valor' => '11.00',
                'tipo_dato' => 'decimal',
                'categoria' => 'financiero',
                'descripcion' => 'Tasa de retención general por defecto (puede ser sobrescrita localmente) (%)',
                'es_cifrado' => false,
            ],

            // ============ PLATAFORMA ============
            [
                'clave' => 'version_actual_sistema',
                'valor' => '1.0.0',
                'tipo_dato' => 'string',
                'categoria' => 'plataforma',
                'descripcion' => 'Versión actual del sistema para control técnico',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'limite_licencia_base_usuarios',
                'valor' => '100',
                'tipo_dato' => 'integer',
                'categoria' => 'plataforma',
                'descripcion' => 'Cantidad máxima de usuarios permitidos en el plan de licencia estándar',
                'es_cifrado' => false,
            ],
            [
                'clave' => 'mantenimiento_activo',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'categoria' => 'plataforma',
                'descripcion' => 'Sistema en modo mantenimiento (bloquea acceso excepto admin global)',
                'es_cifrado' => false,
            ],
        ];

        foreach ($parametros as $parametro) {
            DB::table('parametros_sistema')->insert(array_merge($parametro, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

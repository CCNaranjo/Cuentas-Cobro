<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\VinculacionPendiente;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Intentar autenticar
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Asegurarse de que $user es instancia de Usuario
            if (!$user instanceof \App\Models\Usuario) {
                $user = \App\Models\Usuario::find($user->id);
            }

            // Actualizar último acceso
            $user->ultimo_acceso = now();
            $user->save();

            // Si el usuario tiene organizaciones vinculadas, establecer la primera como actual
            if (!$user->esAdminGlobal()) {
                $primeraOrganizacion = $user->organizacionesVinculadas()->first();
                if ($primeraOrganizacion) {
                    session(['organizacion_actual' => $primeraOrganizacion->id]);
                }
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'documento_identidad' => 'nullable|string|max:50|unique:usuarios,documento_identidad',
            'telefono' => 'nullable|string|max:20',
            'codigo_vinculacion' => 'nullable|string',
        ]);

        // Crear usuario
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'documento_identidad' => $request->documento_identidad,
            'telefono' => $request->telefono,
            'estado' => 'activo',
            'tipo_vinculacion' => 'sin_vinculacion',
            'email_verificado_en' => now(), // Auto-verificar por ahora
        ]);

        // Detectar organización por dominio de email o código
        $organizacion = null;

        // 1. Intentar con código de vinculación
        if ($request->filled('codigo_vinculacion')) {
            $organizacion = Organizacion::where('codigo_vinculacion', $request->codigo_vinculacion)
                ->where('estado', 'activa')
                ->first();
        }

        // 2. Intentar con dominio de email
        if (!$organizacion) {
            $dominioEmail = '@' . explode('@', $request->email)[1];
            $organizacion = Organizacion::whereJsonContains('dominios_email', $dominioEmail)
                ->where('estado', 'activa')
                ->first();
        }

        // Si se encontró organización, crear vinculación pendiente
        if ($organizacion) {
            VinculacionPendiente::create([
                'usuario_id' => $usuario->id,
                'organizacion_id' => $organizacion->id,
                'codigo_vinculacion_usado' => $request->codigo_vinculacion,
                'estado' => 'pendiente',
                'token_verificacion' => Str::random(64),
                'expira_en' => now()->addDays(7),
            ]);

            // TODO: Notificar al admin de la organización
        }

        // Login automático
        Auth::login($usuario);

        return redirect()->route('dashboard')
            ->with('success', 'Cuenta creada exitosamente. ' .
                ($organizacion ? 'Tu solicitud de vinculación está pendiente de aprobación.' : ''));
    }

    /**
     * Procesar logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada exitosamente');
    }

    /**
     * Verificar email (para implementación futura)
     */
    public function verifyEmail($token)
    {
        $vinculacion = VinculacionPendiente::where('token_verificacion', $token)
            ->where('estado', 'pendiente')
            ->where('expira_en', '>', now())
            ->first();

        if (!$vinculacion) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Token de verificación inválido o expirado']);
        }

        $usuario = $vinculacion->usuario;
        $usuario->update([
            'email_verificado_en' => now(),
            'estado' => 'activo',
        ]);

        return redirect()->route('login')
            ->with('success', 'Email verificado exitosamente. Ahora puedes iniciar sesión.');
    }
}

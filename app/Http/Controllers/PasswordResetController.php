<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario para solicitar recuperación
     */
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar código de verificación por email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No existe una cuenta con este correo electrónico.'
        ]);

        // Generar código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Eliminar tokens anteriores del usuario
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Guardar nuevo token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $code,
            'created_at' => Carbon::now()
        ]);

        // Enviar email
        $user = User::where('email', $request->email)->first();
        $emailSent = false;
        
        try {
            Mail::raw(
                "Hola {$user->name},\n\n" .
                "Has solicitado recuperar tu contraseña.\n\n" .
                "Tu código de verificación es: {$code}\n\n" .
                "Este código expirará en 15 minutos.\n\n" .
                "Si no solicitaste este cambio, ignora este mensaje.\n\n" .
                "Saludos,\n" .
                "Equipo de Doggie's",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Código de Recuperación de Contraseña - Doggie\'s');
                }
            );
            $emailSent = true;
        } catch (\Exception $e) {
            // Si el email falla, registrar en log
            Log::error('Error enviando email: ' . $e->getMessage());
        }

        // Si estamos en modo debug y el email falló, mostrar el código
        if (!$emailSent && config('app.debug')) {
            return redirect()
                ->route('password.verify-code')
                ->with('email', $request->email)
                ->with('success', 'Error al enviar email: ' . (isset($e) ? $e->getMessage() : 'Desconocido'))
                ->with('debug_code', $code)
                ->with('warning', 'MODO DESARROLLO: Tu código es: ' . $code);
        }

        if ($emailSent) {
            // Guardar email en sesión persistente, no flash
            session(['email' => $request->email]);
            
            return redirect()
                ->route('password.verify-code')
                ->with('success', 'Se ha enviado un código de verificación a tu correo.');
        }

        return back()
            ->withInput()
            ->with('error', 'Error al enviar el correo. Por favor, verifica la configuración de email.');
    }

    /**
     * Mostrar formulario para ingresar código
     */
    public function showVerifyCodeForm()
    {
        // Verificar si existe el email en la sesión (persistente)
        if (!session()->has('email')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesión expirada. Por favor, solicita un nuevo código.');
        }

        return view('auth.passwords.verify-code');
    }

    /**
     * Verificar código ingresado
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $email = session('email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesión expirada. Por favor, solicita un nuevo código.');
        }

        // Buscar token
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $request->code)
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['code' => 'Código incorrecto.']);
        }

        // Verificar que no haya expirado (15 minutos)
        if (Carbon::parse($resetToken->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        // Código válido, guardar código en sesión y redirigir
        session(['code' => $request->code]);

        return redirect()
            ->route('password.reset');
    }

    /**
     * Mostrar formulario para nueva contraseña
     */
    public function showResetForm()
    {
        if (!session()->has('email') || !session()->has('code')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesión expirada. Por favor, solicita un nuevo código.');
        }

        return view('auth.passwords.reset');
    }

    /**
     * Cambiar la contraseña
     */
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        $email = session('email');
        $code = session('code');

        if (!$email || !$code) {
            return redirect()->route('password.request')
                ->with('error', 'Sesión expirada. Por favor, solicita un nuevo código.');
        }

        // Verificar token nuevamente
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->first();

        if (!$resetToken) {
            return redirect()->route('password.request')
                ->with('error', 'Token inválido. Por favor, solicita un nuevo código.');
        }

        // Actualizar contraseña
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar token usado
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Registrar actividad
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset',
            'description' => 'Restableció su contraseña mediante código de verificación',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Limpiar sesión
        session()->forget(['email', 'code']);

        return redirect()
            ->route('login')
            ->with('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }
}

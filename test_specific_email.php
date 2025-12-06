<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$toEmail = 'c1e2s3a4r.2002@gmail.com';
$code = '123456'; // Código de ejemplo
$userName = 'César'; // Nombre de ejemplo

echo "Enviando correo de prueba a: $toEmail...\n";

try {
    Mail::raw(
        "Hola {$userName},\n\n" .
        "Has solicitado recuperar tu contraseña.\n\n" .
        "Tu código de verificación es: {$code}\n\n" .
        "Este código expirará en 15 minutos.\n\n" .
        "Si no solicitaste este cambio, ignora este mensaje.\n\n" .
        "Saludos,\n" .
        "Equipo de Doggie's",
        function ($message) use ($toEmail) {
            $message->to($toEmail)
                ->subject('Código de Recuperación de Contraseña - Doggie\'s');
        }
    );
    echo "¡Correo enviado EXITOSAMENTE a $toEmail!\n Por favor revisa tu bandeja de entrada (y spam).";
} catch (\Exception $e) {
    echo "FALLÓ el envío: " . $e->getMessage() . "\n";
}

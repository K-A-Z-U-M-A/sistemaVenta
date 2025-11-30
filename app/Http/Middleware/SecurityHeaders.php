<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevenir que la página sea cargada en iframes (Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protección contra XSS en navegadores antiguos
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Prevenir que el navegador adivine el tipo de contenido (MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy para privacidad
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Strict Transport Security (HSTS) - Fuerza HTTPS
        // Solo activar si tienes certificado SSL instalado
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Content Security Policy (CSP) - Básico
        // Esto previene la carga de scripts no autorizados
        $response->headers->set('Content-Security-Policy', "default-src 'self' https:; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:;");

        return $response;
    }
}

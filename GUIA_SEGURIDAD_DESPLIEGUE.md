# 🛡️ Guía de Seguridad y Despliegue

Esta guía detalla los pasos necesarios para asegurar el servidor y configurar las nuevas funcionalidades de mantenimiento y recuperación.

## 1. 🔒 Bloqueo de Puertos (Firewall)

Para proteger el servidor, debes bloquear todos los puertos excepto los necesarios (generalmente 80, 443 y tal vez 22 para SSH).

### En Windows (PowerShell como Administrador)

1. **Ver reglas actuales:**
   ```powershell
   Get-NetFirewallRule
   ```

2. **Bloquear un puerto específico (ej. puerto de base de datos 5432 para acceso externo):**
   Si solo quieres que la aplicación local acceda a la DB, bloquea el acceso externo.
   ```powershell
   New-NetFirewallRule -DisplayName "Bloquear PostgreSQL Externo" -Direction Inbound -LocalPort 5432 -Protocol TCP -Action Block
   ```

3. **Permitir solo tráfico Web (80/443):**
   Asegúrate de que las reglas para HTTP y HTTPS estén habilitadas.

### En Linux (UFW - Uncomplicated Firewall)

```bash
# Denegar todo el tráfico entrante por defecto
sudo ufw default deny incoming

# Permitir tráfico saliente
sudo ufw default allow outgoing

# Permitir SSH (si es necesario)
sudo ufw allow ssh

# Permitir Servidor Web
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Activar Firewall
sudo ufw enable
```

## 2. 📧 Configuración de Correo (Recuperación de Contraseña)

Para que funcione la recuperación de contraseña, debes configurar un servidor SMTP en el archivo `.env`.

1. Abre el archivo `.env`.
2. Busca la sección de `MAIL`.
3. Configura con tu proveedor (Gmail, Outlook, Mailtrap, etc.).

**Ejemplo para Gmail:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```
*Nota: Para Gmail, debes generar una "Contraseña de Aplicación" en la configuración de seguridad de tu cuenta de Google.*

## 3. 🛡️ Protección contra Inyección SQL y XSS

El sistema ya cuenta con protecciones activas:

1. **Middleware de Seguridad (`SecurityHeaders`):** 
   - Se ha implementado un middleware que añade cabeceras HTTP estrictas.
   - Protege contra Clickjacking (`X-Frame-Options`).
   - Protege contra XSS (`X-XSS-Protection`).
   - Previene MIME Sniffing (`X-Content-Type-Options`).

2. **Eloquent ORM:**
   - El sistema utiliza Eloquent de Laravel, que usa *Prepared Statements* por defecto, neutralizando ataques de inyección SQL.
   - **Recomendación:** Evita usar `DB::raw()` con datos ingresados por el usuario sin validación previa.

## 4. 💾 Sistema de Backups

Se ha implementado un módulo de backups en `/backups`.
- **Requisito:** El comando `pg_dump` debe estar accesible en el PATH del sistema (Variables de Entorno).
- Si estás en Windows y falla, agrega la ruta de la carpeta `bin` de PostgreSQL al PATH de Windows.

## 5. 🔧 Módulo de Mantenimiento

En `/system` puedes:
- Ver información del servidor (Versiones PHP, Laravel, DB).
- Limpiar cachés (`optimize:clear`) si haces cambios en `.env` o configuración y no se reflejan.
- Cachear configuración y rutas para mejorar el rendimiento en producción.

---

**Recomendaciones Finales para Producción:**
1. Cambiar `APP_DEBUG=true` a `APP_DEBUG=false` en el archivo `.env`.
2. Ejecutar `php artisan config:cache` y `php artisan route:cache`.
3. Asegurar que la carpeta `storage` y `bootstrap/cache` tengan permisos de escritura.

# 🚀 Guía de Despliegue Gratuito - Sistema de Abarrotes

## 📋 Opciones de Hosting Gratuito

### **Opción 1: Railway.app (RECOMENDADA) ⭐**
**Características:**
- ✅ 500 horas gratis al mes
- ✅ PostgreSQL incluido
- ✅ Deploy automático desde GitHub
- ✅ SSL/HTTPS gratis
- ✅ Dominio personalizado gratis

**Pasos para desplegar:**

1. **Preparar el proyecto**
```bash
# Crear archivo Procfile en la raíz del proyecto
echo "web: php artisan serve --host=0.0.0.0 --port=$PORT" > Procfile

# Crear archivo nixpacks.toml
echo "[phases.setup]
nixPkgs = ['php82', 'php82Extensions.pdo', 'php82Extensions.pgsql', 'nodejs']

[phases.build]
cmds = ['composer install --no-dev --optimize-autoloader', 'npm install', 'npm run build']

[start]
cmd = 'php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT'" > nixpacks.toml
```

2. **Crear repositorio en GitHub**
```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/sistema-abarrotes.git
git push -u origin main
```

3. **Desplegar en Railway**
- Ve a https://railway.app
- Conecta tu cuenta de GitHub
- Click en "New Project" → "Deploy from GitHub repo"
- Selecciona tu repositorio
- Railway detectará automáticamente que es Laravel
- Agrega PostgreSQL: Click en "+ New" → "Database" → "PostgreSQL"

4. **Configurar variables de entorno en Railway**
```env
APP_NAME="Sistema Abarrotes"
APP_ENV=production
APP_KEY=base64:TU_KEY_AQUI
APP_DEBUG=false
APP_URL=https://tu-app.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

BUSINESS_RUC=80000000-0
BUSINESS_PHONE=0000-000000
BUSINESS_SLOGAN="Gracias por su preferencia"
```

---

### **Opción 2: Render.com**
**Características:**
- ✅ 750 horas gratis al mes
- ✅ PostgreSQL gratis (90 días, luego expira)
- ✅ SSL/HTTPS gratis
- ⚠️ Se duerme después de 15 min de inactividad

**Pasos:**
1. Crear cuenta en https://render.com
2. New → Web Service
3. Conectar GitHub
4. Build Command: `composer install && npm install && npm run build`
5. Start Command: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

---

### **Opción 3: Vercel + Supabase (PostgreSQL)**
**Para frontend estático + API:**
- Vercel: Frontend (gratis ilimitado)
- Supabase: Base de datos PostgreSQL (500MB gratis)

---

## 🔧 Optimizaciones Necesarias

### 1. **Optimizar Composer**
```bash
composer install --optimize-autoloader --no-dev
```

### 2. **Cachear Configuraciones**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. **Optimizar Assets**
```bash
npm run build
```

### 4. **Configurar .env para producción**
```env
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

---

## 📦 Preparación del Proyecto

### **Archivo `.gitignore` (verificar que incluya):**
```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
```

### **Crear `database/seeders/DatabaseSeeder.php` para datos iniciales:**
```php
public function run()
{
    // Crear usuario admin por defecto
    \App\Models\User::create([
        'name' => 'Administrador',
        'email' => 'admin@sistema.com',
        'password' => bcrypt('admin123'),
    ]);
}
```

---

## 🔄 Actualización Continua

### **Método 1: GitHub + Railway (Automático)**
```bash
# Hacer cambios en tu código
git add .
git commit -m "Descripción de cambios"
git push origin main
# Railway detecta el push y redespliega automáticamente
```

### **Método 2: Actualización Manual**
```bash
# En el servidor
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 Dominio Personalizado Gratis

### **Opción 1: Subdominio de Railway**
- `tu-sistema.up.railway.app` (gratis)

### **Opción 2: Dominio propio con Freenom**
1. Registrar dominio gratis en https://freenom.com (.tk, .ml, .ga)
2. Configurar DNS en Railway/Render
3. Agregar dominio en configuración del proyecto

### **Opción 3: Cloudflare (Recomendado)**
- Usar Cloudflare para DNS + CDN + SSL
- Gratis para siempre

---

## 📊 Monitoreo Gratuito

### **UptimeRobot**
- https://uptimerobot.com
- Monitorea si tu sitio está activo
- Notificaciones por email
- 50 monitores gratis

### **Google Analytics**
- Estadísticas de uso
- Completamente gratis

---

## 🔐 Seguridad en Producción

### **Checklist:**
- ✅ `APP_DEBUG=false`
- ✅ `APP_ENV=production`
- ✅ HTTPS habilitado (automático en Railway/Render)
- ✅ Cambiar `APP_KEY` (ejecutar `php artisan key:generate`)
- ✅ Usar contraseñas fuertes en `.env`
- ✅ Configurar CORS si es necesario
- ✅ Habilitar rate limiting en rutas

---

## 💾 Backup de Base de Datos

### **Backup Manual en Railway:**
```bash
# Conectarse a la base de datos
railway run psql $DATABASE_URL

# Exportar
pg_dump -h HOST -U USER -d DATABASE > backup.sql

# Importar
psql -h HOST -U USER -d DATABASE < backup.sql
```

### **Backup Automático:**
- Usar servicios como **Supabase** (backups automáticos)
- O configurar cron job para backups diarios

---

## 📱 Acceso desde Cualquier Dispositivo

Una vez desplegado:
- **PC**: https://tu-sistema.railway.app
- **Móvil**: Misma URL, diseño responsive
- **Tablet**: Funciona perfectamente

---

## 🎯 Resumen de Costos

| Servicio | Costo Mensual | Límites |
|----------|---------------|---------|
| Railway | $0 | 500 horas/mes |
| Render | $0 | 750 horas/mes |
| PostgreSQL (Railway) | $0 | Incluido |
| SSL/HTTPS | $0 | Incluido |
| Dominio Railway | $0 | Subdominio |
| **TOTAL** | **$0** | **Gratis** |

---

## 🚀 Próximos Pasos

1. ✅ Optimizar el código (ya hecho)
2. 📝 Crear repositorio en GitHub
3. 🌐 Desplegar en Railway
4. 🔐 Configurar variables de entorno
5. 📊 Configurar monitoreo
6. 🎉 ¡Listo para usar!

---

## 📞 Soporte

Si tienes problemas:
- Railway Docs: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs
- PostgreSQL Docs: https://www.postgresql.org/docs/

---

**¡Tu sistema estará disponible 24/7 de forma gratuita!** 🎉

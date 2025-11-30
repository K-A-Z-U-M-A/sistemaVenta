# 🚀 Guía Paso a Paso: Vercel + Supabase (100% Gratis)

## PARTE 1: Configurar Supabase (Base de Datos PostgreSQL)

### Paso 1: Crear cuenta en Supabase
1. Ve a https://supabase.com
2. Click en **"Start your project"**
3. Inicia sesión con GitHub (usa la misma cuenta: K-A-Z-U-M-A)

### Paso 2: Crear nuevo proyecto
1. Click en **"New project"**
2. Completa:
   - **Name**: `sistema-abarrotes`
   - **Database Password**: Crea una contraseña fuerte (guárdala)
   - **Region**: South America (São Paulo) - más cercano a Paraguay
   - **Pricing Plan**: **Free** (ya seleccionado)
3. Click en **"Create new project"**
4. Espera 2-3 minutos mientras se crea

### Paso 3: Obtener credenciales de la base de datos
1. En tu proyecto, ve al menú lateral → **"Project Settings"** (ícono de engranaje)
2. Click en **"Database"**
3. Busca la sección **"Connection string"**
4. Selecciona **"URI"** y copia la cadena completa
5. Se verá así:
   ```
   postgresql://postgres:[TU-PASSWORD]@db.abc123xyz.supabase.co:5432/postgres
   ```

### Paso 4: Guardar credenciales
Anota estos datos (los necesitarás después):
- **Host**: `db.abc123xyz.supabase.co`
- **Port**: `5432`
- **Database**: `postgres`
- **Username**: `postgres`
- **Password**: `[la que creaste]`

---

## PARTE 2: Preparar el proyecto para Vercel

### Paso 5: Crear archivo de configuración para Vercel

Voy a crear los archivos necesarios automáticamente...

---

## PARTE 3: Desplegar en Vercel

### Paso 6: Crear cuenta en Vercel
1. Ve a https://vercel.com
2. Click en **"Sign Up"**
3. Selecciona **"Continue with GitHub"**
4. Autoriza a Vercel

### Paso 7: Importar proyecto
1. En Vercel, click en **"Add New..."** → **"Project"**
2. Busca tu repositorio: **"K-A-Z-U-M-A/sistemaVenta"**
3. Click en **"Import"**

### Paso 8: Configurar el proyecto
1. **Framework Preset**: Selecciona "Other"
2. **Root Directory**: Dejar en blanco (`.`)
3. **Build Command**: 
   ```bash
   composer install --no-dev --optimize-autoloader && npm install && npm run build
   ```
4. **Output Directory**: `public`
5. **Install Command**: Dejar por defecto

### Paso 9: Agregar Variables de Entorno
Click en **"Environment Variables"** y agrega:

```
APP_NAME=Sistema Abarrotes
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERADO_AUTOMATICAMENTE
APP_URL=https://tu-proyecto.vercel.app

DB_CONNECTION=pgsql
DB_HOST=db.abc123xyz.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=TU_PASSWORD_DE_SUPABASE

SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

BUSINESS_RUC=80000000-0
BUSINESS_PHONE=0981-123456
BUSINESS_SLOGAN=Gracias por su preferencia
```

**IMPORTANTE**: Reemplaza:
- `db.abc123xyz.supabase.co` con tu host de Supabase
- `TU_PASSWORD_DE_SUPABASE` con tu contraseña

### Paso 10: Deploy
1. Click en **"Deploy"**
2. Vercel comenzará a construir tu proyecto
3. Espera 3-5 minutos

### Paso 11: Ejecutar migraciones
Una vez desplegado:
1. Ve a tu proyecto en Vercel
2. Click en la pestaña **"Settings"**
3. En el menú lateral, click en **"Functions"**
4. Necesitarás ejecutar las migraciones manualmente

**Opción A: Desde tu PC**
```bash
# Configurar conexión a Supabase en tu .env local
php artisan migrate --force
```

**Opción B: Desde Supabase SQL Editor**
1. Ve a Supabase → SQL Editor
2. Ejecuta las migraciones SQL manualmente

---

## PARTE 4: Verificar que funciona

### Paso 12: Acceder a tu sistema
1. Tu URL será algo como: `https://sistema-venta.vercel.app`
2. Abre esa URL en tu navegador
3. Deberías ver tu sistema funcionando

---

## 🎉 ¡LISTO!

Tu sistema ahora está:
- ✅ Desplegado en Vercel (GRATIS para siempre)
- ✅ Base de datos en Supabase (GRATIS para siempre)
- ✅ SSL/HTTPS automático
- ✅ Accesible desde cualquier dispositivo
- ✅ Sin límites de tiempo
- ✅ Sin mensualidades

---

## 🔄 Para actualizar en el futuro

Cada vez que hagas cambios:
```bash
git add .
git commit -m "Descripción de cambios"
git push origin main
```

Vercel detectará el cambio y desplegará automáticamente en 2-3 minutos.

---

## 📊 Monitoreo

- **Vercel Dashboard**: Ver logs, analytics, dominios
- **Supabase Dashboard**: Ver base de datos, hacer backups

---

## 🆘 Solución de problemas

Si algo no funciona:
1. Revisa los logs en Vercel → Deployments → [tu deploy] → View Function Logs
2. Verifica las variables de entorno
3. Asegúrate de que las credenciales de Supabase sean correctas

---

**¿Listo para empezar?** Sigue los pasos en orden y avísame si tienes alguna duda! 🚀

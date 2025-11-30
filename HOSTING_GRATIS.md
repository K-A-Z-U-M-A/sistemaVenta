# 🆓 Opciones de Hosting 100% GRATUITO (Sin Pagar Nunca)

## ⭐ OPCIÓN 1: Render.com (RECOMENDADA)

### **Características:**
- ✅ **GRATIS para siempre** (plan Free)
- ✅ PostgreSQL incluido gratis
- ✅ SSL/HTTPS automático
- ✅ Deploy desde GitHub
- ⚠️ Se "duerme" después de 15 minutos sin uso (tarda 30-60 seg en despertar)
- ⚠️ Base de datos expira cada 90 días (debes recrearla, pero es gratis)

### **Pasos para desplegar:**

1. **Ir a Render.com**
   - https://render.com
   - Click en "Get Started for Free"
   - Conecta tu cuenta de GitHub

2. **Crear Web Service**
   - Click en "New +" → "Web Service"
   - Conecta tu repositorio: `K-A-Z-U-M-A/sistemaVenta`
   - Configuración:
     ```
     Name: sistema-abarrotes
     Region: Oregon (US West)
     Branch: main
     Runtime: Docker
     Build Command: composer install --no-dev && php artisan key:generate && npm install && npm run build
     Start Command: php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
     Plan: Free
     ```

3. **Crear PostgreSQL Database**
   - Click en "New +" → "PostgreSQL"
   - Name: `sistema-db`
   - Database: `sistema_abarrotes`
   - User: `sistema_user`
   - Region: Oregon (US West)
   - Plan: **Free**

4. **Conectar Database al Web Service**
   - En tu Web Service, ve a "Environment"
   - Agrega estas variables:
     ```
     APP_NAME=Sistema Abarrotes
     APP_ENV=production
     APP_DEBUG=false
     APP_KEY=base64:GENERADO_AUTO
     
     DB_CONNECTION=pgsql
     DB_HOST=[Copiar de tu PostgreSQL]
     DB_PORT=5432
     DB_DATABASE=sistema_abarrotes
     DB_USERNAME=[Copiar de tu PostgreSQL]
     DB_PASSWORD=[Copiar de tu PostgreSQL]
     
     BUSINESS_RUC=80000000-0
     BUSINESS_PHONE=0981-123456
     BUSINESS_SLOGAN=Gracias por su preferencia
     ```

5. **Deploy**
   - Render desplegará automáticamente
   - Tu URL será: `https://sistema-abarrotes.onrender.com`

---

## 🌟 OPCIÓN 2: InfinityFree + Clever Cloud PostgreSQL

### **InfinityFree (Hosting PHP):**
- ✅ **GRATIS ilimitado**
- ✅ PHP 8.1
- ✅ Sin anuncios
- ✅ SSL gratis
- ⚠️ No soporta PostgreSQL directamente

### **Clever Cloud (PostgreSQL):**
- ✅ **GRATIS** (plan Tiny Spaces)
- ✅ PostgreSQL gratis
- ✅ 256MB RAM

**Pasos:**
1. Hosting en InfinityFree: https://infinityfree.net
2. Base de datos en Clever Cloud: https://www.clever-cloud.com
3. Conectar ambos servicios

---

## 🚀 OPCIÓN 3: Vercel + Supabase (MEJOR PARA LARGO PLAZO)

### **Vercel (Frontend + API):**
- ✅ **GRATIS ilimitado**
- ✅ Deploy automático desde GitHub
- ✅ SSL/HTTPS
- ✅ CDN global
- ✅ Sin límite de tiempo

### **Supabase (PostgreSQL):**
- ✅ **GRATIS para siempre**
- ✅ 500MB de base de datos
- ✅ Sin expiración
- ✅ Backups automáticos

### **Pasos:**

1. **Crear proyecto en Supabase**
   - https://supabase.com
   - Click en "Start your project"
   - Crear nuevo proyecto
   - Copiar credenciales de PostgreSQL

2. **Configurar Vercel**
   - https://vercel.com
   - Importar repositorio de GitHub
   - Framework Preset: "Other"
   - Build Command: `composer install && npm run build`
   - Output Directory: `public`

3. **Variables de entorno en Vercel**
   ```
   APP_NAME=Sistema Abarrotes
   APP_ENV=production
   DB_CONNECTION=pgsql
   DB_HOST=[De Supabase]
   DB_PORT=5432
   DB_DATABASE=[De Supabase]
   DB_USERNAME=[De Supabase]
   DB_PASSWORD=[De Supabase]
   ```

---

## 🎯 OPCIÓN 4: 000webhost (TODO EN UNO)

### **Características:**
- ✅ **100% GRATIS**
- ✅ PHP 8.1
- ✅ MySQL incluido (no PostgreSQL)
- ✅ 300MB espacio
- ✅ Sin anuncios
- ⚠️ Debes usar MySQL en lugar de PostgreSQL

### **Pasos:**
1. Ir a https://www.000webhost.com
2. Crear cuenta gratis
3. Subir archivos vía FTP
4. Configurar `.env` para usar MySQL

**Cambios necesarios:**
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=id_tu_db
DB_USERNAME=id_tu_user
DB_PASSWORD=tu_password
```

---

## 📊 COMPARACIÓN DE OPCIONES GRATUITAS

| Servicio | Costo | PostgreSQL | Límites | Recomendación |
|----------|-------|------------|---------|---------------|
| **Render.com** | $0 | ✅ Sí (90 días) | Se duerme 15min | ⭐⭐⭐⭐⭐ |
| **Vercel + Supabase** | $0 | ✅ Sí (ilimitado) | Sin límites | ⭐⭐⭐⭐⭐ |
| **000webhost** | $0 | ❌ Solo MySQL | 300MB | ⭐⭐⭐ |
| **InfinityFree** | $0 | ❌ Solo MySQL | Ilimitado | ⭐⭐⭐ |

---

## 🏆 MI RECOMENDACIÓN FINAL

### **Para uso inmediato: Render.com**
- Más fácil de configurar
- Todo en un solo lugar
- Solo necesitas recrear la DB cada 90 días

### **Para largo plazo: Vercel + Supabase**
- Sin límites de tiempo
- Base de datos permanente
- Más confiable
- Requiere más configuración inicial

---

## 🔄 ALTERNATIVA: Hosting Local + Túnel

Si quieres **control total y 100% gratis**:

1. **Ejecutar en tu PC**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Exponer a internet con ngrok (GRATIS)**
   - https://ngrok.com
   - `ngrok http 8000`
   - Te da una URL pública: `https://abc123.ngrok.io`

**Ventajas:**
- ✅ 100% gratis
- ✅ Control total
- ✅ Sin límites

**Desventajas:**
- ⚠️ Tu PC debe estar encendida
- ⚠️ URL cambia cada vez que reinicias ngrok

---

## 🎯 ¿Cuál elijo?

**Si quieres algo rápido y fácil:** → **Render.com**

**Si quieres algo permanente:** → **Vercel + Supabase**

**Si solo lo usarás tú localmente:** → **ngrok**

---

¿Cuál prefieres que configuremos? Te guío paso a paso! 🚀

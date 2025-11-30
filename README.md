# 🚀 Sistema de Ventas para Abarrotes/Bar

Sistema completo de punto de venta desarrollado en Laravel + PostgreSQL, diseñado para gestionar ventas de comida y bebidas con sistema de descuentos especiales.

## ✨ Características Principales

### 📊 Gestión de Ventas
- ✅ Punto de venta intuitivo
- ✅ Gestión de clientes y productos
- ✅ Sistema de descuentos para tragos
- ✅ Impresión de tickets térmicos
- ✅ Múltiples formas de pago (efectivo/tarjeta)
- ✅ Gestión de pedidos por mesa

### 💰 Control de Caja
- ✅ Apertura y cierre de caja
- ✅ Balance detallado por sesión
- ✅ Distribución de ganancias (Comida vs Tragos)
- ✅ Historial completo de movimientos

### 📈 Estadísticas y Reportes
- ✅ Ventas por día de la semana
- ✅ Ventas por mes (histórico completo)
- ✅ Top 10 productos más vendidos
- ✅ Gráficos interactivos con Chart.js
- ✅ Exportación a Excel (CSV)
- ✅ Generación de PDF profesional

### 📦 Inventario
- ✅ Control de stock en tiempo real
- ✅ Alertas de stock bajo
- ✅ Categorización de productos
- ✅ Precios de compra y venta

### 👥 Gestión de Usuarios
- ✅ Sistema de roles y permisos
- ✅ Autenticación segura
- ✅ Registro de actividades

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 10.x
- **Base de Datos**: PostgreSQL
- **Frontend**: Bootstrap 5, Chart.js
- **Autenticación**: Laravel Breeze
- **Impresión**: ESC/POS (tickets térmicos)

## 📋 Requisitos

- PHP 8.1 o superior
- PostgreSQL 12 o superior
- Composer
- Node.js y NPM

## 🚀 Instalación Local

```bash
# Clonar repositorio
git clone https://github.com/TU_USUARIO/sistema-abarrotes.git
cd sistema-abarrotes

# Instalar dependencias PHP
composer install

# Instalar dependencias JavaScript
npm install

# Copiar archivo de configuración
cp .env.example .env

# Generar key de aplicación
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=sistema_abarrotes
# DB_USERNAME=postgres
# DB_PASSWORD=tu_password

# Ejecutar migraciones
php artisan migrate

# Crear usuario admin (opcional)
php artisan db:seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## 🌐 Despliegue en Producción

Ver guía completa en [DEPLOYMENT.md](DEPLOYMENT.md)

### Opciones Gratuitas Recomendadas:
1. **Railway.app** (Recomendado) - 500 horas/mes gratis
2. **Render.com** - 750 horas/mes gratis
3. **Vercel + Supabase** - Ilimitado

## 📸 Capturas de Pantalla

### Panel de Ventas
- Interfaz moderna y responsive
- Búsqueda rápida de productos
- Cálculo automático de totales

### Estadísticas
- Gráficos de barras por día/mes
- Separación Comida vs Tragos
- Exportación a PDF/Excel

### Tickets
- Diseño térmico profesional
- Precios netos (sin descuentos visibles)
- Información completa de la venta

## 🔐 Seguridad

- ✅ Protección CSRF
- ✅ Validación de datos
- ✅ Sanitización de inputs
- ✅ Autenticación robusta
- ✅ HTTPS en producción

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## 👨‍💻 Autor

Desarrollado con ❤️ para pequeños negocios

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📞 Soporte

Si tienes preguntas o necesitas ayuda:
- Abre un issue en GitHub
- Consulta la documentación de Laravel
- Revisa la guía de despliegue

---

**¡Gracias por usar nuestro sistema!** 🎉
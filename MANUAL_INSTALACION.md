# Manual de Instalación del Sistema de Abarrotes

Este documento detalla los pasos necesarios para instalar y ejecutar el sistema en una nueva máquina (Windows/Linux/Mac).

## 1. Requisitos Previos

Asegúrate de tener instalado el siguiente software en tu equipo:

*   **PHP** (Versión 8.1 o superior).
*   **Composer** (Gestor de dependencias de PHP).
*   **Node.js** y **NPM** (Para compilar los estilos y scripts).
*   **Base de Datos**: MySQL (Recomendado) o PostgreSQL.
*   **Git** (Opcional, para clonar el repositorio).

## 2. Instalación Paso a Paso

### Paso 1: Obtener el Código
Copia la carpeta del proyecto a tu nueva máquina o clona el repositorio si usas Git.

### Paso 2: Instalar Dependencias de Backend (Laravel)
Abre una terminal en la carpeta raíz del proyecto y ejecuta:
```bash
composer install
```

### Paso 3: Instalar Dependencias de Frontend
En la misma terminal, ejecuta:
```bash
npm install
```
Luego, compila los archivos estáticos:
```bash
npm run build
```

### Paso 4: Configuración del Entorno (.env)
1.  Duplica el archivo `.env.example` y renómbralo a `.env`.
    *   En Windows (PowerShell): `cp .env.example .env`
    *   O hazlo manualmente desde el explorador de archivos.
2.  Abre el archivo `.env` y configura tu base de datos. Ejemplo para MySQL:
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=sistema_abarrotes
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    *Asegúrate de crear una base de datos vacía con el nombre que pongas en `DB_DATABASE`.*

### Paso 5: Generar Clave de Aplicación
Ejecuta el siguiente comando para generar la llave de encriptación:
```bash
php artisan key:generate
```

### Paso 6: Migración y Datos Iniciales (IMPORTANTE)
Este comando creará todas las tablas necesarias (incluyendo productos, categorías, ventas, compras) y configurará el usuario administrador por defecto.

```bash
php artisan migrate:fresh --seed
```

> **Nota:** Esto borrará cualquier dato existente en la base de datos configurada y la volverá a crear desde cero con los datos base.

### Paso 7: Crear Enlace Simbólico para Imágenes
Para que las imágenes de los productos sean visibles:
```bash
php artisan storage:link
```

## 3. Ejecución del Sistema

Para iniciar el servidor local, ejecuta:
```bash
php artisan serve
```
El sistema estará accesible en: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 4. Credenciales de Acceso

El sistema viene configurado con el siguiente usuario administrador:

*   **Usuario:** `admin@gmail.com`
*   **Contraseña:** `admin123`

---

## Solución de Problemas Comunes

*   **Error de Permisos:** Si tienes errores de escritura en `storage` o `bootstrap/cache`, asegúrate de dar permisos de escritura a esas carpetas.
*   **Error de Base de Datos:** Verifica que el servicio de MySQL/PostgreSQL esté corriendo y que las credenciales en el archivo `.env` sean correctas.
*   **Estilos rotos:** Si la página se ve sin diseño, asegúrate de haber ejecutado `npm run build`.

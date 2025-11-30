# 🍹 Sistema de Gestión de Bar - Instrucciones

## ✅ Cambios Implementados

### 1. 🔒 Validación de Caja Abierta
Ahora **NO se puede crear una venta sin antes abrir la caja**. 

**¿Cómo funciona?**
- Al intentar crear una venta sin caja abierta, aparecerá un mensaje de error:
  > "Debe abrir la caja antes de realizar una venta. Por favor, diríjase a la sección de Caja."
- El sistema redirigirá automáticamente a la lista de ventas
- Debes ir a la sección **Caja** en el menú lateral y abrir la caja antes de vender

### 2. 📊 Nuevos Filtros de Búsqueda

#### Filtros en **Ventas**:
- 📅 **Rango de Fechas** (Desde/Hasta)
- 📋 **Estado del Pedido** (Pendiente, Preparación, Completado, Entregado, Cancelado)
- 💳 **Forma de Pago** (Efectivo, Tarjeta, Transferencia, Mixto)
- 👤 **Vendedor** (Seleccionar por usuario)
- 🔍 **Búsqueda de Cliente/Pedido** (Por nombre)

#### Filtros en **Compras**:
- 📅 **Rango de Fechas** (Desde/Hasta)
- 🚚 **Proveedor** (Seleccionar proveedor específico)

#### Filtros en **Productos**:
- 🔍 **Búsqueda** - Por nombre o código del producto
- 🍽️ **Tipo de Producto** - Comida, Bebida o Trago
- 🏷️ **Categoría** - Filtrar por categoría específica
- 🏭 **Marca** - Filtrar por marca
- 📦 **Presentación** - Filtrar por tipo de presentación
- 📉 **Stock Bajo** - Productos con stock menor o igual a un valor
- ✅ **Estado** - Activo o Eliminado

### Características de los Filtros:
- ✨ Panel colapsable con diseño moderno
- 🎨 Badges de colores mostrando filtros activos
- 🔄 Persistencia de valores seleccionados
- 🧹 Botón "Limpiar" para resetear filtros

### 3. 📋 Nuevo Menú Cargado

Se han cargado **48 productos** del menú del bar:

#### 🍹 Tragos (24 productos)
- **Tragos Clásicos**: Caipirinha, Caipiroska, Caipiruva, Mojito, Aperol, Piña Colada, Tequila Sunrise, Cuba Libre, Destornillador, Russo Negro
- **Daiquiris**: Frutilla, Durazno, Piña
- **Sangrías**: Clásico, TuttiFrost
- **Otros**: Fernet Cola, Whiscola, Gin Tonic, Machu Picchu, Green Frost, Electric
- **Shots**: Tequila, Jagger, Bob Marley

#### 🍕 Comidas (15 productos)
- **Papas**: Tradicionales, Con Cheddar y Panceta, Salchipapa
- **Pizzas**: Muzzarella, Pepperoni, Aceituna, Cheddar y Panceta, Doggies
- **Hot Dogs**: Clásico, Cheddar y Bacon, Pizza Pepperoni, Pizza Aceituna
- **Combos**: Clásico, Cheddar y Bacon, Pizzadog

#### 🥤 Bebidas (9 productos)
- **Gaseosas**: Coca Cola (1L, 500ml, 250ml)
- **Aguas**: Con gas, Sin gas
- **Cervezas**: Münich Ultra, Heineken Silver, Pilsen, Miller

### 4. 💰 Precios sin IVA
Todos los productos se han cargado **SIN IVA** (impuesto = 0)

---

## 🚀 Cómo Usar el Sistema

### Paso 1: Abrir la Caja
1. Ve al menú lateral → **Caja**
2. Haz clic en **"Abrir Caja"**
3. Ingresa el monto inicial en efectivo
4. Confirma la apertura

### Paso 2: Realizar Ventas
1. Ve al menú lateral → **Ventas** → **Crear**
2. Selecciona los productos del menú
3. Elige la forma de pago
4. Completa la venta

### Paso 3: Usar Filtros
1. En la lista de ventas, compras o productos, verás el panel de **"Filtros de Búsqueda"**
2. Selecciona los filtros que necesites
3. Haz clic en **"Buscar"**
4. Para limpiar los filtros, haz clic en **"Limpiar"**

### Paso 4: Ver Reportes
1. Ve al menú lateral → **Estadísticas**
2. Visualiza gráficas y reportes de ventas
3. Ve al menú lateral → **Inventario**
4. Revisa el stock de productos

### Paso 5: Cerrar la Caja
1. Ve al menú lateral → **Caja**
2. Haz clic en **"Cerrar Caja"**
3. Ingresa el monto final contado
4. El sistema calculará automáticamente la diferencia

---

## 🔄 Recargar Datos del Menú

Si necesitas volver a cargar los datos del menú (esto **borrará todos los productos existentes**):

```bash
php artisan db:seed --class=MenuBarSeeder
```

**⚠️ ADVERTENCIA**: Este comando eliminará todos los productos actuales y los reemplazará con el menú del bar.

---

## 📱 Secciones del Menú Lateral

- 🏠 **Panel** - Dashboard principal
- 🛒 **Compras** - Gestión de compras a proveedores (con filtros)
- 🛍️ **Ventas** - Gestión de ventas (con filtros)
- 🏦 **Caja** - Apertura/cierre de caja
- 📦 **Inventario** - Control de stock
- 📊 **Estadísticas** - Reportes y gráficas
- 🏷️ **Categorías** - Gestión de categorías
- 📦 **Presentaciones** - Tipos de presentación
- 🏭 **Marcas** - Gestión de marcas
- 🛍️ **Productos** - Gestión de productos (con filtros)
- 👥 **Clientes** - Gestión de clientes
- 🚚 **Proveedores** - Gestión de proveedores
- 👤 **Usuarios** - Gestión de usuarios (solo admin)
- 🔐 **Roles** - Gestión de permisos (solo admin)

---

## 💡 Consejos

1. **Siempre abre la caja** al inicio del turno
2. **Usa los filtros** para encontrar registros específicos rápidamente
3. **Filtra por stock bajo** en Productos para saber qué reponer
4. **Revisa el inventario** regularmente
5. **Cierra la caja** al final del turno para llevar un control exacto
6. Los **tragos con descuento** tienen la opción `aplica_descuento_trago = true`
7. Los **shots NO aplican descuento** (`aplica_descuento_trago = false`)

---

## 🆘 Soporte

Si tienes problemas:
1. Verifica que la caja esté abierta antes de vender
2. Revisa los filtros activos (pueden estar ocultando resultados)
3. Asegúrate de tener los permisos necesarios
4. Recarga la página (F5)
5. Usa el filtro de "Stock Bajo" para encontrar productos con poco inventario

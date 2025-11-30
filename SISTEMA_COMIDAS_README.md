# Sistema de Ventas para Local de Comidas y Bebidas

## 🍔 Descripción
Sistema completo para gestionar ventas de comidas, bebidas y tragos con funcionalidades especiales para el manejo de descuentos en tragos y gestión de pedidos.

## ✨ Nuevas Funcionalidades

### 1. **Tipos de Productos**
Los productos ahora se clasifican en tres categorías:
- 🍕 **Comida**
- 🥤 **Bebida**
- 🍹 **Trago**

### 2. **Sistema de Descuentos de Tragos**
- Ciertos tragos tienen un descuento automático de **5,000 Gs** (Guaraníes)
- Este descuento se resta del precio del trago
- Los 5,000 Gs se transfieren a la ganancia de comida
- El sistema registra y muestra estos movimientos para auditoría

**Cómo funciona:**
1. Al crear un producto tipo "trago", puedes marcar si aplica descuento
2. Al realizar una venta con tragos con descuento, automáticamente:
   - Se descuentan 5,000 Gs del precio del trago
   - Se suman 5,000 Gs a la ganancia de comida
   - Se registra en la venta para trazabilidad

### 3. **Estados de Pedido**
Cada venta/pedido puede tener tres estados:
- ⏳ **Pendiente**: Pedido recién creado
- ✅ **Completado**: Pedido entregado
- ❌ **Cancelado**: Pedido cancelado

### 4. **Sistema de Devoluciones**
Permite realizar devoluciones de dos tipos:

#### Devolución Total
- Devuelve toda la venta completa
- Restaura el stock de todos los productos
- Registra el monto total devuelto

#### Devolución Parcial
- Selecciona productos específicos a devolver
- Indica la cantidad de cada producto
- Restaura solo el stock de los productos devueltos
- Calcula el monto proporcional

### 5. **Impresión de Tickets**
Sistema de impresión para impresoras térmicas portátiles (58mm):

#### Ticket de Venta
- Información completa de la venta
- Detalle de productos con tipo (comida/bebida/trago)
- Muestra descuentos de tragos aplicados
- Ganancia transferida a comida
- Total con formato de moneda paraguaya (Gs)

#### Ticket de Cocina
- Formato simplificado para cocina
- Productos agrupados por tipo
- Número de mesa
- Cantidades destacadas
- Notas especiales del pedido

### 6. **Gestión de Mesas**
- Asigna número de mesa a cada pedido
- Facilita el seguimiento de pedidos por mesa

### 7. **Notas de Pedido**
- Agrega notas especiales a cada pedido
- Útil para instrucciones de preparación
- Se muestra en tickets de cocina

## 📊 Nuevas Tablas de Base de Datos

### Modificaciones a `productos`
```sql
- tipo_producto: enum('comida', 'bebida', 'trago')
- aplica_descuento_trago: boolean
```

### Modificaciones a `ventas`
```sql
- estado_pedido: enum('pendiente', 'completado', 'cancelado')
- numero_mesa: varchar(20)
- descuento_tragos: decimal(10,2)
- ganancia_tragos_a_comida: decimal(10,2)
- notas: text
```

### Modificaciones a `producto_venta`
```sql
- es_trago_con_descuento: boolean
- descuento_trago: decimal(10,2)
```

### Nueva tabla `devoluciones`
```sql
- id
- venta_id
- user_id
- fecha_hora
- monto_devuelto
- motivo
- tipo: enum('total', 'parcial')
```

### Nueva tabla `devolucion_items`
```sql
- id
- devolucion_id
- producto_id
- cantidad
- precio_unitario
- subtotal
```

## 🚀 Instalación y Configuración

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

### 2. Configurar Impresora (Opcional)
Edita el archivo `.env` y agrega:
```env
TICKET_PRINTER_NAME=POS-58
BUSINESS_RUC=80000000-0
BUSINESS_PHONE=0981-123456
BUSINESS_SLOGAN=¡Gracias por su preferencia!
```

### 3. Instalar Librería de Impresión (Si usarás impresora)
```bash
composer require mike42/escpos-php
```

## 📝 Uso del Sistema

### Crear un Producto con Descuento de Trago
1. Ir a Productos → Nuevo Producto
2. Seleccionar tipo: "Trago"
3. Marcar "Aplica descuento de trago"
4. Guardar

### Realizar una Venta
1. Ir a Ventas → Nueva Venta
2. Agregar productos (comidas, bebidas, tragos)
3. Si hay tragos con descuento, se aplicará automáticamente
4. Asignar número de mesa (opcional)
5. Agregar notas (opcional)
6. Completar venta

### Cambiar Estado de Pedido
1. En la lista de ventas, seleccionar una venta
2. Cambiar estado a: Pendiente/Completado/Cancelado

### Imprimir Tickets
1. **Ticket de Venta**: Para el cliente
2. **Ticket de Cocina**: Para la cocina

### Realizar una Devolución
1. Ir a Devoluciones → Nueva Devolución
2. Seleccionar la venta a devolver
3. Elegir tipo:
   - **Total**: Devuelve toda la venta
   - **Parcial**: Selecciona productos específicos
4. Indicar motivo
5. Confirmar

## 📈 Reportes

### Reporte de Descuentos de Tragos
Puedes generar reportes para ver:
- Total de descuentos aplicados en un período
- Ganancia transferida a comida
- Cantidad de ventas con descuentos

## 🔧 Archivos Principales Creados/Modificados

### Migraciones
- `2025_11_25_000001_add_tipo_producto_to_productos_table.php`
- `2025_11_25_000002_add_estado_pedido_to_ventas_table.php`
- `2025_11_25_000003_create_devoluciones_table.php`
- `2025_11_25_000004_create_devolucion_items_table.php`
- `2025_11_25_000005_add_descuento_trago_to_producto_venta_table.php`

### Modelos
- `app/Models/Devolucion.php`
- `app/Models/DevolucionItem.php`
- `app/Models/Producto.php` (modificado)
- `app/Models/Venta.php` (modificado)

### Controladores
- `app/Http/Controllers/DevolucionController.php`
- `app/Http/Controllers/ventaController.php` (modificado)

### Servicios
- `app/Services/TragoDescuentoService.php`
- `app/Services/TicketPrinterService.php`

### Rutas
- `routes/web.php` (modificado)

## 🎯 Próximos Pasos

1. **Ejecutar las migraciones**:
   ```bash
   php artisan migrate
   ```

2. **Crear las vistas** para:
   - Gestión de productos con tipos
   - Lista de ventas con estados
   - Formulario de devoluciones
   - Cambio de estado de pedidos

3. **Configurar la impresora** si deseas usar la funcionalidad de impresión

4. **Personalizar el diseño** del login y las vistas según el estilo del local

## 💡 Notas Importantes

- El descuento de 5,000 Gs es una constante definida en `TragoDescuentoService`
- Puedes modificar este valor editando la constante `DESCUENTO_TRAGO`
- La impresión requiere una impresora térmica compatible con ESC/POS
- Los tickets están optimizados para impresoras de 58mm

## 🆘 Soporte

Si necesitas ayuda o tienes preguntas sobre alguna funcionalidad, revisa el código de los servicios y controladores que contienen comentarios detallados.

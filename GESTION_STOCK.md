# 📦 GESTIÓN AUTOMÁTICA DE STOCK

## ✅ Funcionamiento Implementado

### 1. **Al Crear una Venta**
```
Acción: Se crea una nueva venta con productos
Efecto en Stock: ⬇️ DESCUENTA automáticamente
```
- Cada producto vendido reduce su stock por la cantidad vendida
- Ejemplo: Vendo 3 cervezas → Stock de cervezas baja en 3 unidades

---

### 2. **Al Anular una Venta (Botón "Anular")**
```
Acción: Se anula/cancela una venta
Efecto en Stock: ⬆️ DEVUELVE automáticamente
```
- Todos los productos de la venta regresan al inventario
- La venta se marca como `estado = 0` (anulada)
- El `estado_pedido` cambia a "cancelado"
- Ejemplo: Anulo venta de 3 cervezas → Stock de cervezas sube en 3 unidades

---

### 3. **Al Cambiar Estado a "Cancelado"**
```
Acción: Se marca un pedido como cancelado
Efecto en Stock: ⬆️ DEVUELVE automáticamente
```
- Funciona igual que "Anular"
- El stock se restaura completamente
- No se puede volver a cancelar un pedido ya cancelado

---

### 4. **Al Reactivar un Pedido Cancelado**
```
Acción: Se cambia de "cancelado" a cualquier otro estado
Efecto en Stock: ⬇️ DESCUENTA nuevamente
```
- **Validación importante:** Verifica que haya stock disponible
- Si no hay stock suficiente, muestra error y no permite reactivar
- Si hay stock, descuenta nuevamente las cantidades
- Ejemplo: Reactivo venta de 3 cervezas → Stock baja en 3 (si hay disponibles)

---

### 5. **Al Cambiar entre Estados Normales**
```
Acción: Cambiar de "pendiente" a "completado", "entregado", etc.
Efecto en Stock: ➖ SIN CAMBIOS
```
- Los cambios de estado normales NO afectan el stock
- El stock ya fue descontado al crear la venta
- Estados normales: pendiente, preparacion, completado, entregado

---

## 🔒 Validaciones de Seguridad

### ✅ Al Crear Venta
- Verifica que la cantidad no supere el stock disponible
- Muestra error si no hay suficiente stock

### ✅ Al Reactivar Pedido Cancelado
- Verifica stock actual antes de reactivar
- Muestra mensaje específico: "No hay stock suficiente de [Producto]. Disponible: X, Necesario: Y"

### ✅ Transacciones de Base de Datos
- Todas las operaciones usan transacciones (BEGIN/COMMIT/ROLLBACK)
- Si algo falla, se revierten todos los cambios
- Garantiza integridad de datos

---

## 📊 Ejemplos Prácticos

### Ejemplo 1: Flujo Normal
```
1. Crear venta: 5 Super Panchos
   Stock antes: 20 → Stock después: 15 ✅

2. Marcar como "completado"
   Stock: 15 → Stock: 15 (sin cambios) ✅

3. Marcar como "entregado"
   Stock: 15 → Stock: 15 (sin cambios) ✅
```

### Ejemplo 2: Cancelación
```
1. Crear venta: 3 Coca-Colas
   Stock antes: 10 → Stock después: 7 ✅

2. Cliente cancela, presiono "Anular"
   Stock: 7 → Stock: 10 (devuelto) ✅
```

### Ejemplo 3: Cancelación y Reactivación
```
1. Crear venta: 2 Fernet
   Stock antes: 8 → Stock después: 6 ✅

2. Cancelar por error
   Stock: 6 → Stock: 8 (devuelto) ✅

3. Reactivar (cambiar de "cancelado" a "pendiente")
   Stock: 8 → Stock: 6 (descontado nuevamente) ✅
```

### Ejemplo 4: Intento de Reactivación sin Stock
```
1. Crear venta: 5 Cervezas
   Stock antes: 10 → Stock después: 5 ✅

2. Cancelar venta
   Stock: 5 → Stock: 10 (devuelto) ✅

3. Alguien más vende 8 cervezas
   Stock: 10 → Stock: 2 ✅

4. Intento reactivar la venta original (necesita 5)
   ❌ ERROR: "No hay stock suficiente de Cerveza. Disponible: 2, Necesario: 5"
   Stock: 2 → Stock: 2 (sin cambios) ✅
```

---

## 🎯 Recomendaciones de Uso

### ✅ Buenas Prácticas
1. **Cancelar en lugar de eliminar** - Usa "Anular" para mantener historial
2. **Verificar stock antes de reactivar** - El sistema lo hace automáticamente
3. **Revisar inventario regularmente** - Usa la sección "Inventario" para monitorear

### ⚠️ Advertencias
1. **No editar productos de una venta** - Actualmente solo se pueden editar datos de cabecera (cliente, mesa, notas)
2. **Stock negativo** - El sistema previene ventas sin stock, pero verifica manualmente si es necesario
3. **Ventas antiguas** - Ten cuidado al reactivar ventas muy antiguas, el stock pudo haber cambiado

---

## 🔧 Archivos Modificados

```
✅ app/Http/Controllers/ventaController.php
   - destroy() → Devuelve stock al anular
   - cambiarEstado() → Gestiona stock al cancelar/reactivar
   - store() → Descuenta stock al crear

✅ resources/views/venta/index.blade.php
   - Botones de acción rápida
   - Badges de estado con colores
```

---

## 📞 Soporte

Si encuentras algún problema con el stock:
1. Verifica en "Inventario" el stock actual
2. Revisa el historial de ventas
3. Verifica que no haya ventas duplicadas
4. En caso de inconsistencia, ajusta manualmente desde "Productos > Editar"

---

**Última actualización:** 25/11/2025
**Versión del sistema:** 2.0

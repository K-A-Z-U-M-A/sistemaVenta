-- ============================================
-- SCRIPT DE MIGRACIONES PARA SISTEMA DE COMIDAS
-- ============================================

-- 1. Agregar tipo de producto y descuento de trago a productos
ALTER TABLE productos 
ADD COLUMN tipo_producto VARCHAR(10) DEFAULT 'comida' CHECK (tipo_producto IN ('comida', 'bebida', 'trago')),
ADD COLUMN aplica_descuento_trago BOOLEAN DEFAULT FALSE;

-- 2. Agregar campos a ventas para gestión de pedidos
ALTER TABLE ventas
ADD COLUMN estado_pedido VARCHAR(20) DEFAULT 'pendiente' CHECK (estado_pedido IN ('pendiente', 'completado', 'cancelado')),
ADD COLUMN numero_mesa VARCHAR(20),
ADD COLUMN descuento_tragos DECIMAL(10,2) DEFAULT 0,
ADD COLUMN ganancia_tragos_a_comida DECIMAL(10,2) DEFAULT 0,
ADD COLUMN notas TEXT;

-- 3. Agregar campos a producto_venta para tracking de descuentos
ALTER TABLE producto_venta
ADD COLUMN es_trago_con_descuento BOOLEAN DEFAULT FALSE,
ADD COLUMN descuento_trago DECIMAL(10,2) DEFAULT 0;

-- 4. Crear tabla de devoluciones
CREATE TABLE devoluciones (
    id BIGSERIAL PRIMARY KEY,
    venta_id BIGINT NOT NULL,
    user_id BIGINT,
    fecha_hora TIMESTAMP NOT NULL,
    monto_devuelto DECIMAL(10,2) NOT NULL,
    motivo TEXT NOT NULL,
    tipo VARCHAR(10) DEFAULT 'total' CHECK (tipo IN ('total', 'parcial')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. Crear tabla de items de devolución
CREATE TABLE devolucion_items (
    id BIGSERIAL PRIMARY KEY,
    devolucion_id BIGINT NOT NULL,
    producto_id BIGINT NOT NULL,
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (devolucion_id) REFERENCES devoluciones(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- 6. Crear índices para mejorar el rendimiento
CREATE INDEX idx_ventas_estado_pedido ON ventas(estado_pedido);
CREATE INDEX idx_ventas_numero_mesa ON ventas(numero_mesa);
CREATE INDEX idx_productos_tipo ON productos(tipo_producto);
CREATE INDEX idx_devoluciones_venta ON devoluciones(venta_id);
CREATE INDEX idx_devoluciones_fecha ON devoluciones(fecha_hora);

-- ============================================
-- DATOS DE EJEMPLO (OPCIONAL)
-- ============================================

-- Actualizar productos existentes con tipos
-- UPDATE productos SET tipo_producto = 'comida' WHERE nombre LIKE '%pizza%' OR nombre LIKE '%hamburguesa%';
-- UPDATE productos SET tipo_producto = 'bebida' WHERE nombre LIKE '%coca%' OR nombre LIKE '%agua%';
-- UPDATE productos SET tipo_producto = 'trago', aplica_descuento_trago = TRUE WHERE nombre LIKE '%cerveza%' OR nombre LIKE '%whisky%';

COMMIT;

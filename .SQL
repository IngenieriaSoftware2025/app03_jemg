create database app03_montes

-- TABLA DE CLIENTES
CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombres VARCHAR(100) NOT NULL,
    cliente_apellidos VARCHAR(100) NOT NULL,
    cliente_nit VARCHAR(20) NOT NULL,
    cliente_telefono VARCHAR(15) NOT NULL,
    cliente_correo VARCHAR(150) NOT NULL,
    cliente_situacion SMALLINT DEFAULT 1
);


-- TABLA DE MARCAS DE CELULARES
CREATE TABLE marcas (
    marca_id SERIAL PRIMARY KEY,
    marca_nombre VARCHAR(50) NOT NULL UNIQUE,
    marca_descripcion VARCHAR(200),
    marca_situacion SMALLINT DEFAULT 1
);


-- TABLA DE USUARIOS
CREATE TABLE usuarios(
    usuario_id SERIAL PRIMARY KEY,
    usuario_nom1 VARCHAR(50) NOT NULL,
    usuario_nom2 VARCHAR(50) NOT NULL,
    usuario_ape1 VARCHAR(50) NOT NULL,
    usuario_ape2 VARCHAR(50) NOT NULL,
    usuario_tel INT NOT NULL, 
    usuario_direc VARCHAR(150) NOT NULL,
    usuario_dpi VARCHAR(13) NOT NULL,
    usuario_correo VARCHAR(100) NOT NULL,
    usuario_contrasena LVARCHAR(1056) NOT NULL,
    usuario_token LVARCHAR(1056) NOT NULL,
    usuario_fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT,
    usuario_fecha_contra DATETIME YEAR TO SECOND DEFAULT CURRENT,
    usuario_fotografia LVARCHAR(2056),
    usuario_rol VARCHAR(50) NOT NULL,
    usuario_puesto VARCHAR(50) NOT NULL,
    usuario_estado VARCHAR(50) NOT NULL,
    usuario_situacion SMALLINT DEFAULT 1
);


-- TABLA DE MODELOS DE CELULARES
CREATE TABLE modelos (
    modelo_id SERIAL PRIMARY KEY,
    marca_id INT NOT NULL,
    modelo_nombre VARCHAR(100) NOT NULL,
    modelo_descripcion VARCHAR(200),
    modelo_especificaciones TEXT,
    modelo_precio_referencia DECIMAL(10,2),
    modelo_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (marca_id) REFERENCES marcas(marca_id)
);

--------------------------------------------------------------------------------------------------------------------------------------------

-- TABLA DE SERVICIOS DE REPARACIÓN
CREATE TABLE servicios (
    servicio_id SERIAL PRIMARY KEY,
    servicio_nombre VARCHAR(100) NOT NULL,
    servicio_descripcion VARCHAR(200),
    servicio_precio DECIMAL(10,2) NOT NULL,
    servicio_tiempo_estimado INT, -- en horas
    servicio_situacion SMALLINT DEFAULT 1
);

-- TABLA DE INVENTARIO
CREATE TABLE inventario (
    inventario_id SERIAL PRIMARY KEY,
    modelo_id INT NOT NULL,
    inventario_stock_actual INT NOT NULL DEFAULT 0,
    inventario_stock_minimo INT NOT NULL DEFAULT 5,
    inventario_precio_venta DECIMAL(10,2) NOT NULL,
    inventario_precio_compra DECIMAL(10,2),
    inventario_fecha_actualizacion DATETIME YEAR TO SECOND DEFAULT CURRENT,
    inventario_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (modelo_id) REFERENCES modelos(modelo_id)
);

-- TABLA DE VENTAS
CREATE TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    venta_fecha DATETIME YEAR TO SECOND DEFAULT CURRENT,
    venta_total DECIMAL(10,2) NOT NULL,
    venta_tipo VARCHAR(20) DEFAULT 'VENTA', -- 'VENTA' o 'REPARACION'
    venta_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);

-- TABLA DE DETALLE DE VENTAS
CREATE TABLE detalle_ventas (
    detalle_id SERIAL PRIMARY KEY,
    venta_id INT NOT NULL,
    inventario_id INT,
    servicio_id INT,
    detalle_cantidad INT NOT NULL DEFAULT 1,
    detalle_precio_unitario DECIMAL(10,2) NOT NULL,
    detalle_subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(venta_id),
    FOREIGN KEY (inventario_id) REFERENCES inventario(inventario_id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(servicio_id)
);

-- TABLA DE REPARACIONES
CREATE TABLE reparaciones (
    reparacion_id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_recibe_id INT NOT NULL,
    usuario_asignado_id INT,
    reparacion_marca VARCHAR(50) NOT NULL,
    reparacion_modelo VARCHAR(100) NOT NULL,
    reparacion_problema TEXT NOT NULL,
    reparacion_fecha_recepcion DATETIME YEAR TO SECOND DEFAULT CURRENT,
    reparacion_fecha_asignacion DATETIME YEAR TO SECOND,
    reparacion_fecha_completado DATETIME YEAR TO SECOND,
    reparacion_fecha_entrega DATETIME YEAR TO SECOND,
    reparacion_estado VARCHAR(20) DEFAULT 'RECIBIDO', -- RECIBIDO, ASIGNADO, EN_PROCESO, COMPLETADO, ENTREGADO
    reparacion_observaciones TEXT,
    reparacion_costo_total DECIMAL(10,2) DEFAULT 0,
    reparacion_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
    FOREIGN KEY (usuario_recibe_id) REFERENCES usuarios(usuario_id),
    FOREIGN KEY (usuario_asignado_id) REFERENCES usuarios(usuario_id)
);


-- TABLA DE SERVICIOS POR REPARACIÓN
CREATE TABLE reparacion_servicios (
    reparacion_id INT NOT NULL,
    servicio_id INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio_aplicado DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (reparacion_id, servicio_id),
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(reparacion_id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(servicio_id)
);
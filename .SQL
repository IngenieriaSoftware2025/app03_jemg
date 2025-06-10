create database app03_montes

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


-- TABLA DE ROLES DE USUARIO
CREATE TABLE roles (
    rol_id SERIAL PRIMARY KEY,
    rol_nombre VARCHAR(50) NOT NULL UNIQUE,
    rol_descripcion VARCHAR(200),
    rol_situacion SMALLINT DEFAULT 1
);

-- TABLA DE USUARIOS
CREATE TABLE usuarios (
    usuario_id SERIAL PRIMARY KEY,
    usuario_nombre VARCHAR(100) NOT NULL,
    usuario_email VARCHAR(150) NOT NULL UNIQUE,
    usuario_password VARCHAR(255) NOT NULL,
    usuario_telefono VARCHAR(15),
    rol_id INT NOT NULL,
    usuario_situacion SMALLINT DEFAULT 1,
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id)
);
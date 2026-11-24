

CREATE DATABASE DianitasDB;

USE DianitasDB;


CREATE TABLE empleados (
    id_empleado INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(80) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    puesto ENUM('mesera', 'cocinero', 'administradora') NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120) NOT NULL,
    PRIMARY KEY (id_empleado),
    UNIQUE KEY uq_empleados_correo (correo)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id_usuario INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    id_empleado INT(10) UNSIGNED NOT NULL,
    correo VARCHAR(120) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_usuario),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
) ENGINE=InnoDB;

CREATE TABLE pedidos_cocina (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    mesa VARCHAR(20) NOT NULL,
    productos LONGTEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('Pendiente', 'Preparando', 'Terminado')
        DEFAULT 'Pendiente',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);



-- =====================================
-- TABLA DE CATEGORÍAS
-- =====================================

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1
);

-- =====================================
-- TABLA DE PRODUCTOS
-- =====================================

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    piezas_por_paquete INT NOT NULL DEFAULT 1,

    -- none: no requiere sabores
    -- por_pieza: un sabor por cada pieza
    -- por_paquete: sabores elegidos para el paquete

    modo_seleccion ENUM(
        'none',
        'por_pieza',
        'por_paquete'
    ) DEFAULT 'none',

    sabores_minimos INT DEFAULT 0,
    sabores_maximos INT DEFAULT 0,

    activo TINYINT(1) DEFAULT 1,

    FOREIGN KEY (id_categoria)
    REFERENCES categorias(id_categoria)
);

-- =====================================
-- TABLA DE SABORES
-- =====================================

CREATE TABLE sabores (
    id_sabor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1
);

-- =====================================
-- SABORES DISPONIBLES POR PRODUCTO
-- =====================================

CREATE TABLE producto_sabores (
    id_producto INT NOT NULL,
    id_sabor INT NOT NULL,

    PRIMARY KEY (id_producto, id_sabor),

    FOREIGN KEY (id_producto)
    REFERENCES productos(id_producto)
    ON DELETE CASCADE,

    FOREIGN KEY (id_sabor)
    REFERENCES sabores(id_sabor)
    ON DELETE CASCADE
);

-- =====================================
-- PRESENTACIONES Y PRECIOS
-- =====================================

CREATE TABLE presentaciones (
    id_presentacion INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    activo TINYINT(1) DEFAULT 1,

    FOREIGN KEY (id_producto)
    REFERENCES productos(id_producto)
    ON DELETE CASCADE
);

-- =====================================
-- CATEGORÍAS
-- =====================================

INSERT INTO categorias (nombre) VALUES
('Antojitos'),
('Tostadas'),
('Tacos'),
('Bebidas'),
('Extras');

-- =====================================
-- SABORES
-- =====================================

INSERT INTO sabores (nombre) VALUES
('Tinga'),
('Chicharrón'),
('Champiñones'),
('Flor de calabaza'),
('Queso'),
('Papa'),
('Papa con longaniza'),
('Suadero'),
('Pata'),
('Bistec'),
('Arrachera'),
('Campechano'),
('Longaniza'),
('Refresco'),
('Agua de sabor');

-- =====================================
-- PRODUCTOS
-- =====================================

-- Quesadillas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Quesadillas', 40, 3, 'por_paquete', 3, 3);

-- Gorditas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Gorditas', 40, 1, 'por_pieza', 1, 1);

-- Flautas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Flautas', 40, 3, 'por_paquete', 3, 3);

-- Tostada grande
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(2, 'Tostada grande', 40, 1, 'por_pieza', 1, 1);

-- Mini tostadas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(2, 'Mini tostadas', 40, 4, 'por_paquete', 4, 4);

-- Pambazos
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Pambazos', 40, 1, 'por_pieza', 1, 1);

-- Tortas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion)
VALUES
(1, 'Tortas de suadero', 40, 1, 'none');

-- Huaraches
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Huaraches', 40, 1, 'por_pieza', 1, 1);

-- Sopes
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(1, 'Sopes', 40, 1, 'por_pieza', 1, 1);

-- Tacos grandes
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(3, 'Tacos grandes', 30, 1, 'por_pieza', 1, 1);

-- Mini tacos
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion, sabores_minimos, sabores_maximos)
VALUES
(3, 'Mini tacos', 40, 4, 'por_paquete', 4, 4);

-- Bebidas
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion)
VALUES
(4, 'Agua 1 litro', 40, 1, 'none'),
(4, 'Agua 1/2 litro', 20, 1, 'none'),
(4, 'Refresco 1/2 litro', 25, 1, 'none');

-- Extra queso
INSERT INTO productos
(id_categoria, nombre, precio, piezas_por_paquete,
modo_seleccion)
VALUES
(5, 'Extra queso', 5, 1, 'none');

-- =====================================
-- RELACIONAR SABORES Y PRODUCTOS
-- =====================================

-- Quesadillas: 1 al 8
INSERT INTO producto_sabores
SELECT 1, id_sabor FROM sabores
WHERE id_sabor BETWEEN 1 AND 8;

-- Gorditas: Chicharrón y Suadero
INSERT INTO producto_sabores VALUES
(2, 2), (2, 8);

-- Flautas: 1 al 8
INSERT INTO producto_sabores
SELECT 3, id_sabor FROM sabores
WHERE id_sabor BETWEEN 1 AND 8;

-- Tostada grande: Tinga y Pata
INSERT INTO producto_sabores VALUES
(4, 1), (4, 9);

-- Mini tostadas: Tinga y Pata
INSERT INTO producto_sabores VALUES
(5, 1), (5, 9);

-- Pambazos
INSERT INTO producto_sabores VALUES
(6, 1), (6, 6), (6, 7);

-- Huaraches
INSERT INTO producto_sabores VALUES
(8, 8), (8, 10), (8, 5);

-- Sopes
INSERT INTO producto_sabores VALUES
(9, 8), (9, 10), (9, 5);

-- Tacos grandes
INSERT INTO producto_sabores VALUES
(10, 8), (10, 10), (10, 11),
(10, 12), (10, 13);

-- Mini tacos
INSERT INTO producto_sabores VALUES
(11, 8), (11, 13), (11, 12);

-- =====================================
-- PRESENTACIONES
-- =====================================

INSERT INTO presentaciones
(id_producto, nombre, precio)
VALUES
(8, 'Sencillo', 40),
(8, 'Extra', 70),
(9, 'Sencillo', 40),
(9, 'Extra', 70);

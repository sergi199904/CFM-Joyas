-- ================================================
-- BASE DE DATOS COMPLETA CFM JOYAS
-- Versión: 2.0 con todas las funcionalidades
-- ================================================

-- Eliminar base de datos si existe y crearla nueva
DROP DATABASE IF EXISTS cfmjoyas;
CREATE DATABASE cfmjoyas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cfmjoyas;

-- ================================================
-- TABLA DE USUARIOS (con seguridad mejorada)
-- ================================================
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  codigo_acceso VARCHAR(20) NOT NULL,
  activo BOOLEAN DEFAULT TRUE,
  ultimo_acceso TIMESTAMP NULL,
  intentos_fallidos INT DEFAULT 0,
  bloqueado_hasta TIMESTAMP NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================
-- TABLA DE CATEGORÍAS
-- ================================================
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL,
  descripcion TEXT,
  activa BOOLEAN DEFAULT TRUE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================
-- TABLA DE PRODUCTOS (con precio y categoría)
-- ================================================
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  precio DECIMAL(10,2) DEFAULT 0,
  categoria VARCHAR(50) DEFAULT 'joyas',
  instagram VARCHAR(255) NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_categoria (categoria),
  INDEX idx_fecha (fecha),
  FOREIGN KEY (categoria) REFERENCES categorias(nombre) ON UPDATE CASCADE
);

-- ================================================
-- INSERTAR CATEGORÍAS PREDETERMINADAS
-- ================================================
INSERT INTO categorias (nombre, descripcion) VALUES 
('joyas', 'Anillos, collares, pulseras, aretes y accesorios de joyería'),
('ceramicas', 'Productos de cerámica artesanal y decorativa'),
('otros', 'Otros accesorios y productos especiales');

-- ================================================
-- CREAR USUARIO ADMINISTRADOR
-- ================================================
-- Email: admin@cfmjoyas.com
-- Contraseña: admin123
-- Código: CFM2025
INSERT INTO usuarios (email, nombre, password, codigo_acceso, activo, intentos_fallidos) VALUES 
('admin@cfmjoyas.com', 'Administrador CFM Joyas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CFM2025', TRUE, 0);

-- ================================================
-- INSERTAR PRODUCTOS DE EJEMPLO (opcional)
-- ================================================
INSERT INTO productos (nombre, precio, categoria, instagram, imagen) VALUES 
('Collar de Plata Elegante', 25000, 'joyas', 'https://instagram.com/p/ejemplo1', 'img/productos/collar_plata.jpg'),
('Aretes de Oro Rosa', 18000, 'joyas', 'https://instagram.com/p/ejemplo2', 'img/productos/aretes_oro.jpg'),
('Taza Cerámica Artesanal', 12000, 'ceramicas', 'https://instagram.com/p/ejemplo3', 'img/productos/taza_ceramica.jpg'),
('Pulsera Personalizada', 15000, 'joyas', 'https://instagram.com/p/ejemplo4', 'img/productos/pulsera_personalizada.jpg'),
('Plato Decorativo', 22000, 'ceramicas', 'https://instagram.com/p/ejemplo5', 'img/productos/plato_decorativo.jpg'),
('Llavero Especial', 8000, 'otros', 'https://instagram.com/p/ejemplo6', 'img/productos/llavero_especial.jpg');

-- ================================================
-- VERIFICACIONES Y CONSULTAS DE INFORMACIÓN
-- ================================================

-- Mostrar estructura de tablas
SELECT '=== ESTRUCTURA TABLA USUARIOS ===' AS info;
DESCRIBE usuarios;

SELECT '=== ESTRUCTURA TABLA CATEGORIAS ===' AS info;
DESCRIBE categorias;

SELECT '=== ESTRUCTURA TABLA PRODUCTOS ===' AS info;
DESCRIBE productos;

-- Mostrar datos insertados
SELECT '=== USUARIO ADMINISTRADOR CREADO ===' AS info;
SELECT id, email, nombre, codigo_acceso, activo, fecha_creacion 
FROM usuarios WHERE email = 'admin@cfmjoyas.com';

SELECT '=== CATEGORÍAS DISPONIBLES ===' AS info;
SELECT id, nombre, descripcion, activa FROM categorias;

SELECT '=== PRODUCTOS DE EJEMPLO ===' AS info;
SELECT id, nombre, precio, categoria, fecha FROM productos;

SELECT '=== RESUMEN ESTADÍSTICAS ===' AS info;
SELECT 
    (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
    (SELECT COUNT(*) FROM categorias) AS total_categorias,
    (SELECT COUNT(*) FROM productos) AS total_productos,
    (SELECT COUNT(*) FROM productos WHERE categoria = 'joyas') AS productos_joyas,
    (SELECT COUNT(*) FROM productos WHERE categoria = 'ceramicas') AS productos_ceramicas,
    (SELECT COUNT(*) FROM productos WHERE categoria = 'otros') AS productos_otros,
    (SELECT ROUND(AVG(precio), 0) FROM productos) AS precio_promedio;

-- ================================================
-- CONFIGURACIONES ADICIONALES
-- ================================================

-- Configurar charset para evitar problemas de caracteres especiales
ALTER DATABASE cfmjoyas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Configurar timezone (opcional, ajustar según tu zona)
-- SET time_zone = '-03:00'; -- Chile

SELECT '=== BASE DE DATOS CFM JOYAS CREADA EXITOSAMENTE ===' AS mensaje;
SELECT 'Credenciales de acceso:' AS info;
SELECT 'Email: admin@cfmjoyas.com' AS credential;
SELECT 'Contraseña: admin123' AS credential;
SELECT 'Código: CFM2025' AS credential;
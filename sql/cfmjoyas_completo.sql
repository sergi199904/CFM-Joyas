-- ================================================
-- BASE DE DATOS COMPLETA CFM JOYAS
-- Versión: 2.2 sin usuario admin (se crea automáticamente)
-- ================================================

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
-- TABLA DE CATEGORÍAS (con las nuevas categorías)
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
-- INSERTAR TODAS LAS CATEGORÍAS (originales + nuevas)
-- ================================================
INSERT INTO categorias (nombre, descripcion) VALUES 
-- Categorías ORIGINALES (las que ya tenías)
('joyas', 'Anillos, collares, pulseras, aretes y accesorios de joyería'),
('ceramicas', 'Productos de cerámica artesanal y decorativa'),
('otros', 'Otros accesorios y productos especiales'),

-- Categorías NUEVAS (específicas para joyas)
('collares', 'Collares y cadenas con diferentes estilos y materiales'),
('pulseras', 'Pulseras artesanales y con diseños únicos'),
('aretes', 'Aretes de diferentes tamaños y estilos'),
('anillos', 'Anillos con piedras y diseños especiales');

-- ================================================
-- NOTA: EL USUARIO ADMIN SE CREA AUTOMÁTICAMENTE
-- No insertamos usuario admin aquí porque se genera 
-- automáticamente en includes/db.php con el hash correcto
-- ================================================

-- ================================================
-- INSERTAR PRODUCTOS DE EJEMPLO
-- ================================================
INSERT INTO productos (nombre, precio, categoria, instagram, imagen) VALUES 
-- Productos con categorías ORIGINALES
-- Productos con categorías NUEVAS específicas

-- Más productos de cerámicas y otros

-- ================================================
-- CONFIGURACIONES ADICIONALES
-- ================================================

-- Configurar charset para evitar problemas de caracteres especiales
ALTER DATABASE cfmjoyas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ================================================
-- VERIFICACIONES FINALES
-- ================================================

SELECT '=== BASE DE DATOS CFM JOYAS CREADA EXITOSAMENTE ===' AS mensaje;
SELECT 'ADMIN SE CREA AUTOMÁTICAMENTE EN PRIMER ACCESO' AS info;
SELECT 'Credenciales: admin@cfmjoyas.com / admin123 / CFM2025' AS credentials;
SELECT '=== 7 CATEGORÍAS DISPONIBLES ===' AS categoria_info;
SELECT 'Originales: joyas, ceramicas, otros' AS originales;
SELECT 'Nuevas: collares, pulseras, aretes, anillos' AS nuevas;
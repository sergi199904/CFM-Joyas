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
('Collar de Plata Elegante', 25000, 'joyas', 'https://instagram.com/p/ejemplo1', 'img/productos/collar_plata.jpg'),
('Aretes de Oro Rosa', 18000, 'joyas', 'https://instagram.com/p/ejemplo2', 'img/productos/aretes_oro.jpg'),
('Taza Cerámica Artesanal', 12000, 'ceramicas', 'https://instagram.com/p/ejemplo3', 'img/productos/taza_ceramica.jpg'),
('Llavero Especial', 8000, 'otros', 'https://instagram.com/p/ejemplo6', 'img/productos/llavero_especial.jpg'),

-- Productos con categorías NUEVAS específicas
('Collar Piedra Natural Amatista', 28000, 'collares', 'https://instagram.com/p/cfmjoyas1', 'img/productos/collar_amatista.jpg'),
('Collar Cuarzo Rosa', 22000, 'collares', 'https://instagram.com/p/cfmjoyas2', 'img/productos/collar_cuarzo.jpg'),
('Pulsera Plata con Dijes', 16000, 'pulseras', 'https://instagram.com/p/cfmjoyas3', 'img/productos/pulsera_dijes.jpg'),
('Pulsera Artesanal Macramé', 14000, 'pulseras', 'https://instagram.com/p/cfmjoyas4', 'img/productos/pulsera_macrame.jpg'),
('Aretes Largos Bohemios', 19000, 'aretes', 'https://instagram.com/p/cfmjoyas5', 'img/productos/aretes_bohemios.jpg'),
('Aretes Pequeños Plata', 12000, 'aretes', 'https://instagram.com/p/cfmjoyas6', 'img/productos/aretes_pequenos.jpg'),
('Anillo Plata Ajustable', 15000, 'anillos', 'https://instagram.com/p/cfmjoyas7', 'img/productos/anillo_plata.jpg'),
('Anillo con Piedra Luna', 20000, 'anillos', 'https://instagram.com/p/cfmjoyas8', 'img/productos/anillo_luna.jpg'),

-- Más productos de cerámicas y otros
('Plato Decorativo', 18000, 'ceramicas', 'https://instagram.com/p/ejemplo4', 'img/productos/plato_decorativo.jpg'),
('Pulsera Personalizada', 15000, 'otros', 'https://instagram.com/p/ejemplo5', 'img/productos/pulsera_personalizada.jpg');

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
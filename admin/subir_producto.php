<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_input($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $categoria = limpiar_input($_POST['categoria']);
    $instagram = limpiar_input($_POST['instagram']);
    
    // Validaciones
    if (empty($nombre) || empty($categoria) || empty($instagram)) {
        $_SESSION['error'] = 'Todos los campos son obligatorios.';
        header('Location: dashboard.php');
        exit;
    }
    
    if ($precio < 0) {
        $_SESSION['error'] = 'El precio debe ser mayor a 0.';
        header('Location: dashboard.php');
        exit;
    }
    
    if (!filter_var($instagram, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = 'El enlace de Instagram no es válido.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Validar categoría existe
    $stmt = $conn->prepare("SELECT nombre FROM categorias WHERE nombre = ? AND activa = 1");
    $stmt->bind_param('s', $categoria);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $_SESSION['error'] = 'Categoría no válida.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Procesar imagen
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Error al subir la imagen.';
        header('Location: dashboard.php');
        exit;
    }
    
    $imagen = $_FILES['imagen'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($imagen['type'], $allowed_types)) {
        $_SESSION['error'] = 'Tipo de imagen no válido. Use JPG, PNG, GIF o WebP.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Limitar tamaño de imagen (5MB)
    if ($imagen['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'La imagen es demasiado grande. Máximo 5MB.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Generar nombre único para la imagen
    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $nombre_imagen = 'producto_' . time() . '_' . uniqid() . '.' . $extension;
    $ruta_destino = "../img/productos/{$nombre_imagen}";
    $ruta_bd = "img/productos/{$nombre_imagen}";
    
    // Crear directorio si no existe
    $directorio = dirname($ruta_destino);
    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }
    
    // Mover imagen
    if (!move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
        $_SESSION['error'] = 'Error al guardar la imagen.';
        header('Location: dashboard.php');
        exit;
    }
    
    // Insertar en base de datos
    try {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, categoria, instagram, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sdsss', $nombre, $precio, $categoria, $instagram, $ruta_bd);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Producto agregado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al guardar el producto en la base de datos.';
            // Eliminar imagen si falló la inserción
            if (file_exists($ruta_destino)) {
                unlink($ruta_destino);
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
        // Eliminar imagen si hubo error
        if (file_exists($ruta_destino)) {
            unlink($ruta_destino);
        }
    }
} else {
    $_SESSION['error'] = 'Método no permitido.';
}

header('Location: dashboard.php');
exit;
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

$id = intval($_GET['id']);
$error = '';
$success = '';

// Obtener producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    $_SESSION['error'] = 'Producto no encontrado.';
    header('Location: dashboard.php');
    exit;
}

// Obtener categorías
$categories = $conn->query("SELECT * FROM categorias WHERE activa = 1 ORDER BY nombre");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_input($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $categoria = limpiar_input($_POST['categoria']);
    $instagram = limpiar_input($_POST['instagram']);
    
    // Validaciones
    if (empty($nombre) || empty($categoria) || empty($instagram)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($precio < 0) {
        $error = 'El precio debe ser mayor a 0.';
    } elseif (!filter_var($instagram, FILTER_VALIDATE_URL)) {
        $error = 'El enlace de Instagram no es válido.';
    } else {
        // Validar categoría
        $stmt = $conn->prepare("SELECT nombre FROM categorias WHERE nombre = ? AND activa = 1");
        $stmt->bind_param('s', $categoria);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            $error = 'Categoría no válida.';
        } else {
            $ruta_imagen = $product['imagen']; // Mantener imagen actual por defecto
            
            // Procesar nueva imagen si se subió
            if (!empty($_FILES['imagen']['name'])) {
                $imagen = $_FILES['imagen'];
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!in_array($imagen['type'], $allowed_types)) {
                    $error = 'Tipo de imagen no válido. Use JPG, PNG, GIF o WebP.';
                } elseif ($imagen['size'] > 5 * 1024 * 1024) {
                    $error = 'La imagen es demasiado grande. Máximo 5MB.';
                } else {
                    // Generar nombre único
                    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
                    $nombre_imagen = 'producto_' . time() . '_' . uniqid() . '.' . $extension;
                    $ruta_destino = "../img/productos/{$nombre_imagen}";
                    $ruta_imagen = "img/productos/{$nombre_imagen}";
                    
                    // Crear directorio si no existe
                    $directorio = dirname($ruta_destino);
                    if (!is_dir($directorio)) {
                        mkdir($directorio, 0755, true);
                    }
                    
                    if (!move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                        $error = 'Error al guardar la nueva imagen.';
                    } else {
                        // Eliminar imagen anterior si existe
                        $imagen_anterior = "../" . $product['imagen'];
                        if (file_exists($imagen_anterior) && $imagen_anterior !== $ruta_destino) {
                            unlink($imagen_anterior);
                        }
                    }
                }
            }
            
            // Actualizar en base de datos si no hay errores
            if (!$error) {
                try {
                    $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, categoria=?, instagram=?, imagen=? WHERE id=?");
                    $stmt->bind_param('sdsssi', $nombre, $precio, $categoria, $instagram, $ruta_imagen, $id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success'] = 'Producto actualizado exitosamente.';
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $error = 'Error al actualizar el producto.';
                    }
                } catch (Exception $e) {
                    $error = 'Error del sistema: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto - CFM Joyas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
        <span class="text-light">
            <i class="fas fa-edit"></i> Editando Producto #<?= $id ?>
        </span>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Editar Producto: <?= htmlspecialchars($product['nombre']) ?>
                    </h4>
                </div>
                <div class="card-body">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto</label>
                                    <input name="nombre" class="form-control" 
                                           value="<?= htmlspecialchars($product['nombre']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Precio (CLP)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input name="precio" type="number" class="form-control" min="0" step="500"
                                               value="<?= htmlspecialchars($product['precio']) ?>" required>
                                    </div>
                                    <small class="text-muted">Precio actual: $<?= number_format($product['precio'], 0, ',', '.') ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select name="categoria" class="form-select" required>
                                        <?php while ($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?= $cat['nombre'] ?>" 
                                                    <?= $product['categoria'] === $cat['nombre'] ? 'selected' : '' ?>>
                                                <?= ucfirst($cat['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Enlace Instagram</label>
                                    <input name="instagram" type="url" class="form-control" 
                                           value="<?= htmlspecialchars($product['instagram']) ?>" required>
                                    <small class="text-muted">
                                        <a href="<?= htmlspecialchars($product['instagram']) ?>" target="_blank">
                                            <i class="fab fa-instagram"></i> Ver enlace actual
                                        </a>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Imagen Actual</label><br>
                                    <img src="../<?= htmlspecialchars($product['imagen']) ?>" 
                                         alt="<?= htmlspecialchars($product['nombre']) ?>" 
                                         class="img-fluid rounded shadow mb-2" style="max-width:250px;">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nueva Imagen (opcional)</label>
                                    <input name="imagen" type="file" class="form-control" accept="image/*">
                                    <small class="text-muted">
                                        Formatos: JPG, PNG, GIF, WebP. Máximo 5MB.
                                    </small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Información del producto:</strong><br>
                                    <small>
                                        ID: <?= $product['id'] ?><br>
                                        Creado: <?= date('d/m/Y H:i', strtotime($product['fecha'])) ?><br>
                                        Categoría actual: <?= ucfirst($product['categoria']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
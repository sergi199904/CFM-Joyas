<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: dashboard.php');
    exit;
}

// Fetch products with category filter
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$search = isset($_GET['search']) ? limpiar_input($_GET['search']) : '';

$query = "SELECT p.*, c.nombre as categoria_nombre FROM productos p 
          LEFT JOIN categorias c ON p.categoria = c.nombre 
          WHERE 1=1";
$params = [];
$types = '';

if ($categoria_filtro && $categoria_filtro !== 'todas') {
    $query .= " AND p.categoria = ?";
    $params[] = $categoria_filtro;
    $types .= 's';
}

if ($search) {
    $query .= " AND (p.nombre LIKE ? OR p.categoria LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$query .= " ORDER BY p.fecha DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Fetch categories
$categories = $conn->query("SELECT * FROM categorias WHERE activa = 1 ORDER BY nombre");

// Statistics
$stats = $conn->query("SELECT 
    COUNT(*) as total_productos,
    COUNT(CASE WHEN categoria = 'joyas' THEN 1 END) as total_joyas,
    COUNT(CASE WHEN categoria = 'ceramicas' THEN 1 END) as total_ceramicas,
    COUNT(CASE WHEN categoria = 'otros' THEN 1 END) as total_otros,
    AVG(precio) as precio_promedio
    FROM productos")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - CFM Joyas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stats-card { transition: transform 0.2s; }
        .stats-card:hover { transform: translateY(-5px); }
        .product-img { width: 60px; height: 60px; object-fit: cover; }
        .price-badge { background: linear-gradient(45deg, #28a745, #20c997); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-gem"></i> Admin CFM Joyas
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-light">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
            <a href="../index.php" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-external-link-alt"></i> Ver Sitio
            </a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <!-- Alertas -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Total Productos</h6>
                            <h3><?= $stats['total_productos'] ?></h3>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Joyas</h6>
                            <h3><?= $stats['total_joyas'] ?></h3>
                        </div>
                        <i class="fas fa-gem fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Cerámicas</h6>
                            <h3><?= $stats['total_ceramicas'] ?></h3>
                        </div>
                        <i class="fas fa-cookie-bite fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Precio Promedio</h6>
                            <h3>$<?= number_format($stats['precio_promedio'], 0, ',', '.') ?></h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Add Product Form -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Agregar Producto</h5>
                </div>
                <div class="card-body">
                    <form action="subir_producto.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input name="nombre" class="form-control" placeholder="Nombre del producto" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Precio (CLP)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input name="precio" type="number" class="form-control" min="0" step="500" placeholder="15000" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['nombre'] ?>"><?= ucfirst($cat['nombre']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Enlace Instagram</label>
                            <input name="instagram" type="url" class="form-control" placeholder="https://instagram.com/p/..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <input name="imagen" type="file" class="form-control" accept="image/*" required>
                        </div>
                        
                        <button class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Productos Existentes</h5>
                    
                    <!-- Filters -->
                    <form method="GET" class="d-flex gap-2">
                        <select name="categoria" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="todas">Todas las categorías</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?= $cat['nombre'] ?>" <?= $categoria_filtro === $cat['nombre'] ? 'selected' : '' ?>>
                                    <?= ucfirst($cat['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Categoría</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($products->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay productos que mostrar</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php while ($row = $products->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?= htmlspecialchars($row['imagen']) ?>" 
                                                     class="product-img rounded" alt="Producto">
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge price-badge text-white">
                                                    $<?= number_format($row['precio'], 0, ',', '.') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst(htmlspecialchars($row['categoria'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y', strtotime($row['fecha'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="edit_producto.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= htmlspecialchars($row['instagram']) ?>" 
                                                       target="_blank" class="btn btn-sm btn-outline-info" title="Ver en Instagram">
                                                        <i class="fab fa-instagram"></i>
                                                    </a>
                                                    <a href="dashboard.php?delete=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('¿Eliminar este producto?')" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
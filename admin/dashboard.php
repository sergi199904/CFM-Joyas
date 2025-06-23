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
    $stmt = mysqli_prepare($conn, "DELETE FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: dashboard.php');
    exit;
}

// Fetch products
$products = mysqli_query($conn, "SELECT * FROM productos ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Admin CFM Joyas</a>
    <div class="ms-auto">
      <a href="logout.php" class="btn btn-outline-light me-2">Cerrar Sesión</a>
      <a href="../index.php" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">Ver Sitio</a>
    </div>
  </div>
</nav>
<main class="container py-4">
  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Agregar Producto</h5>
          <form action="subir_producto.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
            <div class="mb-3"><input name="instagram" type="url" class="form-control" placeholder="Enlace Instagram" required></div>
            <div class="mb-3"><input name="imagen" type="file" class="form-control" accept="image/*" required></div>
            <button class="btn btn-success">Agregar</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <h5>Productos Existentes</h5>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php while ($row = mysqli_fetch_assoc($products)): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td>
                <a href="edit_producto.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary me-1">Editar</a>
                <a href="dashboard.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once '../includes/db.php';
$id = intval($_GET['id']);
$stmt = mysqli_prepare($conn, "SELECT nombre, instagram, imagen FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $instagram = $_POST['instagram'];
    if (!empty($_FILES['imagen']['name'])) {
        $img = basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../img/productos/{$img}");
        $ruta = "img/productos/{$img}";
        $query = "UPDATE productos SET nombre=?, instagram=?, imagen=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssi', $nombre, $instagram, $ruta, $id);
    } else {
        $query = "UPDATE productos SET nombre=?, instagram=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssi', $nombre, $instagram, $id);
    }
    mysqli_stmt_execute($stmt);
    header('Location: dashboard.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Editar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h3>Editar Producto #<?= $id ?></h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input name="nombre" class="form-control" value="<?= htmlspecialchars($product['nombre']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Enlace Instagram</label>
        <input name="instagram" type="url" class="form-control" value="<?= htmlspecialchars($product['instagram']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Imagen Actual</label><br>
        <img src="../<?= $product['imagen'] ?>" alt="" class="img-fluid mb-2" style="max-width:150px;">
      </div>
      <div class="mb-3">
        <label class="form-label">Nueva Imagen (opcional)</label>
        <input name="imagen" type="file" class="form-control" accept="image/*">
      </div>
      <button class="btn btn-primary">Guardar Cambios</button>
      <a href="dashboard.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
  </div>
  <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

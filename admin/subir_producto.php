<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $instagram = $_POST['instagram'];
    $imgFile = $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], "../img/productos/{$imgFile}");
    $ruta = "img/productos/{$imgFile}";
    $stmt = mysqli_prepare($conn, "INSERT INTO productos (nombre, instagram, imagen) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $nombre, $instagram, $ruta);
    mysqli_stmt_execute($stmt);
}
header('Location: dashboard.php'); exit;
?>

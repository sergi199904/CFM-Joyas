<?php
session_start();
require_once '../includes/db.php';

$error = '';
$bloqueado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_input($_POST['email']);
    $password = $_POST['password'];
    $codigo_acceso = limpiar_input($_POST['codigo_acceso']);
    
    // Verificar si puede intentar login
    if (!verificar_intentos_login($email)) {
        $error = 'Cuenta temporalmente bloqueada por seguridad. Intente en 15 minutos.';
        $bloqueado = true;
    } else {
        // Verificar código de acceso primero
        if (!validar_codigo_acceso($codigo_acceso)) {
            registrar_intento_fallido($email);
            $error = 'Código de acceso inválido.';
        } else {
            // Buscar usuario
            $stmt = $conn->prepare("SELECT id, password, nombre, codigo_acceso FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Verificar contraseña
                if (password_verify($password, $user['password'])) {
                    // Verificar código de acceso del usuario
                    if ($user['codigo_acceso'] === $codigo_acceso) {
                        // Login exitoso
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['nombre'];
                        $_SESSION['user_email'] = $email;
                        
                        // Limpiar intentos fallidos
                        limpiar_intentos($email);
                        
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        registrar_intento_fallido($email);
                        $error = 'Código de acceso incorrecto.';
                    }
                } else {
                    registrar_intento_fallido($email);
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                registrar_intento_fallido($email);
                $error = 'Usuario no encontrado o inactivo.';
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
    <title>Admin - Login Seguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { backdrop-filter: blur(10px); background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); }
        .form-control { background: rgba(255,255,255,0.9); }
        .alert-danger { background: rgba(220,53,69,0.9); color: white; }
        .text-warning { color: #ffc107 !important; }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-gem fa-3x text-warning mb-3"></i>
                            <h3 class="text-white">CFM Joyas Admin</h3>
                            <p class="text-light">Acceso Seguro</p>
                        </div>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($bloqueado): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i> Por seguridad, intente más tarde.
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label text-white">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" name="email" id="email" class="form-control" required 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label text-white">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" name="password" id="password" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="mb-4">
                                <label for="codigo_acceso" class="form-label text-white">
                                    <i class="fas fa-key"></i> Código de Acceso
                                </label>
                                <input type="text" name="codigo_acceso" id="codigo_acceso" class="form-control" required 
                                       placeholder="Código especial de CFM Joyas">
                                <small class="text-light">
                                    <i class="fas fa-info-circle"></i> Solicite el código a la administración
                                </small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg" <?= $bloqueado ? 'disabled' : '' ?>>
                                    <i class="fas fa-sign-in-alt"></i> Ingresar
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="../index.php" class="text-light">
                                <i class="fas fa-arrow-left"></i> Volver al sitio web
                            </a>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-light">
                                <i class="fas fa-shield-alt"></i> Acceso protegido con doble autenticación
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
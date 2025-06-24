<?php
session_start();
require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_input($_POST['email']);
    $nombre = limpiar_input($_POST['nombre']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $codigo_acceso = limpiar_input($_POST['codigo_acceso']);
    
    // Validaciones
    if (empty($email) || empty($nombre) || empty($password) || empty($codigo_acceso)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!validar_codigo_acceso($codigo_acceso)) {
        $error = 'Código de acceso inválido. Use: CFM2025, JOYAS2025 o ADMIN2025';
    } else {
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $error = 'El email ya está registrado.';
        } else {
            // Crear usuario
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO usuarios (email, nombre, password, codigo_acceso, activo) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param('ssss', $email, $nombre, $hash, $codigo_acceso);
            
            if ($stmt->execute()) {
                $success = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.';
            } else {
                $error = 'Error al crear la cuenta. Intente nuevamente.';
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
    <title>Crear Administrador - CFM Joyas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        .register-card { 
            backdrop-filter: blur(10px); 
            background: rgba(255,255,255,0.1); 
            border: 1px solid rgba(255,255,255,0.2); 
        }
        .form-control { 
            background: rgba(255,255,255,0.9); 
            border: 1px solid rgba(255,255,255,0.3);
        }
        .form-control:focus {
            background: white;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .alert-danger { 
            background: rgba(220,53,69,0.9); 
            color: white; 
            border: none;
        }
        .alert-success { 
            background: rgba(40,167,69,0.9); 
            color: white; 
            border: none;
        }
        .text-warning { 
            color: #ffc107 !important; 
        }
        .code-help {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card register-card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x text-warning mb-3"></i>
                            <h3 class="text-white">Crear Administrador</h3>
                            <p class="text-light">CFM Joyas - Acceso Seguro</p>
                        </div>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="login.php" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Ir a Iniciar Sesión
                                </a>
                            </div>
                        <?php else: ?>
                        
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label text-white">
                                    <i class="fas fa-user"></i> Nombre Completo
                                </label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required 
                                       placeholder="Tu nombre completo"
                                       value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label text-white">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" name="email" id="email" class="form-control" required 
                                       placeholder="tu@email.com"
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label text-white">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" name="password" id="password" class="form-control" required minlength="6"
                                       placeholder="Mínimo 6 caracteres">
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm" class="form-label text-white">
                                    <i class="fas fa-lock"></i> Confirmar Contraseña
                                </label>
                                <input type="password" name="confirm" id="confirm" class="form-control" required 
                                       placeholder="Repite tu contraseña">
                            </div>
                            
                            <div class="mb-4">
                                <label for="codigo_acceso" class="form-label text-white">
                                    <i class="fas fa-key"></i> Código de Acceso Especial
                                </label>
                                <input type="text" name="codigo_acceso" id="codigo_acceso" class="form-control" required 
                                       placeholder="Código CFM Joyas"
                                       value="<?= isset($_POST['codigo_acceso']) ? htmlspecialchars($_POST['codigo_acceso']) : '' ?>">
                                
                                <div class="code-help">
                                    <small class="text-light">
                                        <i class="fas fa-info-circle"></i> <strong>Códigos válidos:</strong><br>
                                        • <code>CFM2025</code><br>
                                        • <code>JOYAS2025</code><br>
                                        • <code>ADMIN2025</code>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-user-plus"></i> Crear Cuenta
                                </button>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-light me-3">
                                <i class="fas fa-arrow-left"></i> Ya tengo cuenta
                            </a>
                            <a href="../index.php" class="text-light">
                                <i class="fas fa-home"></i> Volver al sitio
                            </a>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-light">
                                <i class="fas fa-shield-alt"></i> Solo para administradores autorizados de CFM Joyas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en tiempo real de contraseñas
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('confirm');
            
            function validatePasswords() {
                if (confirm.value && password.value !== confirm.value) {
                    confirm.setCustomValidity('Las contraseñas no coinciden');
                    confirm.classList.add('is-invalid');
                } else {
                    confirm.setCustomValidity('');
                    confirm.classList.remove('is-invalid');
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirm.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>
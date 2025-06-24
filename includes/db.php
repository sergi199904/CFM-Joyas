<?php
// Configuración de la base de datos
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'cfmjoyas';

// Configuración de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8");
} catch (mysqli_sql_exception $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Intente más tarde.");
}

// Función para limpiar datos de entrada
function limpiar_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($data)));
}

// Función para validar código de acceso
function validar_codigo_acceso($codigo) {
    // Lista de códigos válidos (puedes cambiar estos)
    $codigos_validos = ['CFM2025', 'JOYAS2025', 'ADMIN2025'];
    return in_array($codigo, $codigos_validos);
}

// Función para verificar intentos de login
function verificar_intentos_login($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT intentos_fallidos, bloqueado_hasta FROM usuarios WHERE email = ? AND activo = 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Si está bloqueado y aún no ha pasado el tiempo
        if ($row['bloqueado_hasta'] && strtotime($row['bloqueado_hasta']) > time()) {
            return false; // Bloqueado
        }
        
        // Si tiene muchos intentos fallidos
        if ($row['intentos_fallidos'] >= 3) {
            return false; // Bloqueado por intentos
        }
    }
    
    return true; // Puede intentar
}

// Función para registrar intento fallido
function registrar_intento_fallido($email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
}

// Función para limpiar intentos exitosos
function limpiar_intentos($email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
}
?>
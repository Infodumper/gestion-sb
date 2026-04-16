<?php
session_start();
require_once '../includes/db.php';
require_once '../utils/logger.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM dblogin WHERE Usuario = ? AND Estado = 1 LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Clave'])) {
        // Login success - ISO 27001: Session Fixation Protection
        session_regenerate_id(true);
        
        $_SESSION['userid'] = $user['IdUsuario'];
        $_SESSION['email'] = $user['Usuario'];
        $_SESSION['fullname'] = $user['Nombre'];
        $_SESSION['role'] = $user['Rol'];
        $_SESSION['login_at'] = time(); // Requerido por includes/security.php

        log_event('AUTH_OK', 'Usuario access: ' . $email, __FILE__);
        echo json_encode(['success' => true]);
    } else {
        // Mitigation for Brute Force (Simple delay)
        usleep(500000); // 0.5 seconds
        log_event('AUTH_FAIL', 'Intento fallido: ' . $email, __FILE__);
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
    }
} catch (Throwable $e) {
    error_log("[GestionSB][CRITICAL] Error en login: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>


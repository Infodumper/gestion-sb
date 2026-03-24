<?php
/**
 * Security & Audit Logs - ISO 27001
 */

/**
 * Registra un evento en el log de auditoría
 */
function log_event($pdo, $evento, $detalle = null) {
    $userId = $_SESSION['userid'] ?? null;
    try {
        $stmt = $pdo->prepare("INSERT INTO logs (IdUsuario, Evento, Detalle) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $evento, $detalle]);
    } catch (PDOException $e) {
        // Fallback silencioso para no interrumpir el flujo del usuario
        error_log("Error saving log: " . $e->getMessage());
    }
}

/**
 * Sanitiza salida HTML
 */
function s($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica si el usuario está logueado
 */
function check_auth() {
    if (!isset($_SESSION['userid'])) {
        if (strpos($_SERVER['REQUEST_URI'], 'ajax_') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
}
?>

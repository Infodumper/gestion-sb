<?php
/**
 * Security & Session Guard — Gestion SB
 * Ubicación: includes/security.php
 *
 * PROTOCOLO:
 *   1. Incluir como PRIMERA línea en toda vista/endpoint privado.
 *   2. Este archivo arranca la sesión y verifica autenticación.
 *   3. En caso de fallo AJAX → JSON 401. En vista normal → redirect.
 *
 * FUNCIONES EXPORTADAS:
 *   - log_event(nivel, mensaje, origen)  → delegado a utils/logger.php
 *   - s($text)                           → htmlspecialchars seguro
 *   - check_auth()                       → forzar autenticación (ya se llama aquí)
 */

// ── 0. Arrancar sesión (idempotente) ──────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 1. Cargar logger PHP ───────────────────────────────────────────────────────
require_once __DIR__ . '/../utils/logger.php';

// ── 2. Timeout de sesión: 8 horas ─────────────────────────────────────────────
const SESSION_TIMEOUT = 8 * 3600;

$_sessionExpired = isset($_SESSION['login_at']) &&
                   (time() - $_SESSION['login_at']) > SESSION_TIMEOUT;

if ($_sessionExpired) {
    session_unset();
    session_destroy();
    session_start(); // nueva sesión vacía
}

// ── 3. Verificar autenticación ────────────────────────────────────────────────
function check_auth(): void
{
    if (isset($_SESSION['userid'])) {
        // Renovar el timestamp para sesiones activas (sliding expiration)
        $_SESSION['login_at'] = time();
        return;
    }

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
              strpos($_SERVER['REQUEST_URI'] ?? '', 'ajax_') !== false;

    header('Content-Type: application/json; charset=utf-8');

    if ($isAjax) {
        http_response_code(401);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesión expirada. Por favor, volvé a ingresar.',
        ]);
        exit;
    }

    // Vista normal → redirigir al login
    $loginPath = '/admin/login.php?expired=1';
    header("Location: $loginPath");
    exit;
}

// Ejecutar verificación automáticamente al incluir este archivo
check_auth();

// ── 4. Helpers de output ──────────────────────────────────────────────────────

/**
 * Escapa output HTML de forma segura.
 */
if (!function_exists('s')) {
    function s(?string $text): string
    {
        return htmlspecialchars((string)($text ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/**
 * Retorna JSON estándar y termina la ejecución.
 * Uso: json_response('ok', $data, 'Mensaje', 200)
 */
if (!function_exists('json_response')) {
    function json_response(string $status, $data = null, string $message = '', int $httpCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($httpCode);
        }
        $payload = ['status' => $status, 'message' => $message];
        if ($data !== null) $payload['data'] = $data;
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

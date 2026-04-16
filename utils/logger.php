<?php
/**
 * Logger Central — Gestion SB
 * Ubicación: utils/logger.php
 *
 * CONTRATO: log_event(string $nivel, string $mensaje, string $origen = '')
 * Sin PDO como parámetro — usa la variable global $pdo si está disponible.
 *
 * Niveles soportados: INFO, INSERT, UPDATE, DELETE, AUTH_OK, AUTH_FAIL, ERROR, WARN
 */

if (!function_exists('log_event')) {

    function log_event(string $nivel, string $mensaje, string $origen = ''): void
    {
        global $pdo;

        $userId  = $_SESSION['userid'] ?? null;
        $archivo = $origen ?: 'sistema';
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $nivel   = strtoupper($nivel);

        // ── 1. Intentar persistir en la BD ──────────────────────────────
        if ($pdo instanceof PDO) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO logs (IdUsuario, Nivel, Evento, Detalle, Origen, Ip)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $userId,
                    $nivel,
                    $nivel,                // columna Evento (compatibilidad con estructura antigua)
                    $mensaje,
                    basename($archivo),
                    $ip,
                ]);
            } catch (Throwable $e) {
                // Fallback silencioso: no interrumpir el flujo principal
                error_log("[GestionSB][LOG_FAIL] {$e->getMessage()}");
            }
        }

        // ── 2. Siempre escribir en error_log como respaldo ───────────────
        $linea = sprintf(
            '[%s] [%s] [uid=%s] [src=%s] %s',
            date('Y-m-d H:i:s'),
            $nivel,
            $userId ?? 'anon',
            basename($archivo),
            $mensaje
        );
        error_log($linea);
    }

}

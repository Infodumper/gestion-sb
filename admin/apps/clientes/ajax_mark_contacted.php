<?php
/**
 * Endpoint: Registrar Contacto WhatsApp (toggle)
 * Contrato: POST JSON → { status: 'ok|error', data?: {marked: bool}, message: string }
 */

require_once '../../../includes/security.php';
require_once '../../../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

// Aceptar tanto JSON body como form-data (retrocompatible)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($contentType, 'application/json')) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $input = $_POST;
}

$idCliente = intval($input['id_cliente'] ?? $input['id'] ?? 0);
$tipo      = trim($input['tipo'] ?? '');

$tiposValidos = ['cumple', 'habitual', 'mensual'];
if ($idCliente <= 0 || !in_array($tipo, $tiposValidos)) {
    json_response('error', null, 'Parámetros inválidos. Tipo debe ser: ' . implode(', ', $tiposValidos));
}

try {
    // ── Determinar lógica de período según tipo ────────────────────────────────
    if ($tipo === 'cumple') {
        // Solo una vez por día (el día exacto del cumpleaños)
        $checkSql = "SELECT IdContacto FROM contactoswhatsapp
                     WHERE IdCliente = ? AND Tipo = ? AND DATE(FechaContacto) = CURDATE()";
    } else {
        // Solo una vez por mes (habitual, mensual)
        $checkSql = "SELECT IdContacto FROM contactoswhatsapp
                     WHERE IdCliente = ? AND Tipo = ?
                       AND MONTH(FechaContacto) = MONTH(CURDATE())
                       AND YEAR(FechaContacto)  = YEAR(CURDATE())";
    }

    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$idCliente, $tipo]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Toggle OFF → eliminar el registro
        $pdo->prepare("DELETE FROM contactoswhatsapp WHERE IdContacto = ?")
            ->execute([$existing['IdContacto']]);

        log_event('DELETE', "Contacto WA tipo=$tipo desmarcado para IdCliente=$idCliente", __FILE__);
        json_response('ok', ['marked' => false], 'Contacto desmarcado');

    } else {
        // Toggle ON → insertar nuevo registro
        $pdo->prepare("INSERT INTO contactoswhatsapp (IdCliente, Tipo) VALUES (?, ?)")
            ->execute([$idCliente, $tipo]);

        log_event('INSERT', "Contacto WA tipo=$tipo registrado para IdCliente=$idCliente", __FILE__);
        json_response('ok', ['marked' => true], 'Contacto registrado');
    }

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    json_response('error', null, 'Error interno del servidor', 500);
}

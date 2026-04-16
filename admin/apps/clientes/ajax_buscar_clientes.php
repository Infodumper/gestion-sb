<?php
/**
 * Endpoint: Búsqueda dinámica de clientes (AJAX search)
 * Contrato: GET ?q={term} → { status: 'ok', data: [{id, label, telefono},...] }
 */

require_once '../../../includes/security.php';
require_once '../../../includes/db.php';
require_once '../../../includes/utils.php';
header('Content-Type: application/json; charset=utf-8');

$term = trim($_GET['q'] ?? $_GET['term'] ?? '');

if (empty($term) || strlen($term) < 2) {
    json_response('ok', [], 'Consulta muy corta');
}

$f = "%{$term}%";

try {
    $stmt = $pdo->prepare("
        SELECT IdCliente, Nombre, Apellido, Telefono
        FROM clientes
        WHERE (Nombre LIKE ? OR Apellido LIKE ? OR Telefono LIKE ? OR Dni LIKE ?)
          AND Estado = 1
        ORDER BY Apellido ASC, Nombre ASC
        LIMIT 20
    ");
    $stmt->execute([$f, $f, $f, $f]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = array_map(fn($r) => [
        'id'       => (int)$r['IdCliente'],
        'label'    => s($r['Apellido']) . ' ' . s($r['Nombre']),
        'telefono' => s($r['Telefono'] ?? ''),
    ], $rows);

    json_response('ok', $results, count($results) . ' resultado(s)');

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    json_response('error', null, 'Error en la búsqueda', 500);
}

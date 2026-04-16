<?php
/**
 * Endpoint: Ficha 360° del Cliente
 * Contrato: GET ?id={n} → { status: 'ok|error', data: {client, history, metricas}, message }
 */

require_once '../../../includes/security.php';
require_once '../../../includes/db.php';
require_once '../../../includes/utils.php';
header('Content-Type: application/json; charset=utf-8');

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    json_response('error', null, 'ID de cliente no válido', 400);
}

try {
    // ── 1. Datos del Cliente ───────────────────────────────────────────────────
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE IdCliente = ?");
    $stmt->execute([$id]);
    $c = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$c) {
        json_response('error', null, 'Cliente no encontrado', 404);
    }

    // ── 2. Formatear datos sensibles ──────────────────────────────────────────
    $client = [
        'IdCliente'      => (int)$c['IdCliente'],
        'Nombre'         => s($c['Nombre'] ?? ''),
        'Apellido'       => s($c['Apellido'] ?? ''),
        'NombreCompleto' => s(trim(($c['Nombre'] ?? '') . ' ' . ($c['Apellido'] ?? ''))),
        'Telefono'       => s($c['Telefono'] ?? ''),
        'TelefonoWA'     => preg_replace('/\D/', '', $c['Telefono'] ?? ''),
        'Dni'            => s($c['Dni'] ?? ''),
        'FechaNac'       => $c['FechaNac'],
        'FechaNacFormat' => $c['FechaNac'] ? date('d/m', strtotime($c['FechaNac'])) : null,
        'Estado'         => (int)$c['Estado'],
        'Promociones'    => (int)($c['Promociones'] ?? 0),
        'Iniciales'      => strtoupper(
            substr($c['Nombre'] ?? '', 0, 1) . substr($c['Apellido'] ?? '', 0, 1)
        ),
    ];

    // ── 3. Historial de pedidos (últimos 5) ────────────────────────────────────
    $history  = [];
    $metricas = ['total_pedidos' => 0, 'monto_total' => 0, 'ticket_promedio' => 0];

    try {
        // Historial
        $stmtHist = $pdo->prepare("
            SELECT IdPedido, FechaCreacion AS Fecha, Total, Estado
            FROM pedidos
            WHERE IdCliente = ?
            ORDER BY FechaCreacion DESC
            LIMIT 5
        ");
        $stmtHist->execute([$id]);

        while ($row = $stmtHist->fetch(PDO::FETCH_ASSOC)) {
            $history[] = [
                'id'     => (int)$row['IdPedido'],
                'fecha'  => date('d/m/Y', strtotime($row['Fecha'])),
                'total'  => fmt_money($row['Total']),
                'estado' => (int)$row['Estado'],
            ];
        }

        // Métricas de compra
        $stmtMet = $pdo->prepare("
            SELECT
              COUNT(*)      AS total_pedidos,
              COALESCE(SUM(Total), 0)  AS monto_total,
              COALESCE(AVG(Total), 0)  AS ticket_promedio
            FROM pedidos
            WHERE IdCliente = ? AND Estado IN (2, 3)
        ");
        $stmtMet->execute([$id]);
        $met = $stmtMet->fetch(PDO::FETCH_ASSOC);

        $metricas = [
            'total_pedidos'   => (int)$met['total_pedidos'],
            'monto_total'     => fmt_money($met['monto_total']),
            'ticket_promedio' => fmt_money($met['ticket_promedio']),
        ];

    } catch (PDOException $e) {
        // La tabla pedidos podría no existir aún — no es error crítico
        log_event('WARN', 'Tabla pedidos no disponible en ficha 360°: ' . $e->getMessage(), __FILE__);
    }

    json_response('ok', [
        'client'   => $client,
        'history'  => $history,
        'metricas' => $metricas,
    ], 'Ficha cargada');

} catch (Exception $e) {
    log_event('ERROR', $e->getMessage(), __FILE__);
    json_response('error', null, 'Error al cargar la ficha del cliente', 500);
}

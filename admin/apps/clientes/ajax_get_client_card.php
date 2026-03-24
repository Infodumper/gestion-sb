<?php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

require_once '../../../includes/db.php';
require_once '../../../includes/security.php';
require_once '../../../includes/utils.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de cliente no válido.']);
    exit;
}

try {
    // 1. Obtener Info del Cliente
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE IdCliente = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
        exit;
    }

    // 2. Formatear datos
    $client['FechaNacFormat'] = $client['FechaNac'] ? date('d/m', strtotime($client['FechaNac'])) : 'No cargado';

    // 3. Obtener Historial de Pedidos (si existe la tabla)
    $history = [];
    try {
        $stmt_hist = $pdo->prepare("SELECT IdPedido, Fecha, Total FROM pedidos WHERE IdCliente = ? ORDER BY Fecha DESC LIMIT 5");
        $stmt_hist->execute([$id]);
        while ($row = $stmt_hist->fetch(PDO::FETCH_ASSOC)) {
            $history[] = [
                'id' => $row['IdPedido'],
                'fecha' => date('d/m/Y', strtotime($row['Fecha'])),
                'total' => fmt_money($row['Total'])
            ];
        }
    } catch (PDOException $e) {
        // La tabla Pedidos podría no tener datos aún
    }

    echo json_encode([
        'success' => true,
        'client' => [
            'IdCliente' => $client['IdCliente'],
            'Nombre' => s($client['Nombre']),
            'Apellido' => s($client['Apellido']),
            'Telefono' => s($client['Telefono']),
            'Dni' => s($client['Dni']),
            'FechaNac' => $client['FechaNac'],
            'FechaNacFormat' => $client['FechaNacFormat'],
            'Promociones' => $client['Promociones'],
            'Estado' => $client['Estado']
        ],
        'history' => $history
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

require_once '../../../includes/db.php';

try {
    $id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id_pedido <= 0) throw new Exception('ID de pedido inválido.');

    // 1. Obtener datos del pedido
    $stmt = $pdo->prepare("SELECT p.*, c.Nombre as ClienteNombre, c.Apellido as ClienteApellido 
                           FROM pedidos p 
                           JOIN clientes c ON p.IdCliente = c.IdCliente 
                           WHERE p.IdPedido = ?");
    $stmt->execute([$id_pedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) throw new Exception('Pedido no encontrado.');

    // 2. Obtener items del detalle
    $stmt_items = $pdo->prepare("SELECT d.*, p.Nombre as ProductoNombre, p.Codigo as ProductoCodigo 
                                 FROM detalle_pedidos d 
                                 JOIN productos p ON d.IdProducto = p.IdProducto 
                                 WHERE d.IdPedido = ?");
    $stmt_items->execute([$id_pedido]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'pedido' => [
            'id' => $pedido['IdPedido'],
            'fecha' => date('d/m/Y H:i', strtotime($pedido['Fecha'])),
            'cliente' => $pedido['ClienteNombre'] . ' ' . $pedido['ClienteApellido'],
            'total' => number_format($pedido['Total'], 2, ',', '.'),
            'estado' => $pedido['Estado']
        ],
        'items' => array_map(function($i) {
            return [
                'codigo' => $i['ProductoCodigo'],
                'nombre' => $i['ProductoNombre'],
                'cantidad' => $i['Cantidad'],
                'precio' => number_format($i['PrecioUnitario'], 2, ',', '.'),
                'subtotal' => number_format($i['Cantidad'] * $i['PrecioUnitario'], 2, ',', '.')
            ];
        }, $items)
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

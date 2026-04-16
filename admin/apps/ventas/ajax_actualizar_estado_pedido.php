<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

require_once '../../../includes/db.php';
require_once '../../../includes/security.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
    $nuevo_estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    if ($id_pedido <= 0 || !in_array($nuevo_estado, ['Activo', 'Inactivo'])) {
        throw new Exception('Parámetros no válidos.');
    }

    $pdo->beginTransaction();

    // 1. Obtener estado actual
    $stmt = $pdo->prepare("SELECT Estado FROM pedidos WHERE IdPedido = ?");
    $stmt->execute([$id_pedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) throw new Exception('Pedido no encontrado.');

    $estado_anterior = $pedido['Estado'];

    if ($estado_anterior === $nuevo_estado) {
        throw new Exception('El pedido ya se encuentra en ese estado.');
    }

    // 2. Actualizar estado
    $stmt_upd = $pdo->prepare("UPDATE pedidos SET Estado = ? WHERE IdPedido = ?");
    $stmt_upd->execute([$nuevo_estado, $id_pedido]);

    // 3. Manejo de Stock
    if ($nuevo_estado === 'Inactivo') {
        // --- REVERTIR STOCK (De Inactivo a Activo) ---
        // Obtenemos los productos del pedido para devolverlos al stock
        $stmt_items = $pdo->prepare("SELECT IdProducto, Cantidad FROM detalle_pedidos WHERE IdPedido = ?");
        $stmt_items->execute([$id_pedido]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $stmt_stock = $pdo->prepare("UPDATE productos SET Stock = Stock + ? WHERE IdProducto = ?");
        foreach ($items as $item) {
            $stmt_stock->execute([$item['Cantidad'], $item['IdProducto']]);
        }
        $log_msg = "Pedido #$id_pedido MARCADO COMO INACTIVO (Stock devuelto).";
    } else if ($nuevo_estado === 'Activo' && $estado_anterior === 'Inactivo') {
        // --- VOLVER A ACTIVAR (De Inactivo a Activo) ---
        // Obtenemos los productos para volver a descontar
        $stmt_items = $pdo->prepare("SELECT IdProducto, Cantidad FROM detalle_pedidos WHERE IdPedido = ?");
        $stmt_items->execute([$id_pedido]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $stmt_stock = $pdo->prepare("UPDATE productos SET Stock = Stock - ? WHERE IdProducto = ?");
        foreach ($items as $item) {
            $stmt_stock->execute([$item['Cantidad'], $item['IdProducto']]);
        }
        $log_msg = "Pedido #$id_pedido RE-ACTIVADO (Stock descontado).";
    }

    log_event('PEDIDO_ESTADO_CAMBIADO', $log_msg, __FILE__);
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

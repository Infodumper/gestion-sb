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

    $id_cliente = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
    $items = isset($_POST['items']) ? $_POST['items'] : [];

    if ($id_cliente <= 0) {
        throw new Exception('Debe seleccionar un cliente válido.');
    }

    if (empty($items)) {
        throw new Exception('La nota debe tener al menos un producto.');
    }

    $pdo->beginTransaction();

    // 1. Insertar en pedidos
    $id_vendedor = 1; // Asumimos el vendedor por defecto creado en la restauración
    $stmt_nota = $pdo->prepare("INSERT INTO pedidos (IdVendedor, IdCliente, Fecha, Estado) VALUES (?, ?, NOW(), 'Activo')");
    $stmt_nota->execute([$id_vendedor, $id_cliente]);
    $id_pedido = $pdo->lastInsertId();

    // 2. Insertar Detalles
    $stmt_det = $pdo->prepare("INSERT INTO detalle_pedidos (IdPedido, IdProducto, Cantidad, PrecioUnitario) VALUES (?, ?, ?, ?)");
    
    $total_general = 0;
    foreach ($items as $item) {
        $id_prod = intval($item['id_servicio']); // El ID viene del selector de productos
        $cant = intval($item['cantidad']);
        $precio = floatval($item['precio']);
        
        if ($id_prod > 0 && $cant > 0) {
            $stmt_det->execute([$id_pedido, $id_prod, $cant, $precio]);
            
            // --- ACTUALIZACIÓN DE STOCK ---
            // Restar del stock disponible
            $stmt_stock = $pdo->prepare("UPDATE productos SET Stock = Stock - ? WHERE IdProducto = ?");
            $stmt_stock->execute([$cant, $id_prod]);
            
            $total_general += ($precio * $cant);
        }
    }

    // Actualizar el total en el pedido principal
    $pdo->prepare("UPDATE pedidos SET Total = ? WHERE IdPedido = ?")->execute([$total_general, $id_pedido]);

    // 3. Log de auditoría
    log_event('PEDIDO_CREADO', "Pedido #$id_pedido para Cliente ID: $id_cliente. Total: $total_general", __FILE__);

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Pedido guardado correctamente.',
        'id_pedido' => $id_pedido
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

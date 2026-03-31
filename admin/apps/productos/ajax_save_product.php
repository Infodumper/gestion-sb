<?php
session_start();
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}
require_once '../../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $id_proveedor = intval($_POST['id_proveedor'] ?? 0) ?: null;

    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE productos SET Codigo = ?, Nombre = ?, Descripcion = ?, Precio = ?, Stock = ?, IdProveedor = ? WHERE IdProducto = ?");
            $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $id_proveedor, $id]);
            echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO productos (Codigo, Nombre, Descripcion, Precio, Stock, IdProveedor, Estado) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock, $id_proveedor]);
            $new_id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Producto creado correctamente', 'id' => $new_id]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

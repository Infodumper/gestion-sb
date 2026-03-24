<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['userid'])) { 
    echo json_encode(['exists' => false]);
    exit; 
}
require_once '../../../includes/db.php';

$telefono = preg_replace('/[^0-9]/', '', $_GET['telefono'] ?? '');
if (strlen($telefono) < 6) { 
    echo json_encode(['exists' => false]); 
    exit; 
}

try {
    $stmt = $pdo->prepare("SELECT IdCliente, Nombre, Apellido FROM clientes WHERE Telefono = ? LIMIT 1");
    $stmt->execute([$telefono]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['exists' => true, 'nombre' => $r['Nombre'].' '.$r['Apellido']]);
    } else {
        echo json_encode(['exists' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['exists' => false]);
}
?>

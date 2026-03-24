<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
    exit;
}

require_once '../../../includes/db.php';
require_once '../../../includes/security.php';

$idCliente = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

if ($idCliente <= 0 || empty($tipo)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

try {
    // 1. Determinar intervalo de validez para el toggle
    // Cumple: Solo HOY
    // Habitual: Este MES
    if ($tipo === 'cumple') {
        $checkSql = "SELECT IdContacto FROM contactoswhatsapp WHERE IdCliente = ? AND Tipo = ? AND DATE(FechaContacto) = CURDATE()";
    } else {
        $checkSql = "SELECT IdContacto FROM contactoswhatsapp WHERE IdCliente = ? AND Tipo = ? AND MONTH(FechaContacto) = MONTH(CURDATE()) AND YEAR(FechaContacto) = YEAR(CURDATE())";
    }

    $stmt = $pdo->prepare($checkSql);
    $stmt->execute([$idCliente, $tipo]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Ya existe -> Eliminar (Toggle OFF)
        $del = $pdo->prepare("DELETE FROM contactoswhatsapp WHERE IdContacto = ?");
        $del->execute([$existing['IdContacto']]);
        $marked = false;
        $label = "ELIMINADO";
    } else {
        // No existe -> Isertar (Toggle ON)
        $ins = $pdo->prepare("INSERT INTO contactoswhatsapp (IdCliente, Tipo) VALUES (?, ?)");
        $ins->execute([$idCliente, $tipo]);
        $marked = true;
        $label = "CREADO";
    }
    
    log_event($pdo, 'WA_CONTACTO', "ID Cliente: $idCliente - Tipo: $tipo - Acción: $label");
    
    echo json_encode(['success' => true, 'marked' => $marked]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

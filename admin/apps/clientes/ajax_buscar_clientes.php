<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once '../../../includes/db.php';
require_once '../../../includes/security.php';

$term = $_GET['term'] ?? '';

if (empty($term)) {
    echo json_encode([]);
    exit;
}

$f = "%$term%";

try {
    // Usar la estructura real detectada - Solo activos
    $stmt = $pdo->prepare("SELECT IdCliente, Nombre, Apellido FROM clientes WHERE (Apellido LIKE ? OR Nombre LIKE ? OR Dni LIKE ?) AND Estado = 1 LIMIT 15");
    $stmt->execute([$f, $f, $f]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted = [];
    foreach ($results as $row) {
        $formatted[] = [
            'id' => (int)$row['IdCliente'],
            'label' => s($row['Apellido']) . ' ' . s($row['Nombre'])
        ];
    }

    echo json_encode($formatted, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error SQL: ' . $e->getMessage()]);
}
?>

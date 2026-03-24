<?php
// Desactivar reporte de errores para evitar ensuciar el JSON
error_reporting(0);
ini_set('display_errors', 0);

ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once '../../../includes/db.php';

if (!$pdo) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($term)) {
    echo json_encode([]);
    exit;
}

try {
    $f = "%$term%";
    
    // Búsqueda por Nombre, Código o Proveedor
    $sql = "SELECT p.IdProducto, p.Codigo, p.Nombre, p.Precio, p.Stock, prov.NombreComercial as Proveedor 
            FROM productos p 
            LEFT JOIN proveedores prov ON p.IdProveedor = prov.IdProveedor 
            WHERE (p.Nombre LIKE ? OR p.Codigo LIKE ? OR prov.NombreComercial LIKE ?) 
            AND p.Estado = 1
            LIMIT 15";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$f, $f, $f]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted = [];
    foreach ($results as $row) {
        $nombre = $row['Nombre'];
        $codigo = $row['Codigo'] ? $row['Codigo'] . " - " : "";
        $prov = $row['Proveedor'] ? " [" . $row['Proveedor'] . "]" : "";
        
        $formatted[] = [
            'id' => (int)$row['IdProducto'],
            'codigo' => $row['Codigo'],
            'nombre' => $nombre,
            'precio' => (float)$row['Precio'],
            'stock' => (int)$row['Stock'],
            'label' => $codigo . $nombre . $prov . " (Stock: " . $row['Stock'] . ") - $" . number_format((float)$row['Precio'], 2, ',', '.')
        ];
    }
    
    ob_clean();
    echo json_encode($formatted, JSON_UNESCAPED_UNICODE);
    exit;

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error SQL: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error General: ' . $e->getMessage()]);
}
?>

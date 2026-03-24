<?php
require_once '../includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE Clientes");
    echo "COLUMNS:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    $stmt2 = $pdo->query("SHOW INDEX FROM Clientes");
    echo "\nINDEXES:\n";
    print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

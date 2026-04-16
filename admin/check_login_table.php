<?php
require_once '../includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE dblogin");
    echo "COLUMNS in dblogin:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    $stmt2 = $pdo->query("SELECT COUNT(*) as TOTAL FROM dblogin");
    print_r($stmt2->fetch(PDO::FETCH_ASSOC));
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

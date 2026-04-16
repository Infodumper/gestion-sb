<?php
require_once '../includes/db.php';
try {
    $tableName = 'dblogin';
    $stmt = $pdo->query("DESC $tableName");
    echo "COLUMNS of $tableName:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error primary: " . $e->getMessage();
    try {
        $tableName = 'dblogin';
        $stmt = $pdo->query("DESC $tableName");
        echo "COLUMNS of fallback $tableName:\n";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Exception $e2) {
        echo "Error fallback: " . $e2->getMessage();
    }
}
?>

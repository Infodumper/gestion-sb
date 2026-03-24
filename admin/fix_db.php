<?php
require_once '../includes/db.php';
try {
    // Intentar eliminar el índice Unique_DNI si existe
    $pdo->exec("ALTER TABLE Clientes DROP INDEX Unique_DNI");
    echo "Índice Unique_DNI eliminado correctamente.";
} catch (Exception $e) {
    echo "Error al eliminar el índice: " . $e->getMessage();
    // Probablemente no existe o tiene otro nombre. Intentemos buscar cómo se llama.
    try {
        $stmt = $pdo->query("SHOW INDEX FROM Clientes WHERE Column_name = 'Dni'");
        $indexes = $stmt->fetchAll();
        foreach($indexes as $idx) {
            $name = $idx['Key_name'];
            $pdo->exec("ALTER TABLE Clientes DROP INDEX $name");
            echo "<br>Eliminado índice alternativo: $name";
        }
    } catch (Exception $e2) {
        echo "<br>Error crítico: " . $e2->getMessage();
    }
}

// También asegurar que permita NULL y guardarlo como tal en el código
try {
    $pdo->exec("ALTER TABLE Clientes MODIFY Dni VARCHAR(20) NULL");
    echo "<br>Columna Dni modificada para permitir NULL.";
} catch (Exception $e) {
    echo "<br>Error al modificar columna: " . $e->getMessage();
}
?>

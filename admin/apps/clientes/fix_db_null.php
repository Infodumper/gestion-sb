<?php
/**
 * Parche temporal para permitir teléfonos NULL en la base de datos.
 * Visitar una vez y luego borrar.
 */
require_once '../../../includes/db.php';

try {
    // MySQL/MariaDB syntax para permitir nulos en la columna Telefono
    $sql = "ALTER TABLE clientes MODIFY Telefono VARCHAR(50) NULL";
    $pdo->exec($sql);
    
    echo "<div style='font-family: Arial; padding: 20px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px;'>";
    echo "<h2>¡Éxito!</h2>";
    echo "<p>La base de datos ha sido actualizada. Ahora el teléfono es opcional y puede quedar vacío.</p>";
    echo "<p><strong>Por favor, borra este archivo (fix_db_null.php) de tu servidor por seguridad.</strong></p>";
    echo "<a href='../../index.php' style='display: inline-block; padding: 10px 20px; background: #166534; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Volver al Panel</a>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='font-family: Arial; padding: 20px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px;'>";
    echo "<h2>Error</h2>";
    echo "<p>No se pudo actualizar la base de datos: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<?php
/**
 * Setup Admin User
 * Run this ONCE in your browser to create the first admin.
 * URL: http://localhost/gestion-sb/admin/setup_admin.php
 */

require_once '../includes/db.php';

try {
    // 1. Clear existing users (as requested)
    $pdo->exec("TRUNCATE TABLE DbLogin");

    // 2. Create the new admin
    $email = 'infodumper.au@gmail.com';
    $pass = 'ID9800';
    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
    $nombre = 'Infodumper';

    $stmt = $pdo->prepare("INSERT INTO DbLogin (Usuario, Clave, Nombre, Rol, Estado) VALUES (?, ?, ?, 'admin', 1)");
    $stmt->execute([$email, $hashedPass, $nombre]);

    echo "<h1>✅ Configuración Exitosa</h1>";
    echo "<p>Usuario <strong>$email</strong> creado con la contraseña solicitada.</p>";
    echo "<p><a href='login.php'>Ir al Login</a></p>";

    // OPTIONAL: Delete this file after use for security
    // unlink(__FILE__);

} catch (PDOException $e) {
    echo "<h1>❌ Error de Setup</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

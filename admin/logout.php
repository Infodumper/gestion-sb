<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

log_event($pdo, 'LOGOUT', 'Usuario cerró sesión');

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>

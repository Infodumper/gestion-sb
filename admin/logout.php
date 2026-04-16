<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

log_event('LOGOUT', 'Usuario cerró sesión', __FILE__);

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>

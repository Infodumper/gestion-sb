<?php
/**
 * Basic .env loader for native PHP
 * Loads variables from ../.env (or relative to current script)
 * Optimized for restricted hosting environments (By-passing putenv restrictions)
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Inyectar directamente en superglobales (más compatible con hostings)
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        
        if (function_exists('putenv')) {
            @putenv(sprintf('%s=%s', $name, $value));
        }
    }
    return true;
}

// Load it relative to this file
loadEnv(__DIR__ . '/../.env');
?>

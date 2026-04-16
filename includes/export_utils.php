<?php
/**
 * Utilidades de Exportación - Gestión SB
 */

/**
 * Genera un string CSV a partir de un array de datos
 */
function generate_csv_string($data, $headers = []) {
    $output = fopen('php://temp', 'r+');
    
    if (!empty($headers)) {
        fputcsv($output, $headers, ';');
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row, ';');
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

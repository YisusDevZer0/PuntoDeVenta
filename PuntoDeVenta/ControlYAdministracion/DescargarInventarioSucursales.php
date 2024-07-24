<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=test.csv');

$output = fopen('php://output', 'w');
if (!$output) {
    die("Error al abrir el flujo de salida.");
}

fputcsv($output, ['Columna1', 'Columna2']);
fputcsv($output, ['Dato1', 'Dato2']);

fclose($output);
?>

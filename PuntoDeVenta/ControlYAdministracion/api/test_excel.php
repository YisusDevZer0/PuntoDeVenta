<?php
// Archivo de prueba para verificar la descarga de Excel
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simular sesión de usuario para pruebas
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1;
    $_SESSION['usuario_nombre'] = 'Usuario de Prueba';
}

try {
    // Cargar PhpSpreadsheet
    require_once '../../../vendor/autoload.php';
    
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
    // Crear un spreadsheet simple
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Agregar datos de prueba
    $sheet->setCellValue('A1', 'Prueba de Descarga Excel');
    $sheet->setCellValue('A2', 'Fecha: ' . date('Y-m-d H:i:s'));
    $sheet->setCellValue('A3', 'Estado: Funcionando correctamente');
    
    // Configurar headers para descarga
    $filename = 'test_excel_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Generar y enviar archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
}
?>


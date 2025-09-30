<?php
// Archivo de prueba para verificar que las respuestas JSON sean válidas
header('Content-Type: application/json; charset=utf-8');

// Simular datos POST como los que enviaría el formulario
$_POST = [
    'IdBasedatos' => ['TEST001'],
    'CodBarras' => ['123456789'],
    'NombreDelProducto' => ['Producto Test'],
    'Fk_sucursal' => ['1'],
    'PrecioVenta' => ['10.50'],
    'PrecioCompra' => ['8.00'],
    'Contabilizado' => ['5'],
    'StockActual' => ['5'],
    'Diferencia' => ['0'],
    'Sistema' => ['SISTEMA'],
    'AgregoElVendedor' => ['TEST_USER'],
    'ID_H_O_D' => ['1'],
    'FechaInv' => [date('Y-m-d H:i:s')],
    'Tipodeajusteaplicado' => ['AJUSTE'],
    'AnaquelSeleccionado' => ['A1'],
    'RepisaSeleccionada' => ['R1']
];

// Capturar la salida del archivo principal
ob_start();
include 'RegistraInventariosSucursales.php';
$output = ob_get_clean();

// Verificar si la salida es JSON válido
$decoded = json_decode($output, true);
$isValidJson = (json_last_error() === JSON_ERROR_NONE);

$testResult = [
    'test_status' => $isValidJson ? 'success' : 'error',
    'is_valid_json' => $isValidJson,
    'json_error' => $isValidJson ? null : json_last_error_msg(),
    'output_length' => strlen($output),
    'output_preview' => substr($output, 0, 200) . '...',
    'decoded_response' => $decoded,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($testResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>

<?php
// Archivo de prueba para verificar que la corrección de tipos de datos funciona
header('Content-Type: application/json; charset=utf-8');

// Simular datos POST como los que enviaría el formulario
$_POST = [
    'IdBasedatos' => ['TEST001', 'TEST002'],
    'CodBarras' => ['123456789', '987654321'],
    'NombreDelProducto' => ['Producto Test 1', 'Producto Test 2'],
    'Fk_sucursal' => ['1', '1'],
    'PrecioVenta' => ['10.50', '15.75'],
    'PrecioCompra' => ['8.00', '12.00'],
    'Contabilizado' => ['5', '3'],
    'StockActual' => ['5', '3'],
    'Diferencia' => ['0', '0'],
    'Sistema' => ['SISTEMA', 'SISTEMA'],
    'AgregoElVendedor' => ['TEST_USER', 'TEST_USER'],
    'ID_H_O_D' => ['1', '1'],
    'FechaInv' => [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
    'Tipodeajusteaplicado' => ['AJUSTE', 'AJUSTE'],
    'AnaquelSeleccionado' => ['A1', 'A2'],
    'RepisaSeleccionada' => ['R1', 'R2']
];

// Incluir el archivo principal
try {
    ob_start();
    include 'RegistraInventariosSucursales.php';
    $output = ob_get_clean();
    
    $response = [
        'status' => 'success',
        'message' => 'Prueba completada exitosamente',
        'output' => $output,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error en la prueba: ' . $e->getMessage(),
        'code' => 'TEST_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ];
} catch (Error $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error fatal en la prueba: ' . $e->getMessage(),
        'code' => 'TEST_FATAL_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

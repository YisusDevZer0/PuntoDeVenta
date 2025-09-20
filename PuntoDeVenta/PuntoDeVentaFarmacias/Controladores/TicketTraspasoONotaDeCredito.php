<?php
// Archivo para generar tickets de traspasos y notas de crédito
// Este archivo maneja la generación de tickets para el módulo de PuntoDeVentaFarmacias

include_once "db_connect.php";

// Verificar que se recibieron datos
if (!isset($_POST['Folio_Ticket']) || empty($_POST['Folio_Ticket'])) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió el folio del ticket']);
    exit;
}

// Obtener datos del traspaso
$folio_ticket = $_POST['Folio_Ticket'][0]; // Tomar el primer folio
$tipo_movimiento = $_POST['TipoDeMov'][0] ?? 'Traspaso';
$sucursal_origen = $_POST['Fk_sucursal'][0] ?? '';
$sucursal_destino = $_POST['Fk_SucursalDestino'][0] ?? '';
$fecha_venta = $_POST['FechaVenta'][0] ?? date('Y-m-d');
$agregado_por = $_POST['AgregadoPor'][0] ?? '';

// Aquí puedes agregar la lógica para generar el ticket
// Por ejemplo, generar un PDF, imprimir, o simplemente confirmar

try {
    // Simular generación exitosa del ticket
    $response = [
        'status' => 'success',
        'message' => 'Ticket generado correctamente',
        'data' => [
            'folio' => $folio_ticket,
            'tipo' => $tipo_movimiento,
            'fecha' => $fecha_venta,
            'sucursal_origen' => $sucursal_origen,
            'sucursal_destino' => $sucursal_destino,
            'usuario' => $agregado_por
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error al generar el ticket: ' . $e->getMessage()
    ]);
}
?>

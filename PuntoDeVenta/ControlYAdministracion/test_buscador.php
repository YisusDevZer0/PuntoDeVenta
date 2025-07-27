<?php
// Archivo de prueba para verificar el buscador
header('Content-Type: application/json');

// Simular una búsqueda simple
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Datos de prueba
$productos = [
    [
        'ID_Prod_POS' => 1,
        'Nombre_Prod' => 'Paracetamol 500mg',
        'Cod_Barra' => '123456789',
        'Precio_Venta' => 15.50,
        'Existencias_R' => 100,
        'Min_Existencia' => 50,
        'Max_Existencia' => 200,
        'Estatus' => 'Activo',
        'stock_status' => 'normal'
    ],
    [
        'ID_Prod_POS' => 2,
        'Nombre_Prod' => 'Ibuprofeno 400mg',
        'Cod_Barra' => '987654321',
        'Precio_Venta' => 12.75,
        'Existencias_R' => 25,
        'Min_Existencia' => 50,
        'Max_Existencia' => 150,
        'Estatus' => 'Activo',
        'stock_status' => 'bajo'
    ]
];

echo json_encode([
    'success' => true,
    'productos' => $productos,
    'total' => count($productos),
    'query' => $query,
    'test' => true
]);
?> 
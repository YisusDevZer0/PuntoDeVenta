<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    ia.IDIngreso, 
    ia.ID_Prod_POS, 
    ia.NumFactura, 
    ia.Proveedor, 
    ia.Cod_Barra, 
    ia.Nombre_Prod, 
    ia.Fk_Sucursal, 
    s.Nombre_Sucursal,
    ia.Contabilizado, 
    ia.Fecha_Caducidad, 
    ia.Lote, 
    ia.PrecioMaximo, 
    ia.Precio_Venta, 
    ia.Precio_C, 
    ia.PrecioVentaAutorizado, 
    ia.AgregadoPor, 
    ia.AgregadoEl, 
    ia.FechaInventario, 
    ia.Estatus, 
    ia.NumOrden,
    ia.SolicitadoPor
FROM 
    IngresosAutorizados ia
JOIN 
    Sucursales s ON ia.Fk_Sucursal = s.ID_Sucursal
WHERE 
    MONTH(ia.AgregadoEl) = MONTH(CURRENT_DATE) AND YEAR(ia.AgregadoEl) = YEAR(CURRENT_DATE)";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Definir el estilo y el texto según el valor de estatus
    $estatus_estilo = '';
    $estatus_leyenda = '';
    switch ($fila["Estatus"]) {
        case 'Ingresado':
            $estatus_estilo = 'background-color: #008080; color: white;'; // Verde marino
            $estatus_leyenda = 'Ingresado';
            break;
        case 'Pendiente':
            $estatus_estilo = 'background-color: red; color: white;'; // Rojo
            $estatus_leyenda = 'Pendiente';
            break;
        default:
            $estatus_estilo = ''; // No se aplica estilo
            $estatus_leyenda = $fila["Estatus"];
            break;
    }
    $realizar_corte = '<td><a data-id="' . $fila["IDIngreso"] . '" class="btn btn-success btn-sm btn-AutorizaIngreso" style="color:white;"><i class="fa-solid fa-check"></i></a></td>';
    // Construir el array de datos incluyendo las columnas de la consulta
    $data[] = [
        "IDIngreso" => $fila["IDIngreso"],
        "NumFactura" => $fila["NumFactura"],
        "Proveedor" => $fila["Proveedor"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Contabilizado" => $fila["Contabilizado"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "FechaInventario" => $fila["FechaInventario"],
        "RealizarCorte" => $realizar_corte,
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>

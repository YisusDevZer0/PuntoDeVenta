<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    tg.ID_Traspaso_Generado, 
    tg.Num_Orden, 
    tg.Num_Factura, 
    tg.Cod_Barra, 
    tg.Nombre_Prod, 
    tg.Fk_SucDestino, 
    tg.Precio_Venta, 
    tg.Precio_Compra, 
    tg.Cantidad_Enviada, 
    tg.FechaEntrega, 
    tg.TraspasoGeneradoPor, 
    tg.TraspasoRecibidoPor, 
    tg.Estatus, 
    tg.AgregadoPor, 
    tg.AgregadoEl, 
    tg.ID_H_O_D, 
    tg.TotaldePiezas, 
    tg.Fecha_recepcion,
    s.Nombre_Sucursal AS SucursalDestino
FROM 
    Traspasos_generados tg
INNER JOIN 
    Sucursales s ON tg.Fk_SucDestino = s.ID_Sucursal
WHERE 
    MONTH(tg.AgregadoEl) = MONTH(CURRENT_DATE) AND YEAR(tg.AgregadoEl) = YEAR(CURRENT_DATE)";

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
    // Construir el array de datos
    $data[] = [
        "ID_Traspaso_Generado" => $fila["ID_Traspaso_Generado"],
        "Num_Orden" => $fila["Num_Orden"],
        "Num_Factura" => $fila["Num_Factura"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Fk_SucDestino" => $fila["Fk_SucDestino"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Precio_Compra" => $fila["Precio_Compra"],
        "Cantidad_Enviada" => $fila["Cantidad_Enviada"],
        "FechaEntrega" => $fila["FechaEntrega"],
        "TraspasoGeneradoPor" => $fila["TraspasoGeneradoPor"],
        "TraspasoRecibidoPor" => $fila["TraspasoRecibidoPor"],
        "Estatus" => $fila["Estatus"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "ID_H_O_D" => $fila["ID_H_O_D"],
        "TotaldePiezas" => $fila["TotaldePiezas"],
        "Fecha_recepcion" => $fila["Fecha_recepcion"],
        "SucursalDestino" => $fila["SucursalDestino"],
        "Editar" => "<a href='https://saludapos.com/AdminPOS/CoincidenciaSucursales?Disid=" . base64_encode($fila["ID_Traspaso_Generado"]) . "' type='button' class='btn btn-info btn-sm'><i class='fas fa-edit'></i></a>",
        "Eliminar" => "<a href='https://saludapos.com/AdminPOS/ActualizaOne?idProd=" . base64_encode($fila["ID_Traspaso_Generado"]) . "' type='button' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a>",
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

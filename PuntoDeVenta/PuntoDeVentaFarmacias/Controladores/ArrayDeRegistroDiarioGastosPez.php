<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");
// Obtener la fecha actual en formato 'YYYY-MM-DD'
$fechaActual = date("Y-m-d");

// Consulta SQL para obtener los gastos
$sql = "SELECT 
    gp.ID_Gastos,
    gp.Concepto_Categoria,
    gp.Importe_Total,
    gp.Empleado,
    s.Nombre_Sucursal,
    gp.Recibe,
    gp.Sistema,
    gp.AgregadoPor,
    gp.AgregadoEl,
    gp.Licencia,
    gp.FechaConcepto
FROM 
    GastosPOS gp
INNER JOIN 
    Sucursales s ON gp.Fk_sucursal = s.ID_Sucursal
INNER JOIN 
    Cajas c ON gp.Fk_Caja = c.ID_Caja
WHERE 
    DATE(gp.FechaConcepto) = '$fechaActual'";

// Ejecutar la consulta
$result = mysqli_query($conn, $sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $data[] = [
            "ID_Gastos" => $fila["ID_Gastos"],
            "Concepto_Categoria" => $fila["Concepto_Categoria"],
            "Importe_Total" => number_format($fila["Importe_Total"], 2),
            "Empleado" => $fila["Empleado"],
            "Sucursal" => $fila["Nombre_Sucursal"],
            "Recibe" => $fila["Recibe"],
            "Sistema" => $fila["Sistema"],
            "AgregadoPor" => $fila["AgregadoPor"],
            "AgregadoEl" => $fila["AgregadoEl"],
            "Licencia" => $fila["Licencia"],
            "FechaConcepto" => $fila["FechaConcepto"]
        ];
    }
}

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar la conexión
$conn->close();
?>

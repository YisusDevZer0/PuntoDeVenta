<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
        Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
        Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
        FROM Cajas
        INNER JOIN Sucursales ON Cajas.Sucursal = Sucursales.ID_Sucursal
        WHERE Cajas.Sucursal = ?"; // Se cambió la condición WHERE para utilizar Fk_Sucursal

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Obtener el valor de Fk_Sucursal
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Vincular parámetro
$stmt->bind_param("s", $fk_sucursal);

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
        "IdCaja" => $fila["ID_Caja"],
        "Empleado" => $fila["Empleado"],
        "Cantidad_Fondo" => $fila["Cantidad_Fondo"],
        "Fecha_Apertura" => $fila["Fecha_Apertura"],
        "Estatus" => $fila["Estatus"],
        "Turno" => $fila["Turno"],
        "Asignacion" => $fila["Asignacion"],
        "ValorTotalCaja" => $fila["Valor_Total_Caja"]
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

<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
G.`ID_Gastos`, 
G.`Concepto_Categoria`, 
G.`Importe_Total`, 
G.`Empleado`, 
S.`nombre_sucursal`, -- Selecciona el nombre de la sucursal
G.`Fk_Caja`, 
G.`Recibe`, 
G.`Sistema`, 
G.`AgregadoPor`, 
G.`AgregadoEl`, 
G.`Licencia`
FROM 
GastosPOS G
JOIN 
Sucursales S ON G.`Fk_sucursal` = S.`ID_Sucursal` AND G.Licencia = ?";
 
// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetro
$stmt->bind_param("s", $licencia);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Construir el array de datos
    // Aquí puedes seguir con la lógica que ya tienes para construir los datos de salida
    
    $data[] = [
        "Idpersonal" => $fila["ID_Gastos"],
        "NombreApellidos" => $fila["Concepto_Categoria"],
        "Foto" => $fila["Importe_Total"],
        "Tipousuario" => $fila["Empleado"],
        "Sucursal" => $fila["nombre_sucursal"],
        "CreadoEl" => $fila["Recibe"], // Cambiado de "CreadoPorEl" a "CreadoEl"
        "Estatus" => $fila["AgregadoEl"],
        "CreadoPor" => $fila["AgregadoPor"],
        // Agregar el botón Desglosar ticket
        /* "Editar" => '<td><a data-id="' . $fila["Id_PvUser"] . '" class="btn btn-success btn-sm btn-edita " style="background-color: #0172b6 !important;" ><i class="fa-solid fa-pen-to-square"></i></a></td>',
        "Eliminar" => '<td><a data-id="' . $fila["Id_PvUser"] . '" class="btn btn-danger btn-sm btn-elimina " style="background-color: #ff3131 !important;" ><i class="fa-solid fa-trash"></i></a></td>'
    */ ];
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

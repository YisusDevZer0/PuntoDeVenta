<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
WHERE Usuarios_PV.Estatus = 'Activo' AND Usuarios_PV.Licencia = ?";
 
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
        "Idpersonal" => $fila["Id_PvUser"],
        "NombreApellidos" => $fila["Nombre_Apellidos"],
        "Foto" => $fila["file_name"],
        "Tipousuario" => $fila["TipoUsuario"],
        "Sucursal" => $fila["Nombre_Sucursal"],
        "CreadoEl" => $fila["AgregadoEl"], // Cambiado de "CreadoPorEl" a "CreadoEl"
        "Estatus" => $fila["Estatus"],
        "CreadoPor" => $fila["AgregadoPor"],
        // Agregar el botón Desglosar ticket
        "Editar" => '<td><a data-id="' . $fila["Id_PvUser"] . '" class="btn btn-success btn-sm btn-edita " style="background-color: #0172b6 !important;" ><i class="fa-solid fa-pen-to-square"></i></a></td>',
        "Eliminar" => '<td><a data-id="' . $fila["Id_PvUser"] . '" class="btn btn-danger btn-sm btn-elimina " style="background-color: #ff3131 !important;" ><i class="fa-solid fa-trash"></i></a></td>'
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

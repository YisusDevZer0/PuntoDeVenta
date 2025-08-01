<?php
header('Content-Type: application/json');

try {
    include("db_connect.php");
    
    // Consulta simple sin filtros
    $sql = "SELECT 
        Usuarios_PV.Id_PvUser,
        Usuarios_PV.Nombre_Apellidos,
        Usuarios_PV.file_name,
        Usuarios_PV.Fecha_Nacimiento,
        Usuarios_PV.Correo_Electronico,
        Usuarios_PV.Telefono,
        Usuarios_PV.AgregadoPor,
        Usuarios_PV.AgregadoEl,
        Usuarios_PV.Estatus,
        Tipos_Usuarios.TipoUsuario,
        Sucursales.Nombre_Sucursal
    FROM Usuarios_PV 
    INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
    INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
    WHERE Usuarios_PV.Estatus = 'Activo'
    ORDER BY Usuarios_PV.Id_PvUser DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $conn->error);
    }

    $data = [];

    while ($fila = $result->fetch_assoc()) {
        // Formatear fecha de nacimiento
        $fecha_nacimiento = '';
        if ($fila["Fecha_Nacimiento"]) {
            $fecha_nacimiento = date('d/m/Y', strtotime($fila["Fecha_Nacimiento"]));
        }
        
        // Formatear fecha de creación
        $fecha_creacion = '';
        if ($fila["AgregadoEl"]) {
            $fecha_creacion = date('d/m/Y H:i', strtotime($fila["AgregadoEl"]));
        }
        
        // Construir el array de datos
        $data[] = [
            "Idpersonal" => $fila["Id_PvUser"],
            "NombreApellidos" => $fila["Nombre_Apellidos"],
            "Foto" => '<img src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/' . $fila["file_name"] . '" alt="Foto" class="profile-img" onerror="this.src=\'https://doctorpez.mx/PuntoDeVenta/PerfilesImg/Administrativos.jpeg\'">',
            "Tipousuario" => $fila["TipoUsuario"],
            "Sucursal" => $fila["Nombre_Sucursal"],
            "CorreoElectronico" => $fila["Correo_Electronico"] ?: 'No especificado',
            "Telefono" => $fila["Telefono"] ?: 'No especificado',
            "FechaNacimiento" => $fecha_nacimiento,
            "CreadoEl" => $fecha_creacion,
            "Estatus" => $fila["Estatus"],
            "CreadoPor" => $fila["AgregadoPor"] ?: 'Sistema'
        ];
    }

    // Construir el array de resultados para la respuesta JSON
    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];

    echo json_encode($results);
    $conn->close();

} catch (Exception $e) {
    error_log('Error en ArrayUsuariosVigentes.php: ' . $e->getMessage());
    
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => [],
        "error" => "Error al cargar los datos: " . $e->getMessage()
    ]);
}
?>

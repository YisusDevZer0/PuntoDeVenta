<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

// Obtener parámetros de filtro desde POST (DataTables envía por POST)
$tipo = '';
$sucursal = '';
$estado = '';

// Buscar los parámetros en el array $_POST
foreach ($_POST as $key => $value) {
    if ($key === 'tipo') {
        $tipo = $value;
    } elseif ($key === 'sucursal') {
        $sucursal = $value;
    } elseif ($key === 'estado') {
        $estado = $value;
    }
}

// Construir la consulta SQL base
$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
WHERE Usuarios_PV.Estatus = 'Activo'";

// Aplicar filtros
if (!empty($tipo)) {
    $sql .= " AND Tipos_Usuarios.TipoUsuario = '" . mysqli_real_escape_string($conn, $tipo) . "'";
}
if (!empty($sucursal)) {
    $sql .= " AND Sucursales.ID_Sucursal = '" . mysqli_real_escape_string($conn, $sucursal) . "'";
}
if (!empty($estado)) {
    $sql .= " AND Usuarios_PV.Estatus = '" . mysqli_real_escape_string($conn, $estado) . "'";
}

$sql .= " ORDER BY Usuarios_PV.Id_PvUser DESC";
 
$result = mysqli_query($conn, $sql);
 
$c = 0;
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Idpersonal"] = $fila["Id_PvUser"];
    $data[$c]["NombreApellidos"] = $fila["Nombre_Apellidos"];
    
    // Mostrar foto del usuario
    if (!empty($fila["file_name"])) {
        $data[$c]["Foto"] = '<img src="../PerfilesImg/' . $fila["file_name"] . '" class="rounded-circle" width="40" height="40" alt="Foto">';
    } else {
        $data[$c]["Foto"] = '<img src="../PerfilesImg/Devzero.jpg" class="rounded-circle" width="40" height="40" alt="Foto por defecto">';
    }
    
    $data[$c]["Tipousuario"] = '<span class="badge bg-primary">' . $fila["TipoUsuario"] . '</span>';
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["CreadoEl"] = date('d/m/Y H:i', strtotime($fila["AgregadoEl"]));
    $data[$c]["Estatus"] = '<span class="badge bg-success">' . $fila["Estatus"] . '</span>';
    $data[$c]["CreadoPor"] = $fila["AgregadoPor"];
    
    // Acciones mejoradas para el personal
    $acciones = '
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cogs fa-1x"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item btn-edita" href="#" data-id="' . $fila["Id_PvUser"] . '">
                    <i class="fas fa-edit me-2"></i>Editar datos
                </a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStock?idProd=' . base64_encode($fila["Id_PvUser"]) . '">
                    <i class="fas fa-clinic-medical me-2"></i>Asignar sucursales
                </a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/DistribucionSucursales?Disid=' . base64_encode($fila["Id_PvUser"]) . '">
                    <i class="fas fa-table me-2"></i>Consultar distribución
                </a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/HistorialProducto?idProd=' . base64_encode($fila["Id_PvUser"]) . '">
                    <i class="fas fa-history me-2"></i>Ver historial
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item btn-elimina" href="#" data-id="' . $fila["Id_PvUser"] . '" style="color: #dc3545;">
                    <i class="fas fa-trash me-2"></i>Eliminar usuario
                </a></li>
            </ul>
        </div>
    ';
    
    $data[$c]["Acciones"] = $acciones;
    
    $c++; 
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];
 
echo json_encode($results);
?>

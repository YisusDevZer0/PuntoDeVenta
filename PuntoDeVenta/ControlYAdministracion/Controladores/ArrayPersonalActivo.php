<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
WHERE Usuarios_PV.Estatus = 'Activo'";
 
$result = mysqli_query($conn, $sql);
 
$c = 0;
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Idpersonal"] = $fila["Id_PvUser"];
    $data[$c]["NombreApellidos"] = $fila["Nombre_Apellidos"];
    $data[$c]["Foto"] = $fila["file_name"];
    $data[$c]["Tipousuario"] = $fila["TipoUsuario"];
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["CreadoEl"] = $fila["AgregadoEl"];
    $data[$c]["Estatus"] = $fila["Estatus"];
    $data[$c]["CreadoPor"] = $fila["AgregadoPor"];
    
    $acciones = '
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-th-list fa-1x"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStock?idProd='.base64_encode($fila["Id_PvUser"]).'">Asignar en sucursales <i class="fas fa-clinic-medical"></i></a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/DistribucionSucursales?Disid='.base64_encode($fila["Id_PvUser"]).'">Consultar distribución <i class="fas fa-table"></i></a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/EdicionDatosProducto?editprod='.base64_encode($fila["Id_PvUser"]).'">Editar datos <i class="fas fa-pencil-alt"></i></a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/HistorialProducto?idProd='.base64_encode($fila["Id_PvUser"]).'">Ver movimientos <i class="fas fa-history"></i></a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/MaximoYMinimo?Disid='.base64_encode($fila["Id_PvUser"]).'">Actualizar mínimo y máximo <i class="fas fa-list-ol"></i></a></li>
                <li><a class="dropdown-item" href="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/CambiaProveedor?idProd='.base64_encode($fila["Id_PvUser"]).'">Cambio de proveedores <i class="fas fa-truck-loading"></i></a></li>
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

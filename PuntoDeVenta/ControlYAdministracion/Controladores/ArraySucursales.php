<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";
$nombreLicencia = $row['Nombre_Licencia'];

$sql = "SELECT ID_Sucursal, Nombre_Sucursal, Direccion, CP, RFC, Licencia, Identificador, 
Telefono, Pin_Equipo, Sucursal_Activa, Agrego, AgregadoEl, NombreImpresora 
FROM Sucursales 
WHERE Licencia = '" . $nombreLicencia . "'";
 
$result = mysqli_query($conn, $sql);
 
$c = 0;
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Idsucursal"] = $fila["Id_PvUser"];
    $data[$c]["NombreSucursal"] = $fila["Nombre_Apellidos"];
    $data[$c]["Direccion"] = $fila["file_name"];
    $data[$c]["Telefono"] = $fila["TipoUsuario"];
    $data[$c]["Pin"] = $fila["Nombre_Sucursal"];
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

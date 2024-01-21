
<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";


$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
 Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
  Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
  FROM Usuarios_PV INNER JOIN Tipos_Usuarios ON 
  Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User INNER JOIN Sucursales
   ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal;";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
    $data[$c]["Idpersonal"] = $fila["Id_PvUser"];
    $data[$c]["NombreApellidos"] = $fila["Nombre_Apellidos"];
    $data[$c]["Foto"] = $fila["file_name"];
    $data[$c]["Tipousuario"] = $fila["TipoUsuario"];
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["CreadoEl"] = $fila["AgregadoEl"];
    $data[$c]["Estatus"] = $fila["Estatus"];
    $data[$c]["CreadoPor"] = $fila["AgregadoPor"];
    
   
    $data[$c]["Acciones"] = ["<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStock?idProd=".base64_encode($fila["Id_PvUser"])." class='btn-edit  dropdown-item' >Asignar en sucursales <i class='fas fa-clinic-medical'></i></a>
       <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/DistribucionSucursales?Disid=".base64_encode($fila["Id_PvUser"])." class='btn-VerDistribucion  dropdown-item' >Consultar distribución <i class='fas fa-table'></i> </a>
       <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/EdicionDatosProducto?editprod=".base64_encode($fila["Id_PvUser"])." class='btn-editProd dropdown-item' >Editar datos <i class='fas fa-pencil-alt'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/HistorialProducto?idProd=".base64_encode($fila["Id_PvUser"])." class='btn-History dropdown-item' >Ver movimientos <i class='fas fa-history'></i></a>
    
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/MaximoYMinimo?Disid=".base64_encode($fila["Id_PvUser"])." class='btn-Delete dropdown-item' >Actualiza minimo y maximo <i class='fas fa-list-ol'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/CambiaProveedor?idProd=".base64_encode($fila["Id_PvUser"])." class='btn-Delete dropdown-item' >Cambio de proveedores <i class='fas fa-truck-loading'></i></a></div> "];
    

    $data[$c]["AccionesEnfermeria"] = ["<button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
   
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStockEnfermeria?idProd=".base64_encode($fila["Id_PvUser"])." class='btn-edit  dropdown-item' >Asignar a enfermeria <i class='fas fa-user-nurse'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/CrearCodEnfermeria?editprod=".base64_encode($fila["Id_PvUser"])." class='btn-edit  dropdown-item' >Editar datos  <i class='fas fa-edit'></i></a>
    } <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignaProcedimiento?editprod=".base64_encode($fila["Id_PvUser"])." class='btn-edit  dropdown-item' >Asignar procedimiento  <i class='fas fa-edit'></i></a>
     "];
    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>

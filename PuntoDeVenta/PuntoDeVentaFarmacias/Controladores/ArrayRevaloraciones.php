
<?php

header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";


$sql = "SELECT Agenda_revaloraciones.Id_agenda,Agenda_revaloraciones.Nombres_Apellidos,Agenda_revaloraciones.Telefono,
Agenda_revaloraciones.Fk_sucursal,Agenda_revaloraciones.Medico,Agenda_revaloraciones.Fecha,Agenda_revaloraciones.Turno,
Agenda_revaloraciones.Motivo_Consulta,Agenda_revaloraciones.Asistio,Agenda_revaloraciones.Agrego,
Agenda_revaloraciones.ActualizoEstado,Agenda_revaloraciones.AgregadoEl,Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal FROM Agenda_revaloraciones,Sucursales WHERE 
Sucursales.ID_Sucursal = Agenda_revaloraciones.Fk_sucursal";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
    $data[$c]["IdbD"] = $fila["Id_agenda"];
    $data[$c]["Cod_Barra"] = $fila["Nombres_Apellidos"];
    $data[$c]["Nombre_Prod"] = $fila["Telefono"];
    $data[$c]["Clave_interna"] = $fila["Nombre_Sucursal"];
    $data[$c]["Clave_Levic"] = $fila["Medico"];
  
    $data[$c]["Precio_C"] = $fila["Fecha"];
    $data[$c]["Precio_Venta"] = $fila["Turno"];
    $data[$c]["Nom_Serv"] = $fila["Motivo_Consulta"];
    $data[$c]["Marca"] = $fila["Asistio"];
    $data[$c]["Tipo"] = $fila["Agrego"];
    $data[$c]["Categoria"] = $fila["ActualizoEstado"];
    $data[$c]["Presentacion"] = $fila["AgregadoEl"];
   
    
   
    $data[$c]["Acciones"] = ["<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
    <a href=https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStock?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar en sucursales <i class='fas fa-clinic-medical'></i></a>
       <a href=https://controlfarmacia.com/AdminPOS/DistribucionSucursales?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-VerDistribucion  dropdown-item' >Consultar distribución <i class='fas fa-table'></i> </a>
       <a href=https://controlfarmacia.com/AdminPOS/EdicionDatosProducto?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-editProd dropdown-item' >Editar datos <i class='fas fa-pencil-alt'></i></a>
    <a href=https://controlfarmacia.com/AdminPOS/HistorialProducto?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-History dropdown-item' >Ver movimientos <i class='fas fa-history'></i></a>
    
    <a href=https://controlfarmacia.com/AdminPOS/MaximoYMinimo?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Actualiza minimo y maximo <i class='fas fa-list-ol'></i></a>
    <a href=https://controlfarmacia.com/AdminPOS/CambiaProveedor?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Cambio de proveedores <i class='fas fa-truck-loading'></i></a></div> "];
    

    $data[$c]["AccionesEnfermeria"] = ["<button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
   
    <a href=https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStockEnfermeria?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar a enfermeria <i class='fas fa-user-nurse'></i></a>
    <a href=https://controlfarmacia.com/AdminPOS/CrearCodEnfermeria?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Editar datos  <i class='fas fa-edit'></i></a>
    } <a href=https://controlfarmacia.com/AdminPOS/AsignaProcedimiento?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar procedimiento  <i class='fas fa-edit'></i></a>
     "];
    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>

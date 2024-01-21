
<?php
header('Content-Type: application/json');
include("db_connection.php");
include "Consultas.php";
include "Sesion.php";
include "mcript.php";

$sql = "SELECT Productos_POS.ID_Prod_POS,Productos_POS.Nombre_Prod,Productos_POS.Cod_Barra,Productos_POS.Cod_Enfermeria,Productos_POS.Proveedor1,Productos_POS.Proveedor2,
Productos_POS.ID_H_O_D,Productos_POS.Clave_adicional,Productos_POS.Clave_Levic,Productos_POS.FkMarca,Productos_POS.FkCategoria,Productos_POS.FkPresentacion,Productos_POS.Tipo,
Productos_POS.Precio_Venta,Productos_POS.Precio_C,Productos_POS.Stock,Productos_POS.Saldo,Productos_POS.AgregadoPor,Productos_POS.Vendido,Productos_POS.Tipo_Servicio,
Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,Productos_POS.AgregadoEl FROM Productos_POS,Servicios_POS where 
Servicios_POS.Servicio_ID = Productos_POS.Tipo_Servicio  AND Productos_POS.ID_H_O_D ='".$row['ID_H_O_D']."'";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
    $data[$c]["IdbD"] = $fila["ID_Prod_POS"];
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["Clave_interna"] = $fila["Clave_adicional"];
    $data[$c]["Clave_Levic"] = $fila["Clave_Levic"];
    $data[$c]["Cod_Enfermeria"] = $fila["Cod_Enfermeria"];
    $data[$c]["Precio_C"] = $fila["Precio_C"];
    $data[$c]["Precio_Venta"] = $fila["Precio_Venta"];
    $data[$c]["Nom_Serv"] = $fila["Nom_Serv"];
    $data[$c]["Marca"] = $fila["FkMarca"];
    $data[$c]["Tipo"] = $fila["Tipo"];
    $data[$c]["Categoria"] = $fila["FkCategoria"];
    $data[$c]["Presentacion"] = $fila["FkPresentacion"];
    $data[$c]["Proveedor1"] = $fila["Proveedor1"];
    $data[$c]["Proveedor2"] = $fila["Proveedor2"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    $data[$c]["Stock"] = $fila["Stock"];
    $data[$c]["Vendido"] = $fila["Vendido"];
    $data[$c]["Saldo"] = $fila["Saldo"];
   
    $data[$c]["Acciones"] = ["<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStock?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar en sucursales <i class='fas fa-clinic-medical'></i></a>
       <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/DistribucionSucursales?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-VerDistribucion  dropdown-item' >Consultar distribución <i class='fas fa-table'></i> </a>
       <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/EdicionDatosProducto?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-editProd dropdown-item' >Editar datos <i class='fas fa-pencil-alt'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/HistorialProducto?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-History dropdown-item' >Ver movimientos <i class='fas fa-history'></i></a>
    
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/MaximoYMinimo?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Actualiza minimo y maximo <i class='fas fa-list-ol'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/CambiaProveedor?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Cambio de proveedores <i class='fas fa-truck-loading'></i></a></div> "];
    

    $data[$c]["AccionesEnfermeria"] = ["<button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
   
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignacionSucursalesStockEnfermeria?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar a enfermeria <i class='fas fa-user-nurse'></i></a>
    <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/CrearCodEnfermeria?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Editar datos  <i class='fas fa-edit'></i></a>
    } <a href=https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/AsignaProcedimiento?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar procedimiento  <i class='fas fa-edit'></i></a>
     "];
    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>

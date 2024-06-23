
<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";


$sql = "SELECT Productos_POS.ID_Prod_POS,Productos_POS.Nombre_Prod,Productos_POS.Cod_Barra,Productos_POS.Proveedor1,
Productos_POS.Licencia,Productos_POS.Clave_adicional,Productos_POS.Clave_Levic,Productos_POS.FkMarca,Productos_POS.FkCategoria,Productos_POS.FkPresentacion,Productos_POS.Tipo,
Productos_POS.Precio_Venta,Productos_POS.Precio_C,Productos_POS.AgregadoPor,Productos_POS.Tipo_Servicio,
Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,Productos_POS.AgregadoEl FROM Productos_POS,Servicios_POS where 
Servicios_POS.Servicio_ID = Productos_POS.Tipo_Servicio AND Productos_POS.Licencia ='".$row['Licencia']."'";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
    $data[$c]["IdbD"] = $fila["ID_Prod_POS"];
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["Clave_interna"] = $fila["Clave_adicional"];
    $data[$c]["Clave_Levic"] = $fila["Clave_Levic"];
  
    $data[$c]["Precio_C"] = $fila["Precio_C"];
    $data[$c]["Precio_Venta"] = $fila["Precio_Venta"];
    $data[$c]["Nom_Serv"] = $fila["Nom_Serv"];
    $data[$c]["Marca"] = $fila["FkMarca"];
    $data[$c]["Tipo"] = $fila["Tipo"];
    $data[$c]["Categoria"] = $fila["FkCategoria"];
    $data[$c]["Presentacion"] = $fila["FkPresentacion"];
    $data[$c]["Proveedor1"] = $fila["Proveedor1"];
  
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    
   
    $data[$c]["Acciones"] = ["<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'>
    <a href=https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStock?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar en sucursales <i class='fas fa-clinic-medical'></i></a>
       <a href=https://controlfarmacia.com/AdminPOS/DistribucionSucursales?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-VerDistribucion  dropdown-item' >Consultar distribución <i class='fas fa-table'></i> </a>
       <a href=https://controlfarmacia.com/AdminPOS/EdicionDatosProducto?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-editProd dropdown-item' >Editar datos <i class='fas fa-pencil-alt'></i></a>
    <a href=https://controlfarmacia.com/AdminPOS/HistorialProducto?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-History dropdown-item' >Ver movimientos <i class='fas fa-history'></i></a>
    
    <a href=https://controlfarmacia.com/AdminPOS/MaximoYMinimo?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Actualiza minimo y maximo <i class='fas fa-list-ol'></i></a>
    <a href=https://controlfarmacia.com/AdminPOS/CambiaProveedor?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Cambio de proveedores <i class='fas fa-truck-loading'></i></a></div> "];
    

 
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>


<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

$sql = "SELECT Cajas_POS.ID_Caja, Cajas_POS.Cantidad_Fondo, Cajas_POS.Empleado, Cajas_POS.Turno, Cajas_POS.Sucursal, Cajas_POS.Estatus,
Cajas_POS.CodigoEstatus, Cajas_POS.Fecha_Apertura, Cajas_POS.Valor_Total_Caja, Cajas_POS.ID_H_O_D,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal
FROM Cajas_POS, Sucursales
WHERE Cajas_POS.Sucursal = Sucursales.ID_Sucursal
AND YEAR(Cajas_POS.Fecha_Apertura) = YEAR(NOW())
ORDER BY Cajas_POS.Fecha_Apertura DESC";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
    $data[$c]["IdCaja"] = $fila["ID_Caja"];
    $data[$c]["Empleado"] = $fila["Empleado"];
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["Fondodecaja"] = $fila["Cantidad_Fondo"];
    $data[$c]["Fecha"] = $fila["Fecha_Apertura"];
   
    $data[$c]["Estatus"]= '<button class="btn btn-default btn-sm" style="' . $fila["CodigoEstatus"] . '">' . $fila["Estatus"] . '</button>';
    $data[$c]["Cantidadalcierre"] = $fila["Valor_Total_Caja"];
   
   
    $data[$c]["Acciones"] = '<div class="btn-group">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fas fa-th-list fa-1x"></i>
    </button>
    <div class="dropdown-menu">
 
      <a data-id="' . $fila["ID_Caja"] . '" class="btn-Movimientos dropdown-item">Historial caja <i class="fas fa-history"></i></a>
      <a data-id="' . $fila["ID_Caja"] . '" class="btn-Ventas dropdown-item">Ventas realizadas en caja<i class="fas fa-receipt"></i></a>
      <a data-id="' . $fila["ID_Caja"] . '" style="' . ($fila['Estatus'] == 'Abierta' ? 'display:block;' : 'display:none;') . '" class="btn-Cortes dropdown-item">Realizar Corte<i class="fas fa-cash-register"></i></a>
    </div>
  </div>';
    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>



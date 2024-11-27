<?php
// Ajusta la zona horaria en PHP
date_default_timezone_set('America/Mexico_City');

// Otras variables
$fechaActual = date('Y-m-d');  // Obtiene la fecha actual en el formato 'YYYY-MM-DD'

// ...

// Consulta SQL con la variable de fecha declarada
$sql = "SELECT 
Cajas.ID_Caja,
Cajas.Cantidad_Fondo,
Cajas.Empleado,
Cajas.Sucursal,
Cajas.Estatus,
Cajas.CodigoEstatus,
Cajas.Turno,
Cajas.Asignacion,
Cajas.Fecha_Apertura,
Cajas.Valor_Total_Caja,
Cajas.Licencia,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal 
FROM 
Cajas, Sucursales 
WHERE 
Cajas.Sucursal = Sucursales.ID_Sucursal 
AND Cajas.Sucursal='".$row['Fk_Sucursal']."'
AND Cajas.Asignacion = 1
AND Cajas.Estatus='Abierta'
            AND Cajas.Empleado='".$row['Nombre_Apellidos']."'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$ValorCaja = "";
// Check if the query returned any results
if ($resultset && mysqli_num_rows($resultset) > 0) {
    $ValorCaja = mysqli_fetch_assoc($resultset);

    // Now you can access array elements safely
    // For example, $ValorCaja['ID_Caja'], $ValorCaja['Cantidad_Fondo'], etc.
} else {
    // Handle the case where no results were found
    
}


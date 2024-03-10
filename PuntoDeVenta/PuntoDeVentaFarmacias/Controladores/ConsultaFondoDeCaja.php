<?php
include_once("db_connect.php");
$sql ="SELECT Fondos_Cajas.ID_Fon_Caja,Fondos_Cajas.Fk_Sucursal,Fondos_Cajas.Fondo_Caja, 
Fondos_Cajas.Estatus, 
Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal FROM 
Fondos_Cajas,Sucursales where Fondos_Cajas.Fk_Sucursal = Sucursales.ID_Sucursal
 AND Fondos_Cajas.Fk_Sucursal='".$row['Fk_Sucursal']."' AND Fondos_Cajas.Estatus ='Activo' ";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$ValorFondoCaja = mysqli_fetch_assoc($resultset);

?>
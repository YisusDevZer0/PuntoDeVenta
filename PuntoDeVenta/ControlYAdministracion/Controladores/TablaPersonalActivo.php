<?php
// datatable_server.php
include 'ssp.class.php';
include 'Config.php';

$dbDetails = $config['db'];

$table = 'Usuarios_PV';
$primaryKey = 'Id_PvUser';

$columns = array(
    array('db' => 'Usuarios_PV.Id_PvUser', 'dt' => 0),
    array('db' => 'Tipos_Usuarios.TipoUsuario', 'dt' => 1),
    array('db' => 'Usuarios_PV.Licencia', 'dt' => 2),
    array('db' => 'Usuarios_PV.AgregadoEl', 'dt' => 3),
    array('db' => 'Usuarios_PV.AgregadoPor', 'dt' => 4),
    array('db' => '', 'dt' => 5)
);

// Modify the join conditions in the SSP::simple function
$joinQuery = "INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User
              INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal";

// Utilize the modified SSP::simple function to get the data
$result = SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns, $joinQuery);

// Convert the result to a format that DataTables can understand
echo json_encode($result);
?>

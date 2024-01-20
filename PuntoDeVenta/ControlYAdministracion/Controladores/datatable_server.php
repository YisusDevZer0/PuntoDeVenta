<?php
// datatable_server.php
include 'ssp.class.php';
include 'Config.php';

$dbDetails = $config['db'];

$table = 'Tipos_Usuarios';
$primaryKey = 'ID_User';

$columns = array(
    array('db' => 'ID_User', 'dt' => 0),
    array('db' => 'TipoUsuario', 'dt' => 1),
    array('db' => 'Licencia', 'dt' => 2),
    array('db' => 'Creadoel', 'dt' => 3),
    array('db' => 'Creado', 'dt' => 4),
    array('db' => '', 'dt' => 5)  // Esta columna está vacía porque no proviene de la base de datos, es para el botón 'Editar'
);

// Utiliza la función de SSP para obtener los datos
$result = SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns);

// Después de obtener los datos usando SSP
var_dump($result);
echo json_encode($result);

?>

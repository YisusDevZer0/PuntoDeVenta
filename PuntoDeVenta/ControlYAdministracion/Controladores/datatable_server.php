<?php
include 'ssp.class.php';

$dbDetails = array(
    'host' => 'localhost',
    'user' => 'u858848268_devpezer0',
    'pass' => 'F9+nIIOuCh8yI6wu4!08',
    'db'   => 'u858848268_doctorpez'
);

$table = 'Tipos_Usuarios';

$primaryKey = 'ID_User';

$columns = array(
    array('db' => 'ID_User', 'dt' => 0),
    array('db' => 'TipoUsuario', 'dt' => 1),
    array('db' => 'Licencia', 'dt' => 2),
    array('db' => 'Creadoel', 'dt' => 3),
    array('db' => 'Creado', 'dt' => 4)
);

echo json_encode(
    SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
);
?>

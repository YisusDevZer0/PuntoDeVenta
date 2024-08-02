<?php
// simple conexion a la base de datos
function connect(){
	return new mysqli("localhost","u858848268_devpezer0","F9+nIIOuCh8yI6wu4!08","u858848268_doctorpez");
}
$con = connect();
if (!$con->set_charset("utf8")) {//asignamos la codificación comprobando que no falle
       die("Error cargando el conjunto de caracteres utf8");
}
?>
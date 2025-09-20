<?php
// Debug de búsqueda
session_start();
include_once "Controladores/db_connect.php";
include_once "Controladores/ControladorUsuario.php";

echo "<h1>Debug de Búsqueda</h1>";

echo "<h2>Datos de Sesión:</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h2>Datos POST:</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

echo "<h2>Datos GET:</h2>";
echo "<pre>" . print_r($_GET, true) . "</pre>";

// Simular búsqueda
$_POST['query'] = 'test';

echo "<h2>Probando API:</h2>";
include 'api/buscar_productos_simple.php';
?>

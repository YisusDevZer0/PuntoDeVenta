<?php
// Test simple de la API
session_start();
include_once "Controladores/db_connect.php";
include_once "Controladores/ControladorUsuario.php";

echo "<h1>Test de API de Pedidos</h1>";

// Simular datos POST
$_POST['accion'] = 'buscar_producto';
$_POST['q'] = 'test';

echo "<h2>Datos de Sesión:</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h2>Datos POST:</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

echo "<h2>Probando API:</h2>";
include 'Controladores/PedidosController.php';
?>

<?php
// Script de prueba para verificar el controlador de pedidos
session_start();

// Simular una sesión de usuario para pruebas
$_SESSION['ControlMaestro'] = '1'; // ID de usuario de prueba

echo "<h2>Prueba del Controlador de Pedidos</h2>";

// Incluir el controlador
include_once "Controladores/PedidosController.php";

echo "<p>✅ Controlador cargado correctamente</p>";
echo "<p>Usuario ID: " . ($usuario_id ?? 'No definido') . "</p>";
echo "<p>Sucursal ID: " . ($sucursal_id ?? 'No definido') . "</p>";
echo "<p>Row data: " . print_r($row ?? [], true) . "</p>";
?> 
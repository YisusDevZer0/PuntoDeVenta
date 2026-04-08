<?php

require_once __DIR__ . '/config/app.php';
session_start();
include __DIR__ . '/Consultas/ValidadorUsuario.php';
if($_SESSION["ControlMaestro"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('ControlYAdministracion/'));

}

if($_SESSION["VentasPos"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('PuntoDeVentaFarmacias/'));

}

if($_SESSION["AdministradorGeneral"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('POSAdministracion/'));

}

if($_SESSION["Supervision"] || $_SESSION["ResponsableDeSupervision"])	// Supervisor (ValidadorUsuario usa ResponsableDeSupervision)
{
	

	header('Location: ' . fdp_url('SupervisionPOS/'));

}
if($_SESSION["Inventarios"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('Inventarios/'));

}
if($_SESSION["AdministradorRH"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('ControlYAdministracion/'));

}
if($_SESSION["ResponsableDelCedis"])	//Condicion admin
{
	

	header('Location: ' . fdp_url('CEDIS/'));

}

if($_SESSION["Marketing"])	//Condicion MKT
{
	

	header('Location: ' . fdp_url('ControlYAdministracion/'));

}

if (!empty($_SESSION['Enfermeria'])) {
	header('Location: ' . fdp_url('ControlDeEnfermeria/'));
	exit;
}

// Sin sesión de ningún rol: evitar respuesta vacía; el login canónico está en index.php
$fdpHasPosSession = !empty($_SESSION['ControlMaestro'])
	|| !empty($_SESSION['VentasPos'])
	|| !empty($_SESSION['AdministradorGeneral'])
	|| !empty($_SESSION['Supervision'])
	|| !empty($_SESSION['ResponsableDeSupervision'])
	|| !empty($_SESSION['Inventarios'])
	|| !empty($_SESSION['AdministradorRH'])
	|| !empty($_SESSION['ResponsableDelCedis'])
	|| !empty($_SESSION['Marketing']);
if (!$fdpHasPosSession) {
	header('Location: ' . fdp_url('index.php'));
	exit;
}

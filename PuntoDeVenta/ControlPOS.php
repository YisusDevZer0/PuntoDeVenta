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


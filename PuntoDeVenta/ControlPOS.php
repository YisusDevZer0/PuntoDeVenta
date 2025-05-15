<?php

session_start();
include ("Consultas/ValidadorUsuario.php");
if($_SESSION["ControlMaestro"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/");	

}

if($_SESSION["VentasPos"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/");	

}

if($_SESSION["AdministradorGeneral"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/POSAdministracion/");	

}

if($_SESSION["Supervision"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Supervision/");	

}
if($_SESSION["Inventarios"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Inventarios/");	

}
if($_SESSION["AdministradorRH"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/");	

}
if($_SESSION["ResponsableDelCedis"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/CEDIS/");	

}

if($_SESSION["Marketing"])	//Condicion MKT
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/");	

}


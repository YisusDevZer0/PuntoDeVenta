<?php

session_start();
include ("Consultas/ValidadorUsuario.php");
if($_SESSION["ControlMaestro"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/");	

}

if($_SESSION["Vendedor"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Ventas/");	

}

if($_SESSION["Administrador"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Administracion/");	

}

if($_SESSION["Supervision"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Supervision/");	

}
if($_SESSION["Inventarios"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/Inventarios/");	

}
if($_SESSION["RecursosHumanos"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/");	

}
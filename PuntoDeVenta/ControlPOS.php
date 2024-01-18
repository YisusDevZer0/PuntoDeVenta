<?php

session_start();
include ("Consultas/POS.php");
if($_SESSION["ControlMaestro"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYadministracion");	

}
if($_SESSION["VentasPos"])	//Condicion personal
{

	header("location: https://doctorpez.mx/PV"); 
}


<?php

session_start();
include ("Consultas/ValidadorUsuario.php");
if($_SESSION["ControlMaestro"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/PuntoDeVenta/ControlYadministracion/");	

}


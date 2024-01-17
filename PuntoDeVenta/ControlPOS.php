<?

session_start();
include ("Scripts/POS.php");
if($_SESSION["SuperAdmin"])	//Condicion admin
{
	

	header("location:https://doctorpez.mx/AdminPOS");	

}
if($_SESSION["VentasPos"])	//Condicion personal
{

	header("location: https://doctorpez.mx/POS2"); 
}

if($_SESSION["AdminPOS"])	//Condicion personal
{

	header("location: https://doctorpez.mx/AdministracionPOS"); 
}


if($_SESSION["LogisticaPOS"])	//Condicion personal
{

	header("location: https://doctorpez.mx/POSLogistica"); 
}

if($_SESSION["ResponsableCedis"])	//Condicion personal
{

	header("location: https://doctorpez.mx/CEDIS"); 
}

if($_SESSION["ResponsableInventarios"])	//Condicion personal
{

	header("location: https://doctorpez.mx/Inventarios"); 
}

if($_SESSION["ResponsableDeFarmacias"])	//Condicion personal
	{	header("location: https://doctorpez.mx/ResponsableDeFarmacias");
	}
	if($_SESSION["CoordinadorDental"])	//Condicion personal
	{	header("location: https://doctorpez.mx/JefeDental");
	}
	if($_SESSION["Supervisor"])	//Condicion personal
	{	header("location: https://doctorpez.mx/CEDISMOVIL");
	}
	if($_SESSION["JefeEnfermeros"])	//Condicion personal
	{	header("location: https://doctorpez.mx/JefaturaEnfermeria");
	}
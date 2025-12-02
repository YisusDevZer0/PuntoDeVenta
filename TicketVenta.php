<?php
header("Access-Control-Allow-Origin: *"); // Permite cualquier dominio, puedes restringirlo si es necesario
header("Access-Control-Allow-Methods: POST"); // Métodos permitidos (puedes ajustar esto según tus necesidades)
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *"); // O el dominio correcto
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Max-Age: 86400"); // Cache la respuesta durante 1 día para evitar preflights repetidos
    exit;
}

// Resto del código PHP para la solicitud POST



// Resto del código de tu archivo PHP
// ...



// Resto del código de tu archivo TicketVenta.php
date_default_timezone_set("America/Monterrey");
// ... tu código actual aquí ...
?>






<?php

date_default_timezone_set("America/Monterrey");
$fcha = date("Y-m-d");
function fechaCastellano ($fecha) {
    $fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
  $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
  }
  




  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['TicketVal']) && isset($_POST['BoletaTotal']) && isset($_POST['CambioCliente']) &&
        isset($_POST['ClienteInputValue']) && isset($_POST['FormaPagoSeleccionada']) && isset($_POST['ValoresTabla'])) {

        // Recibe los valores enviados desde JavaScript
        $NumberTicket = $_POST["TicketVal"];
        $BoletaTotal = $_POST["BoletaTotal"];
        $CambioCliente = $_POST["CambioCliente"];
        $ClienteInputValue = $_POST["ClienteInputValue"];
        $FormaPagoSeleccionada = $_POST["FormaPagoSeleccionada"];
        
        $FolioParticipacion = isset($_POST["TicketRifa"]) && !empty($_POST["TicketRifa"]) 
            ? $_POST["TicketRifa"] 
            : "N/A";
            
        $Vendedor = isset($_POST["Vendedor"]) ? $_POST["Vendedor"] : "Vendedor";
        $HoraImpresion = date("H:i:s");
        $ValoresTabla = json_decode($_POST["ValoresTabla"], true);

        // CONTINUAR CON LA IMPRESIÓN (no enviar respuesta aún)
    } else {
        // Envía una respuesta de error si faltan valores requeridos
        $response = array("status" => "error", "message" => "Faltan valores requeridos en la solicitud.");
        echo json_encode($response);
        exit;
    }
} else {
    // Envía una respuesta de error si la solicitud no es POST
    $response = array("status" => "error", "message" => "Solicitud no válida.");
    echo json_encode($response);
    exit;
}

require __DIR__ . '/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;


 
/*
	Vamos a simular algunos productos. Estos
	podemos recuperarlos desde $_POST o desde
	cualquier entrada de datos. Yo los declararé
	aquí mismo
*/



$nombre_impresora = "DevYisus";

$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);



/**
 * Arrange ASCII text into columns
 * 
 * @param string $leftCol
 *            Text in left column
 * @param string $rightCol
 *            Text in right column
 * @param number $leftWidth
 *            Width of left column
 * @param number $rightWidth
 *            Width of right column
 * @param number $space
 *            Gap between columns
 * @return string Text in columns
 */
function columnify($leftCol, $rightCol, $leftWidth, $rightWidth, $space = 4)
{
    $leftWrapped = wordwrap($leftCol, $leftWidth, "\n", true);
    $rightWrapped = wordwrap($rightCol, $rightWidth, "\n", true);

    $leftLines = explode("\n", $leftWrapped);
    $rightLines = explode("\n", $rightWrapped);
    $allLines = array();
    for ($i = 0; $i < max(count($leftLines), count($rightLines)); $i ++) {
        $leftPart = str_pad(isset($leftLines[$i]) ? $leftLines[$i] : "", $leftWidth, " ");
        $rightPart = str_pad(isset($rightLines[$i]) ? $rightLines[$i] : "", $rightWidth, " ");
        $allLines[] = $leftPart . str_repeat(" ", $space) . $rightPart;
    }
    return implode($allLines, "\n") . "\n";
}
$logo = EscposImage::load("logoticketsv3.jpg", false);
$logo2 = EscposImage::load("whats.png", false);
$logo3 = EscposImage::load("facebook.png", false);
$logo4 = EscposImage::load("www.png", false);
$qrtema = EscposImage::load("peznata2.jpg", false);
$printer->bitImage($logo);
$printer -> feed(1);
/* Information for the receipt */


$printer -> feed(1);

$printer->text("Calle 29 #202a entre 30 y 32 centro" . "\n");
 $printer->text("Teabo,Yucatan " . "\n");
 $printer->text("CP:97910" . "\n");

$printer->text("RFC:FMA2405241S7" . "\n");
 $printer->text("9999491470" . "\n");
 $printer->text("contabilidad@doctorpez.mx" . "\n");
#La fecha también
$printer->text("--------------------------------"); 
$printer -> feed(1);
$printer->text("NUMERO DE TICKET : $NumberTicket"); 
$printer -> feed(1);
$printer->text(fechaCastellano(date("Y-m-d") . "\n"));
$printer -> feed(1);
$printer->text($HoraImpresion . "\n");
$printer->text("------------------------------------------------"); 

/* Title of receipt */

$printer -> setEmphasis(true);
$printer -> text("CANTIDAD   PCIO U.  %DESCUENTO  IMPORTE\n");
$printer -> setEmphasis(false);

$printer -> setEmphasis(false);
foreach ($ValoresTabla as $valor) {
  $codigoBarras = $valor['codigoBarras'];
  $descripcionProducto = $valor['descripcionProducto'];
  $cantidadVendida = $valor['cantidadVendida'];
  $descuentorealizado = $valor['descuentorealizado'];
  $preciounitario= $valor['preciounitario'];
  $importeventa= $valor['importeventa'];
  // Imprime la información de cada producto
  $printer->text("$descripcionProducto\n");
  if ($descuentorealizado == 0) {
    $leftCol = "$cantidadVendida     0% $preciounitario     ";
} else {
    $leftCol = "$cantidadVendida      $descuentorealizado% $preciounitario    ";
}
  
  $rightCol = "$$importeventa";

  $printer->text(columnify($leftCol, $rightCol, 22, 32, 4));

  // Puedes continuar con otras acciones si es necesario
  // ...
}




$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> setEmphasis(true);

$printer -> setEmphasis(false);

$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
$printer -> text("    TOTAL:$$BoletaTotal");
$printer -> selectPrintMode();
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> feed(1);
$printer->text("<<<<<<<<FORMAS DE PAGO>>>>>>>>\n"); 
if($FormaPagoSeleccionada == "Efectivo"){
  $printer -> text("EFECTIVO   \n");
  $printer -> text("$BoletaTotal  \n");
  // $printer -> text("CREDITO VALE  TRANSF  \n");
  // $printer -> text("0.00  0.00  0.00 \n");
} 
// if($FormaPagoSeleccionada == "Cheque"){
//   $printer -> text("EFECTIVO  CHEQUE  TARJETA  \n");
//   $printer -> text("0.00 $BoletaTotal  0.00 \n");
//   $printer -> text("CREDITO VALE  TRANSF  \n");
//   $printer -> text("0.00  0.00  0.00 \n");
// } 
if($FormaPagoSeleccionada == "Tarjeta de Credito"){
  $printer -> text("TARJETA DE CRÉDITO \n");
  $printer -> text("$BoletaTotal\n");
  // $printer -> text("CREDITO VALE  TRANSF  \n");
  // $printer -> text("0.00  0.00  0.00 \n");
} 
if($FormaPagoSeleccionada == "Tarjeta de debito"){
  $printer -> text("TARJETA DE DEBITO \n");
  $printer -> text("$BoletaTotal\n");
  // $printer -> text("CREDITO VALE  TRANSF  \n");
  // $printer -> text("0.00  0.00  0.00 \n");
} 

if($FormaPagoSeleccionada == "Transferencia"){
  $printer -> text("Transferencia \n");
  $printer -> text("$BoletaTotal\n");
  // $printer -> text("CREDITO VALE  TRANSF  \n");
  // $printer -> text("0.00  0.00  0.00 \n");
} 

// if($FormaPagoSeleccionada == "Credito"){
//   $printer -> text("EFECTIVO  CHEQUE  TARJETA  \n");
//   $printer -> text(" 0.00       0.00     0.00\n");
//   $printer -> text("CREDITO VALE  TRANSF  \n");
//   $printer -> text(" $BoletaTotal 0.00  0.00  \n");
// } 
// if($FormaPagoSeleccionada == "Vale"){
//   $printer -> text("EFECTIVO  CHEQUE  TARJETA  \n");
//   $printer -> text(" 0.00       0.00     0.00\n");
//   $printer -> text("CREDITO VALE  TRANSF  \n");
//   $printer -> text(" 0.00 $BoletaTotal 0.00  \n");
// } 
// if($FormaPagoSeleccionada == "Transferencia"){
//   $printer -> text("EFECTIVO  CHEQUE  TARJETA  \n");
//   $printer -> text(" 0.00       0.00     0.00\n");
//   $printer -> text("CREDITO VALE  TRANSF  \n");
//   $printer -> text("  0.00  0.00  $BoletaTotal\n");
// } 

if($FormaPagoSeleccionada == "Crédito Enfermería"){
  $printer -> text("Credito de enfermería  \n");
  $printer -> text("$BoletaTotal   \n");
} 
if($FormaPagoSeleccionada == "Crédito Médico"){
  $printer -> text("Credito de médico  \n");
  $printer -> text("$BoletaTotal  \n ");
} 
if($FormaPagoSeleccionada == "Crédito Médico Especialista"){
  $printer -> text("Crédito de médico especialista  \n");
  $printer -> text("$BoletaTotal \n");
} 
$printer -> text("Cliente:  \n");
$printer -> text("$ClienteInputValue  \n");


$printer->text("--------------------------------"); 
$printer -> feed(1);
$printer -> text("CAMBIO: $CambioCliente \n");

$printer->text("Le atendio $Vendedor\n");
$printer -> feed(1);

if ($FolioParticipacion && $FolioParticipacion !== "N/A") {
    $printer->bitImageColumnFormat($qrtema); 
    $printer -> setEmphasis(true);
    $printer->text("Tu folio para participar:\n");
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer->text("$FolioParticipacion\n");
    $printer -> selectPrintMode();
    $printer -> setEmphasis(false);
    $printer -> feed(1);
}
// $printer->bitImageColumnFormat($logo3); 
// $printer->text("Saluda - Centro Médico Familiar \n");

// $printer->text("***Los precios ya incluyen \n impuestos.*** \n ***Para dudas y/o aclaraciones deberá presentar este ticket ***");

/*Alimentamos el papel 3 veces*/
$printer->feed(3);
$printer->feed(3);
/*
	Cortamos el papel. Si nuestra impresora
	no tiene soporte para ello, no generará
	ningún error
*/
$printer->cut();
 
/*
	Por medio de la impresora mandamos un pulso.
	Esto es útil cuando la tenemos conectada
	por ejemplo a un cajón
*/
$printer->pulse();
 
/*
	Para imprimir realmente, tenemos que "cerrar"
	la conexión con la impresora. Recuerda incluir esto al final de todos los archivos
*/
$printer->close();

/* A wrapper to do organise item names & prices into columns */

?>


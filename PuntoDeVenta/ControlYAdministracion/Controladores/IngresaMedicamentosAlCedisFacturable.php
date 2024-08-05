<?php
include "db_connect.php";
include "ControladorUsuario.php";



// Obtener los datos del formulario
$id_prod_pos = isset($_POST["ID_Prod_POS"]) ? intval($_POST["ID_Prod_POS"]) : 0;
$num_factura = isset($_POST["NumFactura"]) ? $_POST["NumFactura"] : '';
$codbarr = isset($_POST["Cod_Barra"]) ? $_POST["Cod_Barra"] : '';
$nombre_prod = isset($_POST["Nombre_Prod"]) ? $_POST["Nombre_Prod"] : '';
$cantidad_piezas = isset($_POST["Piezas"]) ? intval($_POST["Piezas"]) : 0;
$lote = isset($_POST["Lote"]) ? $_POST["Lote"] : '';
$fecha_caducidad = isset($_POST["Fecha_Caducidad"]) ? $_POST["Fecha_Caducidad"] : '';
$agregado_por = isset($_POST["AgregadoPor"]) ? $_POST["AgregadoPor"] : '';

// Consulta para insertar los datos en la tabla IngresosCedis
$sql = "INSERT INTO IngresosCedis (ID_Prod_POS, NumFactura, Cod_Barra, Nombre_Prod, Piezas, Fecha_Caducidad, Lote, AgregadoPor) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Los tipos de datos en bind_param son:
// i = integer, s = string
$stmt->bind_param("ssssssss", $id_prod_pos, $num_factura, $codbarr, $nombre_prod, $cantidad_piezas, $fecha_caducidad, $lote, $agregado_por);

if ($stmt->execute()) {
    echo "Producto insertado correctamente";
} else {
    die("Error al insertar el producto: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Obtener valores de la sesión
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$Id_PvUser = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : '';

// Obtener los datos del formulario
$id_prod_cedis = isset($_POST["ID_Prod_Cedis"]) ? intval($_POST["ID_Prod_Cedis"]) : 0;
$num_factura = isset($_POST["numeroFactura"]) ? $_POST["numeroFactura"] : '';
$cantidad_piezas = isset($_POST["cantidadPiezas"]) ? intval($_POST["cantidadPiezas"]) : 0;
$lote = isset($_POST["Lote"]) ? intval($_POST["Lote"]) : 0;
$fecha_caducidad = isset($_POST["FechaCaducidad"]) ? $_POST["FechaCaducidad"] : '';
$codbarr = isset($_POST["CodBarra"]) ? $_POST["CodBarra"] : '';

// Validar los datos (opcional)
// Puedes agregar validaciones adicionales aquí si es necesario

// Consulta para insertar los datos en la tabla IngresosCedis
$sql = "INSERT INTO IngresosCedis (ID_Prod_POS, NumFactura, Cod_Barra, Nombre_Prod, Piezas, Fecha_Caducidad, Lote, AgregadoPor) 
        VALUES (?, ?, ?, (SELECT Nombre_Prod FROM CEDIS WHERE IdProdCedis = ?), ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssiss", $id_prod_cedis, $num_factura, $codbarr, $id_prod_cedis, $cantidad_piezas, $fecha_caducidad, $lote, $Id_PvUser);

if ($stmt->execute()) {
    echo "Producto insertado correctamente";
} else {
    echo "Error al insertar el producto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

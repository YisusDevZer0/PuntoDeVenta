<?php
include_once 'db_connect.php';

// NOTA: El descuento automático de lotes está desactivado temporalmente
// require_once '../api/descontar_lotes_venta.php';

$contador = count($_POST["IdBasedatos"]);
$ProContador = 0;
$query = "INSERT INTO Ventas_POS ( `ID_Prod_POS`, `Identificador_tipo`, `Turno`,`Folio_Ticket`, `Clave_adicional`, `Cod_Barra`,`Nombre_Prod`,`Cantidad_Venta`, `Fk_sucursal`,`Total_Venta`,`Importe`,`Total_VentaG`,`DescuentoAplicado`,`FormaDePago`,`CantidadPago`,`Cambio`,`Cliente`,`Fecha_venta` , `Fk_Caja`,`Lote`,`Motivo_Cancelacion`,`Estatus`,`Sistema`,`AgregadoPor`, `ID_H_O_D`,`FolioSignoVital`,`TicketAnterior`,`Pagos_tarjeta`,`Tipo`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';
// $ventas_info = []; // Almacenar información para descuento de lotes (desactivado temporalmente)

for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["IdBasedatos"][$i]) || !empty($_POST["TiposDeServicio"][$i]) || !empty($_POST["TurnoEnTurno"])) {
        $ProContador++;
        $placeholders[] = "(?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $values[] = $_POST["IdBasedatos"][$i];
        $values[] = $_POST["TiposDeServicio"][$i];
        $values[] = $_POST["TurnoEnTurno"][$i];
        $values[] = $_POST["NumeroDeTickeT"][$i];
        $values[] = $_POST["ClaveAdicional"][$i];
        $values[] = $_POST["CodBarras"][$i];
        $values[] = $_POST["NombreDelProducto"][$i];
        $values[] = $_POST["CantidadVendida"][$i];
        $values[] = $_POST["SucursalEnVenta"][$i];
        $values[] = $_POST["PrecioVentaProd"][$i];
        $values[] = $_POST["ImporteGenerado"][$i];
        $values[] = $_POST["TotalDeVenta"][$i];
        $values[] = $_POST["DescuentoAplicado"][$i];
        $values[] = $_POST["FormaDePago"][$i];
        $values[] = $_POST["iptEfectivoOculto"][$i];
        $values[] = $_POST["CambioDelCliente"][$i];
        $values[] = $_POST["NombreDelCliente"][$i];
        $values[] = $_POST["FechaDeVenta"][$i];
        $values[] = $_POST["CajaDeSucursal"][$i];
        $values[] = $_POST["LoteDelProducto"][$i];
        $values[] = $_POST["Liquidado"][$i];
        $values[] = $_POST["Estatus"][$i];
        $values[] = $_POST["Sistema"][$i];
        $values[] = $_POST["AgregoElVendedor"][$i];
        $values[] = $_POST["Empresa"][$i];
        $values[] = $_POST["SignoVital"][$i];
        $values[] = $_POST["TicketAnterior"][$i];
        $values[] = $_POST["iptTarjetaCreditosOculto"][$i];
        $values[] = $_POST["Tipo"][$i];
        $valueTypes .= 'sssssssssssssssssssssssssssss';
        
    }
}

$response = array();

if ($ProContador != 0) {
    $query .= implode(', ', $placeholders);
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $valueTypes, ...$values);

        $resultadocon = mysqli_stmt_execute($stmt);

        if ($resultadocon) {
            // NOTA: El descuento automático de lotes está desactivado temporalmente
            // El stock se descuenta normalmente mediante el trigger RestarExistenciasDespuesInsert
            // que actualiza Stock_POS.Existencias_R al insertar en Ventas_POS
            
            $response['status'] = 'success';
            $response['message'] = 'Registro(s) agregado(s) correctamente.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error en la consulta de inserción: ' . mysqli_error($conn);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error en la preparación de la consulta: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se encontraron registros para agregar.';
}

echo json_encode($response);

mysqli_close($conn);
?>

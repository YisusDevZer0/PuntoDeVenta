<?php
include_once 'db_connect.php';

// Incluir función de descuento de lotes
require_once '../api/descontar_lotes_venta.php';

$contador = count($_POST["IdBasedatos"]);
$ProContador = 0;
$query = "INSERT INTO Ventas_POS ( `ID_Prod_POS`, `Identificador_tipo`, `Turno`,`Folio_Ticket`, `Clave_adicional`, `Cod_Barra`,`Nombre_Prod`,`Cantidad_Venta`, `Fk_sucursal`,`Total_Venta`,`Importe`,`Total_VentaG`,`DescuentoAplicado`,`FormaDePago`,`CantidadPago`,`Cambio`,`Cliente`,`Fecha_venta` , `Fk_Caja`,`Lote`,`Motivo_Cancelacion`,`Estatus`,`Sistema`,`AgregadoPor`, `ID_H_O_D`,`FolioSignoVital`,`TicketAnterior`,`Pagos_tarjeta`,`Tipo`) VALUES ";

$placeholders = [];
$values = [];
$valueTypes = '';
$ventas_info = []; // Almacenar información para descuento de lotes

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
        
        // Guardar información para descuento de lotes
        $ventas_info[] = [
            'id_prod_pos' => (int)$_POST["IdBasedatos"][$i],
            'cod_barra' => $_POST["CodBarras"][$i],
            'sucursal' => (int)$_POST["SucursalEnVenta"][$i],
            'cantidad' => (int)$_POST["CantidadVendida"][$i],
            'folio_ticket' => $_POST["NumeroDeTickeT"][$i],
            'usuario' => $_POST["AgregoElVendedor"][$i]
        ];
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
            // Descontar lotes automáticamente después de registrar las ventas
            $errores_lotes = [];
            $lotes_descontados = 0;
            
            foreach ($ventas_info as $venta) {
                // Verificar primero si el producto tiene lotes registrados
                $sql_check_lotes = "SELECT COUNT(*) as total FROM Historial_Lotes 
                                   WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Existencias > 0";
                $stmt_check = mysqli_prepare($conn, $sql_check_lotes);
                mysqli_stmt_bind_param($stmt_check, "ii", $venta['id_prod_pos'], $venta['sucursal']);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $check_row = mysqli_fetch_assoc($result_check);
                mysqli_stmt_close($stmt_check);
                
                // Solo intentar descontar si hay lotes registrados
                if ($check_row['total'] > 0) {
                    $resultado_lotes = descontarLotesVenta(
                        $venta['id_prod_pos'],
                        $venta['cod_barra'],
                        $venta['sucursal'],
                        $venta['cantidad'],
                        $venta['folio_ticket'],
                        $venta['usuario']
                    );
                    
                    if ($resultado_lotes['success']) {
                        $lotes_descontados++;
                    } else {
                        // No fallamos la venta si hay error en lotes, solo registramos el error
                        $errores_lotes[] = "Producto {$venta['cod_barra']}: {$resultado_lotes['error']}";
                    }
                }
                // Si no hay lotes, simplemente no se descuenta (comportamiento silencioso)
            }
            
            $response['status'] = 'success';
            $mensaje = 'Registro(s) agregado(s) correctamente.';
            
            if ($lotes_descontados > 0) {
                $mensaje .= " Lotes descontados: $lotes_descontados.";
            }
            
            if (count($errores_lotes) > 0) {
                $mensaje .= " Advertencias en lotes: " . implode('; ', $errores_lotes);
                $response['warnings'] = $errores_lotes;
            }
            
            $response['message'] = $mensaje;
            $response['lotes_descontados'] = $lotes_descontados;
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

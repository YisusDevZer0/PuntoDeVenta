<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;
$fk_sucursal = isset($_POST['fk_sucursal']) ? $_POST['fk_sucursal'] : null;
$id_h_o_d = isset($_POST['id_h_o_d']) ? $_POST['id_h_o_d'] : null;

// Verificar que las variables POST están definidas
if (!$fk_caja || !$fk_sucursal || !$id_h_o_d) {
    echo "Faltan parámetros necesarios.";
    exit;
}

// CONSULTA 1
$sql1 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID ASC LIMIT 1";
$query1 = $conn->query($sql1);
$Especialistas = null;
if ($query1 && $query1->num_rows > 0) {
    $Especialistas = $query1->fetch_object();
} else {
    echo "Error en la consulta 1: " . $conn->error;
}

// CONSULTA 2
$sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID DESC LIMIT 1";
$query2 = $conn->query($sql2);
$Especialistas2 = null;
if ($query2 && $query2->num_rows > 0) {
    $Especialistas2 = $query2->fetch_object();
} else {
    echo "Error en la consulta 2: " . $conn->error;
}

// CONSULTA 3
$sql3 = "SELECT Venta_POS_ID, Fk_Caja, Turno, Fecha_venta, Fk_sucursal, AgregadoPor, Turno, ID_H_O_D,
                COUNT(DISTINCT Folio_Ticket) AS Total_tickets, 
                COUNT(DISTINCT FolioSignoVital) AS Total_Folios, 
                SUM(Importe) AS VentaTotal  
         FROM Ventas_POS 
         WHERE Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' AND Fk_Caja = '$fk_caja' AND Fecha_venta = '$fcha'";
$query3 = $conn->query($sql3);
$Especialistas3 = null;
if ($query3 && $query3->num_rows > 0) {
    $Especialistas3 = $query3->fetch_object();
} else {
    echo "Error en la consulta 3: " . $conn->error;
}

// CONSULTA 14
$sql14 = "SELECT Ventas_POS.Identificador_tipo, Ventas_POS.Fk_sucursal, Ventas_POS.ID_H_O_D, Ventas_POS.Fecha_venta,
                Ventas_POS.AgregadoPor, Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl, Sucursales.ID_Sucursal, 
                Sucursales.Nombre_Sucursal, Servicios_POS.Servicio_ID, Servicios_POS.Nom_Serv, 
                SUM(Ventas_POS.Importe) AS totaldeservicios 
         FROM Ventas_POS
         INNER JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         INNER JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
         WHERE Fk_Caja = '$fk_caja' AND Ventas_POS.ID_H_O_D = '$id_h_o_d' AND Ventas_POS.Fecha_venta = '$fcha' 
         GROUP BY Servicios_POS.Servicio_ID";
$query14 = $conn->query($sql14);
$Especialistas14 = null;
if ($query14 && $query14->num_rows > 0) {
    $Especialistas14 = $query14->fetch_object();
} else {
    echo "Error en la consulta 14: " . $conn->error;
}

// Otras consultas similares

?>

<?php if ($Especialistas3 != null && $Especialistas14 != null): ?>
    <form action="javascript:void(0)" method="post" id="CortesDeCajaFormulario">
    <div class="text-center">
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Sucursal</label>
                <input type="text" class="form-control" id="cantidadtotalventasss" step="any" readonly value="<?php echo $Especialistas14->Nombre_Sucursal; ?>" aria-describedby="basic-addon1">
                <input type="text" hidden name="Fk_Caja" value="<?php echo $Especialistas14->Fk_Caja; ?>">
                <input type="text" hidden name="Sucursal" value="<?php echo $Especialistas14->Fk_sucursal; ?>">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Turno</label>
                <input type="text" class="form-control" id="cantidadtotalventasss" name="Turno" step="any" readonly value="<?php echo $Especialistas3->Turno; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Cajero</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="Cajero" step="any" readonly value="<?php echo $Especialistas3->AgregadoPor; ?>" aria-describedby="basic-addon1">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Total de venta</label>
                <input type="number" class="form-control" id="cantidadtotalventassss" name="VentaTotal" step="any" readonly value="<?php echo $Especialistas3->VentaTotal; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Total de tickets</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotal" step="any" readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="TotalesGeneralesCortes" class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre Servicio</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control" readonly value="<?php echo $Especialistas14->Nom_Serv; ?>"></td>
                    <td><input type="text" class="form-control" readonly value="<?php echo $Especialistas14->totaldeservicios; ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
    <?php if ($query14->num_rows > 0): ?>
        <div class="text-center">
        <div class="table-responsive">
            <table id="TotalesFormaPagoCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Forma de pago</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Efectivo"></td>
                        <td><input type="number" class="form-control" readonly value="<?php echo $Especialistas3->VentaTotal; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </form>
<?php else: ?>
    <p class="alert alert-warning">404 No se encuentra</p>
<?php endif; ?>
<?php else: ?>
    <p class="alert alert-warning">404 No se encuentra</p>
<?php endif; ?>
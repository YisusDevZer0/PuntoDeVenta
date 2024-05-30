<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = $_POST['id'];
$fk_sucursal = $_POST['fk_sucursal'];
$id_h_o_d = $_POST['id_h_o_d'];

// CONSULTA 1
$sql1 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID ASC LIMIT 1";
$query = $conn->query($sql1);
$Especialistas = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas = $r;
        break;
    }
}

// CONSULTA 2
$sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID DESC LIMIT 1";
$query = $conn->query($sql2);
$Especialistas2 = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas2 = $r;
        break;
    }
}

// CONSULTA 3
$sql3 = "SELECT Venta_POS_ID, Fk_Caja, Turno, Fecha_venta, Fk_sucursal, AgregadoPor, Turno, ID_H_O_D,
                COUNT(DISTINCT Folio_Ticket) AS Total_tickets, 
                COUNT(DISTINCT FolioSignoVital) AS Total_Folios, 
                SUM(Importe) AS VentaTotal  
         FROM Ventas_POS 
         WHERE Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql3);
$Especialistas3 = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas3 = $r;
        break;
    }
}

// CONSULTA 14
$sql14 = "SELECT Ventas_POS.Identificador_tipo, Ventas_POS.Fk_sucursal, Ventas_POS.ID_H_O_D, Ventas_POS.Fecha_venta,
                Ventas_POS.AgregadoPor, Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl, Sucursales.ID_Sucursal, 
                Sucursales.Nombre_Sucursal, Servicios_POS.Servicio_ID, Servicios_POS.Nom_Serv, 
                SUM(Ventas_POS.Importe) AS totaldeservicios 
         FROM Ventas_POS
         INNER JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         INNER JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
         WHERE Fk_Caja = '$fk_caja' AND Ventas_POS.ID_H_O_D = '$id_h_o_d' 
         GROUP BY Servicios_POS.Servicio_ID";
$query = $conn->query($sql14);
$Especialistas14 = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas14 = $r;
        break;
    }
}
?>

<?php if ($Especialistas3 != null && $Especialistas14 != null): ?>
    <div class="text-center">
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Sucursal</label>
                <input type="text" class="form-control" id="cantidadtotalventasss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas14->Nombre_Sucursal; ?>" aria-describedby="basic-addon1">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Turno</label>
                <input type="text" class="form-control" id="cantidadtotalventasss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Turno; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Cajero</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->AgregadoPor; ?>" aria-describedby="basic-addon1">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Total de venta</label>
                <input type="number" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->VentaTotal; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="exampleFormControlInput1">Total de tickets</label>
                <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1">
            </div>
            <div class="col">
                <label for="exampleFormControlInput1">Total de signos vitales</label>
                <input type="number" class="form-control" id="cantidadtotalventasssss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Total_Folios; ?>" aria-describedby="basic-addon1">
            </div>
        </div>
    </div>
<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

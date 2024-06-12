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
$query = $conn->query($sql1);
$Especialistas = null;
if ($query && $query->num_rows > 0) {
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
if ($query && $query->num_rows > 0) {
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
if ($query && $query->num_rows > 0) {
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
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas14 = $r;
        break;
    }
}



// Consulta 6: Total de venta por crédito en enfermería
$sql6 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCredito 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Enfermería' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql6);
$Especialistas6 = null;
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas6 = $r;
        break;
    }
}

// Consulta 7: Total de venta por crédito en limpieza
$sql7 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoLimpieza 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Limpieza' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql7);
$Especialistas7 = null;
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas7 = $r;
        break;
    }
}

// Consulta 11: Total de venta por crédito farmacéutico
$sql11 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoFarmaceutico 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Farmacéutico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql11);
$Especialistas11 = null;
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas11 = $r;
        break;
    }
}

// Consulta 12: Total de venta por crédito médico
$sql12 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoMedicos 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Médico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql12);
$Especialistas12 = null;
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas12 = $r;
        break;
    }
}

// Consulta 13: Cortes de cajas POS
$sql13 = "SELECT * FROM Cortes_Cajas_POS 
          WHERE Sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query = $conn->query($sql13);
$Especialistas13 = null;
if ($query && $query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas13 = $r;
        break;
    }
}




$sql5 = "SELECT Ventas_POS.Identificador_tipo, Ventas_POS.Fk_sucursal, Ventas_POS.ID_H_O_D, Ventas_POS.Fecha_venta,
                Ventas_POS.AgregadoPor, Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl, Sucursales.ID_Sucursal, 
                Sucursales.Nombre_Sucursal, Servicios_POS.Servicio_ID, Servicios_POS.Nom_Serv, 
                SUM(Ventas_POS.Importe) as totaldeservicios 
         FROM Ventas_POS, Servicios_POS, Sucursales 
         WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
         AND Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal AND Ventas_POS.ID_H_O_D ='".$id_h_o_d."' 
         GROUP BY Servicios_POS.Servicio_ID";
$query = $conn->query($sql5);

$sql = "
    SELECT 
        Ventas_POS.FormaDePago,
        SUM(CASE WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagoEfectivo,
        SUM(CASE WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagotarjeta,
        SUM(CASE WHEN Ventas_POS.FormaDePago != 'Efectivo' AND Ventas_POS.FormaDePago != 'Tarjeta' THEN Ventas_POS.Importe ELSE 0 END) as totalesdepagoCreditos
    FROM Ventas_POS
    JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
    JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
    WHERE 
        Ventas_POS.Fk_Caja = '".$_POST['id']."' 
        AND Ventas_POS.ID_H_O_D = '".$id_h_o_d."'
    GROUP BY Ventas_POS.FormaDePago
";
$query = $conn->query($sql);

// Inicializa las variables para los totales
$totalesdepagoEfectivo = 0;
$totalesdepagotarjeta = 0;
$totalesdepagoCreditos = 0;

// Procesa los resultados de la consulta
while ($row = $query->fetch_assoc()) {
    $totalesdepagoEfectivo += $row['totalesdepagoEfectivo'];
    $totalesdepagotarjeta += $row['totalesdepagotarjeta'];
    $totalesdepagoCreditos += $row['totalesdepagoCreditos'];
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
                        <td><input type="text" class="form-control" name="NombreServicio[]" readonly value="<?php echo $Especialistas14->Nom_Serv; ?>"></td>
                        <td><input type="text" class="form-control" name="TotalServicio[]" readonly value="<?php echo $Especialistas14->totaldeservicios; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($query->num_rows > 0): ?>
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
                    <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="Efectivo"></td>
                    <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $totalesdepagoEfectivo; ?>"></td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="Tarjeta"></td>
                    <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $totalesdepagotarjeta; ?>"></td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="Créditos"></td>
                    <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $totalesdepagoCreditos; ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
               
            </tbody>
        </table>
    </div>
</div>

    <button type="submit"  id="submit"  class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
                          
</form>

<?php else: ?>
    <p class="alert alert-danger">No se encontraron datos para mostrar.</p>
<?php endif; ?>
<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
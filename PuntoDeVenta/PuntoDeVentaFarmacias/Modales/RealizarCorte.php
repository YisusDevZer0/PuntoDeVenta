<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");

// Verificación de las variables POST
$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;
$fk_sucursal = isset($_POST['fk_sucursal']) ? $_POST['fk_sucursal'] : null;
$id_h_o_d = isset($_POST['id_h_o_d']) ? $_POST['id_h_o_d'] : null;

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
$Especialistas = ($query1 && $query1->num_rows > 0) ? $query1->fetch_object() : null;

// CONSULTA 2
$sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
         FROM Ventas_POS 
         WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
         ORDER BY Venta_POS_ID DESC LIMIT 1";
$query2 = $conn->query($sql2);
$Especialistas2 = ($query2 && $query2->num_rows > 0) ? $query2->fetch_object() : null;

// CONSULTA 3
$sql3 = "SELECT Venta_POS_ID, Fk_Caja, Turno, Fecha_venta, Fk_sucursal, AgregadoPor, Turno, ID_H_O_D,
                COUNT(DISTINCT Folio_Ticket) AS Total_tickets, 
                COUNT(DISTINCT FolioSignoVital) AS Total_Folios, 
                SUM(Importe) AS VentaTotal  
         FROM Ventas_POS 
         WHERE Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' AND Fk_Caja = '$fk_caja'";
$query3 = $conn->query($sql3);
$Especialistas3 = ($query3 && $query3->num_rows > 0) ? $query3->fetch_object() : null;

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
$query14 = $conn->query($sql14);

// Consulta 4: Total de dentales créditos
$sql4 = "SELECT Identificador_tipo, Fk_Caja, SUM(Importe) as totaldentalescreditos 
         FROM Ventas_POS 
         WHERE Identificador_tipo='Cr&eacute;ditos' 
         AND Fk_Caja = '$fk_caja'";
$query4 = $conn->query($sql4);
$Especialistas4 = ($query4 && $query4->num_rows > 0) ? $query4->fetch_object() : null;

// Consulta 6: Total de venta por crédito en enfermería
$sql6 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCredito 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Enfermería' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query6 = $conn->query($sql6);
$Especialistas6 = ($query6 && $query6->num_rows > 0) ? $query6->fetch_object() : null;

// Consulta 7: Total de venta por crédito en limpieza
$sql7 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoLimpieza 
         FROM Ventas_POS 
         WHERE FormaDePago = 'Crédito Limpieza' 
         AND Fk_sucursal = '$fk_sucursal' 
         AND ID_H_O_D = '$id_h_o_d' 
         AND Fk_Caja = '$fk_caja'";
$query7 = $conn->query($sql7);
$Especialistas7 = ($query7 && $query7->num_rows > 0) ? $query7->fetch_object() : null;

// Consulta 11: Total de venta por crédito farmacéutico
$sql11 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoFarmaceutico 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Farmacéutico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query11 = $conn->query($sql11);
$Especialistas11 = ($query11 && $query11->num_rows > 0) ? $query11->fetch_object() : null;

// Consulta 12: Total de venta por crédito médico
$sql12 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoMedicos 
          FROM Ventas_POS 
          WHERE FormaDePago = 'Crédito Médico' 
          AND Fk_sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query12 = $conn->query($sql12);
$Especialistas12 = ($query12 && $query12->num_rows > 0) ? $query12->fetch_object() : null;

// Consulta 13: Cortes de cajas POS
$sql13 = "SELECT * FROM Cortes_Cajas_POS 
          WHERE Sucursal = '$fk_sucursal' 
          AND ID_H_O_D = '$id_h_o_d' 
          AND Fk_Caja = '$fk_caja'";
$query13 = $conn->query($sql13);
$Especialistas13 = ($query13 && $query13->num_rows > 0) ? $query13->fetch_object() : null;

$sql5 = "SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja,
Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal,
Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal  AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
GROUP by Servicios_POS.Servicio_ID";
$query5 = $conn->query($sql5);

// Código para la forma de pago como efectivo
$sql8 = "SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoEfectivo
FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago='Efectivo' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query8 = $conn->query($sql8);

// Código para la forma de pago como tarjeta
$sql88 = "SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagotarjeta
FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago='Tarjeta'";
$query88 = $conn->query($sql88);

// Código para la forma de pago global de los Créditos 
$sql888 = "SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoCreditos
FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago!='Efectivo' AND Ventas_POS.FormaDePago!='Tarjeta' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query888 = $conn->query($sql888);
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
                <?php while ($Usuarios = $query14->fetch_array()): ?>
                    <tr>
                        <td><input type="text" class="form-control" name="NombreServicio[]" readonly value="<?php echo $Usuarios['Nom_Serv']; ?>"></td>
                        <td><input type="text" class="form-control" name="TotalServicio[]" readonly value="<?php echo $Usuarios['totaldeservicios']; ?>"></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($query8->num_rows > 0): ?>
        <div class="text-center">
            <div class="table-responsive">
                <table id="TotalesFormaPAgoCortes" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Forma de pago</th>
                            <th>Total</th>
                            <th>Forma de pago</th>
                            <th>Total</th>
                            <th>Forma de pago</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($Usuarios2 = $query8->fetch_array()): ?>
                            <?php while ($Usuarios3 = $query88->fetch_array()): ?>
                                <?php while ($Usuarios4 = $query888->fetch_array()): ?>
                                    <tr>
                                        <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="<?php echo $Usuarios2["FormaDePago"]; ?>"></td>
                                        <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $Usuarios2["totalesdepagoEfectivo"]; ?>"></td>
                                        <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="<?php echo $Usuarios3["FormaDePago"]; ?>"></td>
                                        <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $Usuarios3["totalesdepagotarjeta"]; ?>"></td>
                                        <td><input type="text" class="form-control" name="NombreFormaPago[]" readonly value="Creditos"></td>
                                        <td><input type="text" class="form-control" name="TotalFormasPagos[]" readonly value="<?php echo $Usuarios4["totalesdepagoCreditos"]; ?>"></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endwhile; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

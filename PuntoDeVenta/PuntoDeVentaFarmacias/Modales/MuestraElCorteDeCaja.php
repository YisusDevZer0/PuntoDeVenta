<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;

if (!$fk_caja) {
    echo "Faltan parámetros necesarios.";
    exit;
}

// CONSULTA 1: Obtener la información completa del corte, excepto los servicios
$sql = "SELECT ID_Caja, Fk_Caja, Empleado, Sucursal, Turno, TotalTickets, 
               Valor_Total_Caja, TotalEfectivo, TotalTarjeta, TotalCreditos, 
               TotalTransferencias, Hora_Cierre, Sistema, ID_H_O_D, Comentarios 
        FROM Cortes_Cajas_POS 
        WHERE Fk_Caja = '$fk_caja'";
$query = $conn->query($sql);

$datosCorte = null;

if ($query && $query->num_rows > 0) {
    $datosCorte = $query->fetch_object();
} else {
    echo '<p class="alert alert-danger">No se encontraron datos para mostrar.</p>';
    exit;
}

// CONSULTA 2: Obtener solo los servicios
$sqlServicios = "SELECT Servicios FROM Cortes_Cajas_POS WHERE Fk_Caja = '$fk_caja'";
$queryServicios = $conn->query($sqlServicios);

$servicios = [];

if ($queryServicios && $queryServicios->num_rows > 0) {
    $resultServicios = $queryServicios->fetch_object();
    
    // Mostrar el contenido del campo Servicios para verificar el formato
    echo "<pre>Contenido de Servicios antes de procesar: " . htmlspecialchars($resultServicios->Servicios) . "</pre>";

    // Procesar manualmente la cadena de servicios
    if (!empty($resultServicios->Servicios)) {
        // Dividir la cadena en partes separadas por comas
        $serviciosArray = explode(", ", $resultServicios->Servicios);

        // Recorrer cada parte y dividir en clave y valor
        foreach ($serviciosArray as $servicio) {
            $servicioPartes = explode(": ", $servicio);
            if (count($servicioPartes) === 2) {
                $servicios[] = [
                    'nombre' => $servicioPartes[0],
                    'total' => $servicioPartes[1]
                ];
            }
        }
    }
} else {
    echo '<p class="alert alert-danger">No se encontraron servicios para mostrar.</p>';
}
?>

<?php if ($datosCorte): ?>
    <div class="text-center">
        <h4>Mostrando el desglose del corte</h4>
        <p><strong>Sucursal:</strong> <?php echo $datosCorte->Sucursal; ?></p>
        <p><strong>Cajero:</strong> <?php echo $datosCorte->Empleado; ?></p>
        <p><strong>Total de ventas:</strong> $<?php echo $datosCorte->Valor_Total_Caja; ?></p>
        <p><strong>Total de tickets:</strong> <?php echo $datosCorte->TotalTickets; ?></p>

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
                        <td><input type="text" class="form-control" name="EfectivoTotal" readonly value="<?php echo $datosCorte->TotalEfectivo; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Tarjeta"></td>
                        <td><input type="text" class="form-control" name="TarjetaTotal" readonly value="<?php echo $datosCorte->TotalTarjeta; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Créditos"></td>
                        <td><input type="text" class="form-control" name="CreditosTotales" readonly value="<?php echo $datosCorte->TotalCreditos; ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="Transferencias"></td>
                        <td><input type="text" class="form-control" name="TransferenciasTotales" readonly value="<?php echo $datosCorte->TotalTransferencias; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Desglose de servicios -->
        <div class="table-responsive">
            <table id="ServiciosCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre del servicio</th>
                        <th>Total del servicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="<?php echo $servicio['nombre']; ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?php echo $servicio['total']; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No hay servicios disponibles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <label for="comentarios">Observaciones:</label>
    <textarea class="form-control" id="comentarios" readonly name="comentarios" rows="4" cols="50"><?php echo $datosCorte->Comentarios; ?></textarea>
    <br>
   
    <input type="hidden" name="Sistema" value="<?php echo $datosCorte->Sistema; ?>">
    <input type="hidden" name="ID_H_O_D" value="<?php echo $datosCorte->ID_H_O_D; ?>">
<!-- 
    <button class="btn btn-primary" type="submit">Realizar corte</button> -->

<?php else: ?>
    <p class="alert alert-danger">No se encontraron datos para mostrar.</p>
<?php endif; ?>

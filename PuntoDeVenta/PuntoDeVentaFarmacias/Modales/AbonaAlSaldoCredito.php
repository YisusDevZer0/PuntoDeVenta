<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";
include "../Controladores/ConsultaCaja.php";
include "../Controladores/SumadeFolioTicketsNuevo.php";

$fcha = date("Y-m-d");
$user_id = null;

$sql1 = "SELECT 
    Ventas_POS.Folio_Ticket,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Cod_Barra,
    Ventas_POS.FormaDePago,
    Ventas_POS.Fecha_venta,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.CantidadPago,
    Ventas_POS.Cambio,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    COALESCE(
        (SELECT Pagos_tarjeta 
         FROM Ventas_POS v 
         WHERE v.Folio_Ticket = Ventas_POS.Folio_Ticket 
         AND v.Pagos_tarjeta IS NOT NULL 
         LIMIT 1), 
        0
    ) AS Pagos_tarjeta,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
WHERE 
    Ventas_POS.Folio_Ticket = '".$_POST["id"]."'";

$query = $conn->query($sql1);
$Especialistas = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas = $r;
        break;
    }
}

$primeras_tres_letras = substr($Especialistas->Nombre_Sucursal, 0, 3);
$resultado_concatenado = $primeras_tres_letras . $totalmonto;
$resultado_en_mayusculas = strtoupper($resultado_concatenado);
?>

<!-- Formulario de reimpresión de ticket con simulación de cobro -->
<?php if ($Especialistas != null): ?>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Abono pendiente</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly name="AbonoPendiente" value="<?php echo $Especialistas->Pagos_tarjeta ?>">
            <input type="text" class="form-control" hidden readonly name="TicketAnterior" value="<?php echo $Especialistas->Folio_Ticket ?>">
            <input type="text" class="form-control " hidden name="Turno" readonly value="<?php echo $ValorCaja['Turno'] ?>">
            <input type="text" class="form-control " hidden name="FkCaja" readonly value="<?php echo $ValorCaja['ID_Caja'] ?>">
            <input type="text" class="form-control " hidden name="CobradoPor" readonly value="<?php echo $row['Nombre_Apellidos'] ?>">
            
            <input type="text" class="form-control " hidden name="TicketNuevo" value="<?php echo $resultado_en_mayusculas; ?>" readonly>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Forma de pago </label>
        <select class="form-control form-select form-select-sm" aria-label=".form-select-sm example" name="FormaPago"id="selTipoPago" required>
            <option value="0">Seleccione el Tipo de Pago</option>
            <option value="Efectivo" selected="true">Efectivo</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Transferencia">Transferencia</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Abonado</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="Abonado" placeholder="Ingrese el monto abonado">
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Nuevo saldo</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly id="NuevoSaldo" name="NuevoSaldo">
        </div>
    </div>
</div>

<!-- Botón para simular el cobro del abono -->
<div class="row">
    <div class="col text-center">
        <button type="button" class="btn btn-primary" id="cobrarAbono">Cobrar Abono</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Al cambiar el valor del campo "Abonado"
    $('input[name="Abonado"]').on('input', function() {
        var abonoPendiente = parseFloat($('input[name="AbonoPendiente"]').val()) || 0;
        var abonado = parseFloat($(this).val()) || 0;

        // Calcular el nuevo saldo
        var nuevoSaldo = abonoPendiente - abonado;

        // Actualizar el valor del campo "Nuevo saldo"
        $('#NuevoSaldo').val(nuevoSaldo.toFixed(2));
    });

    // Simulación de cobro de abono con AJAX
    $('#cobrarAbono').on('click', function() {
        var abonado = $('input[name="Abonado"]').val();
        var abonoPendiente = $('input[name="AbonoPendiente"]').val();
        var nuevoSaldo = $('#NuevoSaldo').val();
        var formaPago = $('#selTipoPago').val();
        var ticket = $('input[name="Ticket"]').val();
        var cobrador = '<?php echo $_SESSION["usuario_nombre"]; ?>';

        if (abonado && parseFloat(abonado) > 0) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/guardar_abono.php',
                type: 'POST',
                data: {
                    FkCaja: "<?php echo $Especialistas->Fk_Caja; ?>",
                    Turno: "<?php echo $ValorCaja['Turno']; ?>",
                    SaldoPrevio: abonoPendiente,
                    Abono: abonado,
                    NuevoSaldo: nuevoSaldo,
                    CobradoPor: cobrador,
                    FormaPago: formaPago,
                    NumTicket: ticket,
                    TicketNuevo: 'No',
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Abono Exitoso',
                        text: 'El abono de ' + abonado + ' ha sido registrado con éxito.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Recargar la página al hacer clic en OK
                        }
                    });
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al registrar el abono. Por favor, inténtalo de nuevo.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, ingrese un monto válido para el abono.',
                confirmButtonText: 'OK'
            });
        }
    });
});
</script>

<?php else: ?>
    <p class="alert alert-warning">No hay resultados</p>
<?php endif; ?>

<?php
include("../db_connection.php");

// Obtener el valor del ID del ticket
$id = $_POST["id"];

// Consulta para obtener los datos del ticket
$sql = "SELECT 
            id, 
            nombre_paciente, 
            medicamento, 
            cantidad, 
            precioventa, 
            fecha_encargo, 
            estado, 
            costo, 
            abono_parcial, 
            NumTicket, 
            Fk_Sucursal 
        FROM encargos 
        WHERE NumTicket = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo json_encode(["error" => "Error en la consulta: " . $stmt->error]);
    exit;
}

$ticketData = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ticketData = $row;
        break;
    }
}

$stmt->close();

// Verificar si se encontraron datos
if (!$ticketData) {
    echo '<p class="alert alert-warning">No hay resultados</p>';
    exit;
}

// Crear valores concatenados y procesados
$primeras_tres_letras = substr($ticketData['Fk_Sucursal'], 0, 3);
$totalMonto = $ticketData['precioventa'] * $ticketData['cantidad'];
$resultado_concatenado = $primeras_tres_letras . $totalMonto;
$resultado_en_mayusculas = strtoupper($resultado_concatenado);
?>

<!-- Formulario de reimpresión de ticket con simulación de cobro -->
<div class="row">
    <div class="col">
        <label for="abonoPendiente">Abono pendiente</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly name="AbonoPendiente" value="<?php echo $ticketData['abono_parcial']; ?>">
            <input type="text" class="form-control" hidden readonly name="TicketAnterior" value="<?php echo $ticketData['NumTicket']; ?>">
            <input type="text" class="form-control" hidden name="TicketNuevo" value="<?php echo $resultado_en_mayusculas; ?>" readonly>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="formaPago">Forma de pago</label>
        <select class="form-control form-select form-select-sm" aria-label="Tipo de pago" name="FormaPago" id="selTipoPago" required>
            <option value="0">Seleccione el Tipo de Pago</option>
            <option value="Efectivo" selected="true">Efectivo</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Transferencia">Transferencia</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="abonado">Abonado</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="Abonado" placeholder="Ingrese el monto abonado">
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="nuevoSaldo">Nuevo saldo</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly id="NuevoSaldo" name="NuevoSaldo">
        </div>
    </div>
</div>

<button type="button" class="btn btn-primary" id="cobrarAbono">Cobrar Abono</button>

<script>
$(document).ready(function() {
    $('input[name="Abonado"]').on('input', function() {
        var abonoPendiente = parseFloat($('input[name="AbonoPendiente"]').val()) || 0;
        var abonado = parseFloat($(this).val()) || 0;
        var nuevoSaldo = abonoPendiente - abonado;
        $('#NuevoSaldo').val(nuevoSaldo.toFixed(2));
    });

    $('#cobrarAbono').on('click', function() {
        var abonado = $('input[name="Abonado"]').val();
        var abonoPendiente = $('input[name="AbonoPendiente"]').val();
        var nuevoSaldo = $('#NuevoSaldo').val();
        var formaPago = $('#selTipoPago').val();
        var ticket = $('input[name="TicketAnterior"]').val();

        if (abonado && parseFloat(abonado) > 0) {
            $.ajax({
                url: 'guardar_abono.php',
                type: 'POST',
                data: {
                    Abono: abonado,
                    SaldoPrevio: abonoPendiente,
                    NuevoSaldo: nuevoSaldo,
                    FormaPago: formaPago,
                    NumTicket: ticket
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Abono Exitoso',
                        text: 'El abono ha sido registrado con éxito.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al registrar el abono.',
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

<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();

// Generar número de ticket automáticamente
$fecha_actual = date('Y-m-d');
$sql_ticket = "SELECT MAX(CAST(SUBSTRING(NumTicket, 9) AS UNSIGNED)) as ultimo_numero 
               FROM encargos 
               WHERE NumTicket LIKE 'ENC-" . date('Ymd') . "-%'";
$result_ticket = $conn->query($sql_ticket);
$row_ticket = $result_ticket->fetch_assoc();
$siguiente_numero = ($row_ticket['ultimo_numero'] ?? 0) + 1;
$NumTicket = 'ENC-' . date('Ymd') . '-' . str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT);
?>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="RegistrarEncargoForm" class="mb-3">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                    <input type="text" name="nombre_paciente" id="nombre_paciente" class="form-control" placeholder="Escriba el nombre del paciente" required>
                </div>

                <div class="mb-3">
                    <label for="medicamento" class="form-label">Medicamento:</label>
                    <input type="text" name="medicamento" id="medicamento" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="precioventa" class="form-label">Precio de Venta:</label>
                    <input type="number" step="0.01" name="precioventa" id="precioventa" class="form-control" required>
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="fecha_encargo" class="form-label">Fecha de Encargo:</label>
                    <input type="date" name="fecha_encargo" id="fecha_encargo" class="form-control" value="<?php echo $fecha_actual; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="costo" class="form-label">Costo:</label>
                    <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="abono_parcial" class="form-label">Abono realizado:</label>
                    <input type="number" step="0.01" name="abono_parcial" id="abono_parcial" class="form-control" value="0.00" required>
                </div>

                <div class="mb-3">
                    <label for="NumTicket" class="form-label">Número de Ticket:</label>
                    <input type="text" name="NumTicket" id="NumTicket" class="form-control" value="<?php echo $NumTicket; ?>" readonly required>
                </div>
            </div>
        </div>

        <!-- Manten el input oculto con el ID_Caja -->
        <input type="hidden" name="Fk_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
        <input type="hidden" name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']?>">
        <input type="hidden" name="AgregadoPor" id="AgregadoPor" value="<?php echo $row['Nombre_Apellidos']?>">
        <input type="hidden" name="Fk_sucursal" id="sucursal" value="<?php echo $row['Fk_Sucursal']?>">
        <input type="hidden" name="Sistema" id="sistema" value="Administrador">
        <input type="hidden" name="Licencia" id="licencia" value="<?php echo $row['Licencia']?>">
        <input type="hidden" name="estado" id="estado" value="Pendiente">

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Registrar Encargo</button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        // Manejar el envío del formulario
        $('#RegistrarEncargoForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            $.ajax({
                url: '../Controladores/RegistrarEncargoController.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Encargo registrado exitosamente');
                        $('#editModal').modal('hide');
                        // Recargar la página o actualizar la lista
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error al procesar la solicitud');
                }
            });
        });
    });
    </script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra la caja especificada</p>
<?php endif; ?>

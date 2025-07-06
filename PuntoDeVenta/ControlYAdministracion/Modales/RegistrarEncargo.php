<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Verificar que se recibió el ID de la caja
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    echo '<p class="alert alert-danger">Error: No se especificó el ID de la caja</p>';
    exit;
}

$user_id = null;
$caja_id = intval($_POST["id"]);

// Consulta corregida usando los nombres correctos de las columnas
$sql1 = "SELECT c.ID_Caja, c.Sucursal, s.Nombre_Sucursal 
         FROM Cajas c 
         INNER JOIN Sucursales s ON c.Sucursal = s.ID_Sucursal 
         WHERE c.ID_Caja = ?";

$stmt = $conn->prepare($sql1);
if (!$stmt) {
    echo '<p class="alert alert-danger">Error en la preparación de la consulta: ' . $conn->error . '</p>';
    exit;
}

$stmt->bind_param("i", $caja_id);
$stmt->execute();
$result = $stmt->get_result();
$Especialistas = $result->fetch_object();
$stmt->close();

// Verificar que se encontró la caja y obtener datos de la sucursal
if ($Especialistas && !empty($Especialistas->Nombre_Sucursal)) {
    // Obtener las 3 primeras letras de la sucursal
    $primeras_tres_letras = substr($Especialistas->Nombre_Sucursal, 0, 3);
    $primeras_tres_letras = strtoupper($primeras_tres_letras);

    // Generar número de ticket automáticamente con formato mejorado
    $fecha_actual = date('Y-m-d');
    
    // Consulta mejorada para el ticket - obtener el último número secuencial
    $sql_ticket = "SELECT MAX(CAST(SUBSTRING(NumTicket, ?) AS UNSIGNED)) as ultimo_numero 
                   FROM encargos 
                   WHERE NumTicket LIKE ? 
                   AND Fk_Sucursal = ?";
    
    $stmt_ticket = $conn->prepare($sql_ticket);
    if ($stmt_ticket) {
        $posicion = strlen($primeras_tres_letras) + 4; // Posición después de "TEAENC-"
        $patron = $primeras_tres_letras . 'ENC-%';
        $stmt_ticket->bind_param("isi", $posicion, $patron, $Especialistas->Sucursal);
        $stmt_ticket->execute();
        $result_ticket = $stmt_ticket->get_result();
        $row_ticket = $result_ticket->fetch_assoc();
        $siguiente_numero = ($row_ticket['ultimo_numero'] ?? 0) + 1;
        $stmt_ticket->close();
    } else {
        // Si hay error en la consulta del ticket, usar número 1
        $siguiente_numero = 1;
    }
    
    // Formato correcto: TEAENC-0001 (4 dígitos con ceros a la izquierda)
    $NumTicket = $primeras_tres_letras . 'ENC-' . str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT);
} else {
    // Si no se encuentra la caja, mostrar error
    echo '<p class="alert alert-danger">Error: No se encontró la caja especificada o la sucursal no tiene nombre válido</p>';
    exit;
}
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

                <div class="mb-3" style="display: none;">
                    <label for="NumTicket" class="form-label">Número de Ticket:</label>
                    <input type="text" name="NumTicket" id="NumTicket" class="form-control" value="<?php echo $NumTicket; ?>" readonly required>
                </div>
                <!-- Campo oculto para mantener el valor del ticket -->
                <input type="hidden" name="NumTicket" value="<?php echo $NumTicket; ?>">

                <!-- Select de forma de pago -->
                <div class="mb-3">
                    <label for="forma_pago" class="form-label">Forma de pago:</label>
                    <select class="form-control form-select form-select-sm" aria-label=".form-select-sm example" id="selTipoPagoEncargo" required>
                        <option value="0">Seleccione el Tipo de Pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Credito">Credito</option>
                        <option value="Efectivo y Tarjeta">Efectivo y tarjeta</option>
                        <option value="Efectivo Y Credito">Efectivo y credito</option>
                        <option value="Efectivo Y Transferencia">Efectivo y transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                    <input type="hidden" name="FormaDePago" id="FormaDePagoEncargo" value="">
                </div>
            </div>
        </div>

        <!-- Manten el input oculto con el ID_Caja -->
        <input type="hidden" name="Fk_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
        <input type="hidden" name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']; ?>">
        <input type="hidden" name="Fk_Sucursal" id="sucursal" value="<?php echo $Especialistas->Sucursal; ?>">
        <input type="hidden" name="estado" id="estado" value="Pendiente">

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Registrar Encargo</button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        // Actualizar el input oculto con el valor del select
        $('#selTipoPagoEncargo').on('change', function() {
            $('#FormaDePagoEncargo').val($(this).val());
        });
        // Inicializar el valor por defecto
        $('#FormaDePagoEncargo').val($('#selTipoPagoEncargo').val());

        // Manejar el envío del formulario
        $('#RegistrarEncargoForm').on('submit', function(e) {
            e.preventDefault();
            
            // Mostrar indicador de carga con SweetAlert
            Swal.fire({
                title: 'Guardando encargo...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            var formData = $(this).serialize();
            
            // Debug: mostrar datos que se envían
            console.log('Datos a enviar:', formData);
            
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/RegistrarEncargoController.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 10000, // 10 segundos de timeout
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Encargo registrado exitosamente!',
                            text: 'Ticket: ' + response.NumTicket,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            $('#editModal').modal('hide');
                            // Recargar la página o actualizar la lista
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al registrar encargo',
                            text: response.message,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error AJAX:', {xhr: xhr, status: status, error: error});
                    
                    var errorMessage = 'Error al procesar la solicitud';
                    
                    if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            // Si no es JSON válido, mostrar el texto de respuesta
                            errorMessage = 'Error del servidor: ' + xhr.responseText.substring(0, 200);
                        }
                    } else if (status === 'timeout') {
                        errorMessage = 'Tiempo de espera agotado. Intente nuevamente.';
                    } else if (status === 'error') {
                        errorMessage = 'Error de conexión: ' + error;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        });
    });
    </script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra la caja especificada</p>
<?php endif; ?>

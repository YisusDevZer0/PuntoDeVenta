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
    
    // Consulta simplificada para obtener el último número de ticket de esta sucursal
    $sql_ticket = "SELECT NumTicket 
                   FROM PagosServicios 
                   WHERE Fk_Sucursal = ? 
                   AND NumTicket LIKE ? 
                   ORDER BY CAST(SUBSTRING(NumTicket, ?) AS UNSIGNED) DESC 
                   LIMIT 1";
    
    $stmt_ticket = $conn->prepare($sql_ticket);
    if ($stmt_ticket) {
        $patron = $primeras_tres_letras . 'SER-%';
        $posicion = strlen($primeras_tres_letras) + 4; // Posición después de "TEASER-"
        $stmt_ticket->bind_param("isi", $Especialistas->Sucursal, $patron, $posicion);
        $stmt_ticket->execute();
        $result_ticket = $stmt_ticket->get_result();
        $row_ticket = $result_ticket->fetch_assoc();
        
        if ($row_ticket) {
            // Extraer el número del último ticket
            $ultimo_ticket = $row_ticket['NumTicket'];
            // Buscar la posición del guión y extraer todo lo que viene después
            $pos_guion = strpos($ultimo_ticket, '-');
            if ($pos_guion !== false) {
                $numero_actual = (int)substr($ultimo_ticket, $pos_guion + 1);
            } else {
                $numero_actual = 0;
            }
            $siguiente_numero = $numero_actual + 1;
        } else {
            // Si no hay tickets previos, empezar con 1
            $siguiente_numero = 1;
        }
        $stmt_ticket->close();
    } else {
        // Si hay error en la consulta del ticket, usar número 1
        $siguiente_numero = 1;
    }
    
    // Formato correcto: TEASER-0001 (4 dígitos con ceros a la izquierda)
    $NumTicket = $primeras_tres_letras . 'SER-' . str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT);
    
    // Cargar lista de servicios desde ListadoServicios
    $sql_servicios = "SELECT `Servicio_ID`, `Servicio`, `Estado`, `Agregado_Por`, `Agregadoel`, `Sistema`, `Licencia` 
                      FROM `ListadoServicios` 
                      WHERE 1 
                      ORDER BY `Servicio` ASC";
    $result_servicios = $conn->query($sql_servicios);
    $servicios = [];
    if ($result_servicios && $result_servicios->num_rows > 0) {
        while ($row_servicio = $result_servicios->fetch_assoc()) {
            $servicios[] = $row_servicio;
        }
    }
} else {
    // Si no se encuentra la caja, mostrar error
    echo '<p class="alert alert-danger">Error: No se encontró la caja especificada o la sucursal no tiene nombre válido</p>';
    exit;
}
?>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="RegistrarPagoDeServicioForm" class="mb-3">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente:</label>
                    <input type="text" name="cliente" id="cliente" class="form-control" placeholder="Escriba el nombre del cliente" required>
                </div>

                <div class="mb-3">
                    <label for="servicio_id" class="form-label">Servicio:</label>
                    <select class="form-control form-select form-select-sm" name="servicio_id" id="servicio_id" required>
                        <option value="">Seleccione un servicio</option>
                        <?php if (!empty($servicios)) : ?>
                            <?php foreach ($servicios as $servicio) : ?>
                                <option value="<?php echo htmlspecialchars($servicio['Servicio_ID']); ?>">
                                    <?php echo htmlspecialchars($servicio['Servicio']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value="" disabled>No hay servicios disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="monto" class="form-label">Costo:</label>
                    <input type="number" step="0.01" name="monto" id="monto" class="form-control" placeholder="0.00" required>
                    <small class="form-text text-muted" id="costo-variable-msg" style="display: none;">Costo variable - Puede modificar el valor</small>
                </div>

                <div class="mb-3">
                    <label for="comision" class="form-label">Comisión:</label>
                    <input type="number" step="0.01" name="comision" id="comision" class="form-control" placeholder="0.00" readonly>
                    <small class="form-text text-muted">Comisión del servicio (solo lectura)</small>
                </div>

                <div class="mb-3">
                    <label for="fecha_pago" class="form-label">Fecha de Pago:</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="<?php echo $fecha_actual; ?>" required>
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones:</label>
                    <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales (opcional)"></textarea>
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
                    <select class="form-control form-select form-select-sm" aria-label=".form-select-sm example" id="selTipoPagoServicio" required>
                        <option value="0">Seleccione el Tipo de Pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Credito">Credito</option>
                        <option value="Efectivo y Tarjeta">Efectivo y tarjeta</option>
                        <option value="Efectivo Y Credito">Efectivo y credito</option>
                        <option value="Efectivo Y Transferencia">Efectivo y transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                    <input type="hidden" name="FormaDePago" id="FormaDePagoServicio" value="">
                </div>
            </div>
        </div>

        <!-- Manten el input oculto con el ID_Caja -->
        <input type="hidden" name="Fk_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
        <input type="hidden" name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']; ?>">
        <input type="hidden" name="Fk_Sucursal" id="sucursal" value="<?php echo $Especialistas->Sucursal; ?>">

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Registrar Pago de Servicio</button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        // Actualizar el input oculto con el valor del select
        $('#selTipoPagoServicio').on('change', function() {
            $('#FormaDePagoServicio').val($(this).val());
        });
        // Inicializar el valor por defecto
        $('#FormaDePagoServicio').val($('#selTipoPagoServicio').val());

        // Cargar datos del servicio cuando se seleccione
        $('#servicio_id').on('change', function() {
            var servicioId = $(this).val();
            
            if (!servicioId) {
                // Limpiar campos si no hay servicio seleccionado
                $('#monto').val('');
                $('#comision').val('');
                $('#costo-variable-msg').hide();
                return;
            }
            
            // Hacer petición AJAX para obtener los datos del servicio
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ObtenerDatosServicio.php',
                type: 'POST',
                data: { servicio_id: servicioId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.servicio) {
                        var servicio = response.servicio;
                        var costo = parseFloat(servicio.Costo) || 0;
                        var comision = parseFloat(servicio.Comision) || 0;
                        var esVariable = servicio.CostoVariable === 'S' || servicio.CostoVariable === 's';
                        
                        // Establecer el costo (si es variable, permitir edición, si no, mostrar el valor predeterminado)
                        if (costo > 0) {
                            $('#monto').val(costo.toFixed(2));
                        } else {
                            $('#monto').val('');
                        }
                        
                        // Si el costo es variable, permitir edición y mostrar mensaje
                        if (esVariable) {
                            $('#monto').prop('readonly', false);
                            $('#costo-variable-msg').show();
                        } else {
                            // Si no es variable pero el costo es 0, permitir edición
                            if (costo <= 0) {
                                $('#monto').prop('readonly', false);
                                $('#costo-variable-msg').hide();
                            } else {
                                // Si tiene costo fijo, permitir edición también (por si necesita ajustar)
                                $('#monto').prop('readonly', false);
                                $('#costo-variable-msg').hide();
                            }
                        }
                        
                        // Establecer la comisión (solo lectura)
                        $('#comision').val(comision.toFixed(2));
                    } else {
                        // Si no se encuentra el servicio, limpiar campos
                        $('#monto').val('');
                        $('#comision').val('');
                        $('#costo-variable-msg').hide();
                        console.error('Error al obtener datos del servicio:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición AJAX:', error);
                    // En caso de error, permitir que el usuario ingrese el costo manualmente
                    $('#monto').prop('readonly', false);
                    $('#comision').val('');
                    $('#costo-variable-msg').hide();
                }
            });
        });

        // Manejar el envío del formulario
        $('#RegistrarPagoDeServicioForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validar que se haya seleccionado un servicio
            if (!$('#servicio_id').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Servicio requerido',
                    text: 'Por favor seleccione un servicio',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
            
            // Validar que se haya seleccionado una forma de pago válida
            if ($('#selTipoPagoServicio').val() === '0' || !$('#selTipoPagoServicio').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Forma de pago requerida',
                    text: 'Por favor seleccione una forma de pago',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
            
            // Mostrar indicador de carga con SweetAlert
            Swal.fire({
                title: 'Guardando pago de servicio...',
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
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/RegistrarPagoDeServicioController.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 10000, // 10 segundos de timeout
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    
                    if (response.success) {
                        // Obtener los datos del formulario para enviar al ticket
                        var NumTicket = $('#NumTicket').val();
                        var cliente = $('#cliente').val();
                        var servicioNombre = $('#servicio_id option:selected').text();
                        var monto = $('#monto').val();
                        var formaDePago = $('#FormaDePagoServicio').val();
                        var empleado = $('#empleado').val();
                        
                        // Preparar datos para el ticket (similar a como se hace en ventas)
                        var ticketData = {
                            TicketVal: NumTicket,
                            ClienteInputValue: cliente,
                            ServicioNombre: servicioNombre,
                            BoletaTotal: monto,
                            FormaPagoSeleccionada: formaDePago,
                            Vendedor: empleado,
                            TipoTicket: 'PagoServicio' // Identificador para diferenciar el tipo de ticket
                        };
                        
                        // Codificar los datos
                        var encodedTicketVal = encodeURIComponent(NumTicket);
                        var encodedClienteInputValue = encodeURIComponent(cliente);
                        var encodedServicioNombre = encodeURIComponent(servicioNombre);
                        var encodedBoletaTotal = encodeURIComponent(monto);
                        var encodedFormaPagoSeleccionada = encodeURIComponent(formaDePago);
                        var encodedVendedor = encodeURIComponent(empleado);
                        var encodedTipoTicket = encodeURIComponent('PagoServicio');
                        
                        var ticketDataString = 'TicketVal=' + encodedTicketVal +
                                             '&ClienteInputValue=' + encodedClienteInputValue +
                                             '&ServicioNombre=' + encodedServicioNombre +
                                             '&BoletaTotal=' + encodedBoletaTotal +
                                             '&FormaPagoSeleccionada=' + encodedFormaPagoSeleccionada +
                                             '&Vendedor=' + encodedVendedor +
                                             '&TipoTicket=' + encodedTipoTicket;
                        
                        // Mostrar mensaje de éxito y luego enviar ticket
                        Swal.fire({
                            icon: 'success',
                            title: '¡Pago de servicio registrado exitosamente!',
                            text: 'Ticket: ' + response.NumTicket,
                            showConfirmButton: false,
                            timer: 2000,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        }).then(() => {
                            // Enviar datos al ticket por AJAX
                            Swal.fire({
                                icon: 'info',
                                title: 'Generando ticket...',
                                text: 'Por favor espere',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Enviar ticket por AJAX (el archivo del ticket aún no está construido, pero el envío funciona)
                            $.ajax({
                                type: 'POST',
                                url: 'http://localhost/ticket/TicketPagoServicio.php',
                                data: ticketDataString,
                                success: function(ticketResponse) {
                                    // Verificar si la respuesta contiene errores de PHP
                                    if (typeof ticketResponse === 'string' && (
                                        ticketResponse.includes('Fatal error') || 
                                        ticketResponse.includes('Warning') || 
                                        ticketResponse.includes('failed to open stream') ||
                                        ticketResponse.includes('No such file or directory') ||
                                        ticketResponse.includes('Failed to copy file to printer')
                                    )) {
                                        // Hay un error en la impresión, mostrar alert
                                        console.error("Error al imprimir ticket:", ticketResponse);
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Pago registrado, pero hubo un problema al imprimir el ticket',
                                            text: 'El pago se guardó correctamente (Ticket: ' + response.NumTicket + '), pero no se pudo imprimir el ticket. Verifica la conexión con la impresora.',
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonColor: '#ffc107'
                                        }).then((result) => {
                                            $('#ModalEdDele').modal('hide');
                                            location.reload();
                                        });
                                    } else {
                                        console.log("Ticket enviado correctamente:", ticketResponse);
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Pago registrado y ticket generado!',
                                            text: 'Ticket: ' + response.NumTicket,
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonColor: '#28a745'
                                        }).then((result) => {
                                            $('#ModalEdDele').modal('hide');
                                            // Recargar la página o actualizar la lista
                                            location.reload();
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error al generar ticket:", error);
                                    console.log("Respuesta del servidor:", xhr.responseText);
                                    // Aún así mostrar éxito si el registro fue exitoso
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Pago registrado, pero hubo un problema al imprimir el ticket',
                                        text: 'El pago se guardó correctamente (Ticket: ' + response.NumTicket + '), pero no se pudo imprimir el ticket. Verifica la conexión con la impresora.',
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#ffc107'
                                    }).then((result) => {
                                        $('#ModalEdDele').modal('hide');
                                        location.reload();
                                    });
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al registrar pago de servicio',
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

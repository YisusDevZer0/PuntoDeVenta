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
                    <label for="monto" class="form-label">Monto:</label>
                    <input type="number" step="0.01" name="monto" id="monto" class="form-control" placeholder="0.00" required>
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
                        Swal.fire({
                            icon: 'success',
                            title: '¡Pago de servicio registrado exitosamente!',
                            text: 'Ticket: ' + response.NumTicket,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            $('#ModalEdDele').modal('hide');
                            // Recargar la página o actualizar la lista
                            location.reload();
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

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

<!-- Select2 CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="RegistrarEncargoForm" class="mb-3">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2-pacientes" id="clienteInput" name="nombre_paciente" style="width: 100%;" required>
                            <option value="">Buscar paciente...</option>
                        </select>
                        <input type="hidden" id="id_paciente" name="id_paciente">
                    </div>
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
        // Inicializar Select2 para el selector de pacientes
        $('#clienteInput').select2({
            theme: 'bootstrap-5',
            placeholder: 'Buscar paciente...',
            allowClear: true,
            ajax: {
                url: '../Controladores/BuscarPacientes.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        sucursal: '<?php echo $row['Fk_Sucursal']; ?>'
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.nombre_paciente,
                                telefono: item.telefono || 'N/A',
                                edad: item.edad || 'N/A',
                                sexo: item.sexo || 'N/A'
                            };
                        })
                    };
                },
                cache: true
            },
            templateResult: formatPaciente,
            templateSelection: function(paciente) {
                return paciente.text || paciente.id || '';
            },
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return 'Por favor ingrese 2 o más caracteres...';
                },
                searching: function() {
                    return 'Buscando...';
                },
                noResults: function() {
                    return 'No se encontraron resultados';
                },
                errorLoading: function() {
                    return 'Error al cargar los resultados';
                }
            }
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $("#id_paciente").val(data.id);
        });

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

    function formatPaciente(paciente) {
        if (paciente.loading) {
            return paciente.text;
        }
        if (!paciente.id) {
            return paciente.text;
        }
        var $container = $(
            "<div class='select2-result-paciente'>" +
                "<div class='select2-result-paciente__nombre'>" + paciente.text + "</div>" +
                "<div class='select2-result-paciente__info'>" +
                    "<small>Tel: " + (paciente.telefono || 'N/A') + " | " +
                    "Edad: " + (paciente.edad || 'N/A') + " | " +
                    "Sexo: " + (paciente.sexo || 'N/A') + "</small>" +
                "</div>" +
            "</div>"
        );
        return $container;
    }
    </script>

    <style>
    /* Estilos para Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.5;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        padding: 8px 12px;
    }

    .select2-result-paciente {
        padding: 4px 0;
    }

    .select2-result-paciente__nombre {
        font-weight: bold;
        margin-bottom: 2px;
    }

    .select2-result-paciente__info {
        color: #666;
        font-size: 0.9em;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
        color: white;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] .select2-result-paciente__info {
        color: #e6e6e6;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #ced4da;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    </style>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra la caja especificada</p>
<?php endif; ?>

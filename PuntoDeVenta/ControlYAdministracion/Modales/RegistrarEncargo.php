<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<!-- Agregar las dependencias de jQuery UI en el head -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="RegistrarEncargoForm" class="mb-3">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="clienteInput" name="nombre_paciente" required>
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
                    <input type="date" name="fecha_encargo" id="fecha_encargo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="costo" class="form-label">Costo:</label>
                    <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="abono_parcial" class="form-label">Abono realizado:</label>
                    <input type="number" step="0.01" name="abono_parcial" id="abono_parcial" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="NumTicket" class="form-label">Número de Ticket:</label>
                    <input type="text" name="NumTicket" id="NumTicket" class="form-control" required>
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

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Registrar Encargo</button>
        </div>
    </form>

    <script src="js/RegistrarEncargo.js"></script>

    <script>
    $(document).ready(function() {
        // Verificar que jQuery UI esté cargado
        if (typeof $.fn.autocomplete === 'undefined') {
            console.error('jQuery UI no está cargado');
            return;
        }

        $("#clienteInput").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/BusquedaClientes.php",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        // Decodificar las entidades HTML en los nombres
                        var items = $.map(data, function(item) {
                            return {
                                label: $('<div>').html(item.Nombre_Paciente).text(), // Decodifica HTML entities
                                value: $('<div>').html(item.Nombre_Paciente).text(), // Decodifica HTML entities
                                id: item.id,
                                telefono: item.telefono,
                                edad: $('<div>').html(item.edad).text()
                            };
                        });
                        response(items);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la búsqueda:', error);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                event.preventDefault();
                $("#clienteInput").val(ui.item.value);
                $("#id_paciente").val(ui.item.id);
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            // Personalizar la visualización de cada item en el dropdown
            return $("<li>")
                .append("<div>" + 
                    "<strong>" + item.label + "</strong><br>" +
                    "<small>Tel: " + item.telefono + " | Edad: " + item.edad + "</small>" +
                    "</div>")
                .appendTo(ul);
        };
    });
    </script>

    <style>
    /* Estilos para el autocomplete */
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        z-index: 1000;
    }

    .ui-autocomplete .ui-menu-item {
        padding: 8px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .ui-autocomplete .ui-menu-item:last-child {
        border-bottom: none;
    }

    .ui-autocomplete .ui-menu-item div {
        padding: 2px 0;
    }

    .ui-autocomplete .ui-menu-item strong {
        color: #333;
        display: block;
    }

    .ui-autocomplete .ui-menu-item small {
        color: #666;
        font-size: 0.9em;
    }

    .ui-autocomplete .ui-menu-item:hover {
        background-color: #f5f5f5;
    }

    .ui-helper-hidden-accessible {
        display: none;
    }
    </style>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

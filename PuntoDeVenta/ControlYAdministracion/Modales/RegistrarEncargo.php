<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>



<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="RegistrarEncargoForm" class="mb-3">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                    <input type="text" 
                           class="form-control" 
                           list="lista-pacientes" 
                           name="nombre_paciente" 
                           id="nombre_paciente" 
                           autocomplete="off"
                           required>
                    <datalist id="lista-pacientes">
                        <?php
                        $query = $conn->query("SELECT ID_Paciente, Nombre, Apellido_Paterno, Apellido_Materno FROM Pacientes ORDER BY Nombre ASC");
                        while ($paciente = mysqli_fetch_array($query)) {
                            $nombre_completo = $paciente['Nombre'] . ' ' . $paciente['Apellido_Paterno'] . ' ' . $paciente['Apellido_Materno'];
                            echo '<option value="' . $nombre_completo . '" data-id="' . $paciente['ID_Paciente'] . '">';
                        }
                        ?>
                    </datalist>
                    <input type="hidden" name="id_paciente" id="id_paciente">
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
    document.getElementById('nombre_paciente').addEventListener('change', function() {
        // Obtener el ID del paciente seleccionado
        const datalist = document.getElementById('lista-pacientes');
        const options = datalist.getElementsByTagName('option');
        const selectedValue = this.value;
        
        for(let option of options) {
            if(option.value === selectedValue) {
                document.getElementById('id_paciente').value = option.getAttribute('data-id');
                break;
            }
        }
    });
    </script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

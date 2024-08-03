<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Registro de Encargos de Medicamentos</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
</head>

<body>
    <?php include "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid pt-4 px-8">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Registro de Encargos de Medicamentos</h6>
                    <form id="formEncargo" action="guardar_encargo.php" method="post">
                        <div class="mb-3">
                            <label for="nombre_paciente" class="form-label">Nombre del Paciente</label>
                            <input type="text" class="form-control" id="nombre_paciente" name="nombre_paciente" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_encargo" class="form-label">Fecha de Encargo</label>
                            <input type="date" class="form-control" id="fecha_encargo" name="fecha_encargo" required>
                        </div>
                        <div id="medicamentos_container">
                            <div class="medicamento">
                                <div class="mb-3">
                                    <label for="medicamento" class="form-label">Medicamento</label>
                                    <select class="form-control" name="medicamentos[0][id]" required>
                                        <?php
                                        include_once "Controladores/ControladorUsuario.php";
                                        $query = "SELECT ID_Prod_POS, Nombre_Prod FROM Productos_POS";
                                        if ($result = $mysqli->query($query)) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value=\"{$row['ID_Prod_POS']}\">{$row['Nombre_Prod']}</option>";
                                            }
                                            $result->free();
                                        }
                                        $mysqli->close();
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" name="medicamentos[0][cantidad]" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="add_medicamento">Añadir Medicamento</button>
                        <div class="mb-3 mt-3">
                            <label for="abono_parcial" class="form-label">Abono Parcial</label>
                            <input type="text" class="form-control" id="abono_parcial" name="abono_parcial">
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Encargo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/encargo.js"></script>
    <?php include "Footer.php"; ?>
</body>

</html>

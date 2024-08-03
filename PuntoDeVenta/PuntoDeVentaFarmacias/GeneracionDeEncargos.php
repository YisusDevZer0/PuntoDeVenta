<?php
include_once "Controladores/ControladorUsuario.php";
 include "Controladores/ConsultaCaja.php";
include "Controladores/SumadeFolioTicketsNuevo.php";
include("Controladores/db_connect.php");
$primeras_tres_letras = substr($row['Nombre_Sucursal'], 0, 3);


// Concatenar las primeras 3 letras con el valor de $totalmonto
$resultado_concatenado = $primeras_tres_letras . $totalmonto;

// Convertir el resultado a mayúsculas
$resultado_en_mayusculas = strtoupper($resultado_concatenado);

// Imprimir el resultado en mayúsculas





include_once "db_config.php"; // Incluye la configuración de tu base de datos


// Manejo del formulario de encargo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo_producto']) && isset($_POST['cantidad'])) {
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad = $_POST['cantidad'];

    // Consulta para buscar el producto
    $sql = "SELECT Cod_Barra, Nombre_Prod, Precio_Venta, Precio_C, Tipo_Servicio, Proveedor1, Proveedor2, Licencia FROM Productos_POS WHERE Cod_Barra = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Producto encontrado
        $producto = $result->fetch_assoc();
        // Aquí podrías agregar el encargo a la base de datos
        $sql_encargo = "INSERT INTO Encargos (Cod_Barra, Cantidad, Fecha) VALUES (?, ?, NOW())";
        $stmt_encargo = $conn->prepare($sql_encargo);
        $stmt_encargo->bind_param("si", $codigo_producto, $cantidad);
        $stmt_encargo->execute();
        $stmt_encargo->close();
        $mensaje = "Encargo realizado exitosamente para el producto: " . $producto['Nombre_Prod'];
    } else {
        // Producto no encontrado
        $mensaje = "Producto no encontrado en la base de datos.";
    }
    $stmt->close();
}

// Cierra la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Encargos de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php"; ?>
        <!-- Navbar End -->

        <!-- Table Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Encargo de Productos - <?php echo $row['Licencia']?> sucursal <?php echo $row['Nombre_Sucursal']?> </h6>
                    
                    <!-- Formulario para ingresar un encargo -->
                    <form id="formEncargo" method="POST" action="">
                        <div class="form-group">
                            <label for="codigo_producto">Código de Producto:</label>
                            <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" required>
                        </div>
                        <div class="form-group">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Realizar Encargo</button>
                    </form>

                    <?php if (isset($mensaje)): ?>
                        <div class="alert alert-info mt-4">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <div id="DataDeClientes"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <script src="js/ControlDeListaDeTraspasos.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on("click", ".btn-AceptarTraspaso", function() {
                var id = $(this).data("id");
                $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/AceptaTraspasosPez.php", { id: id }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("Verificación del traspaso");
                    $("#Di").addClass("modal-dialog modal-xl modal-notify modal-info");
                });
                $('#ModalEdDele').modal('show');
            });
        });
    </script>

    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
        <div id="Di" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header" style=" background-color: #ef7980 !important;" >
                        <p class="heading lead" id="TitulosCajas" style="color:white;"></p>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div id="FormCajas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Start -->
    <?php 
    include "Modales/NuevoFondoDeCaja.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";?>
</body>
</html>

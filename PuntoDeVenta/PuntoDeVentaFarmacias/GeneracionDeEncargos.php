<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/ConsultaCaja.php";
include_once "Controladores/SumadeFolioTicketsNuevo.php";
include_once "Controladores/db_connect.php";
include_once "db_config.php"; // Incluye la configuración de tu base de datos

// Manejo del formulario de encargo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productos']) && isset($_POST['nombre_cliente']) && isset($_POST['abono'])) {
    $productos = $_POST['productos']; // Array de productos
    $nombre_cliente = $_POST['nombre_cliente'];
    $abono = $_POST['abono'];
    
    // Conexión a la base de datos
   
    // Variables para el total y faltante
    $total = 0;

    foreach ($productos as $producto) {
        $codigo_producto = $producto['codigo'];
        $cantidad = $producto['cantidad'];

        // Consulta para buscar el producto
        $sql = "SELECT Cod_Barra, Nombre_Prod, Precio_Venta FROM Productos_POS WHERE Cod_Barra = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $codigo_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Producto encontrado
            $producto_data = $result->fetch_assoc();
            $precio = $producto_data['Precio_Venta'];
            $total += $precio * $cantidad;

            // Agrega el encargo a la base de datos
            $sql_encargo = "INSERT INTO Encargos (Cod_Barra, Cantidad, Fecha, Nombre_Cliente, Abono, Total) VALUES (?, ?, NOW(), ?, ?, ?)";
            $stmt_encargo = $conn->prepare($sql_encargo);
            $stmt_encargo->bind_param("sissi", $codigo_producto, $cantidad, $nombre_cliente, $abono, $total);
            $stmt_encargo->execute();
            $stmt_encargo->close();
        } else {
            // Producto no encontrado
            $mensaje = "Producto con código $codigo_producto no encontrado en la base de datos.";
        }
        $stmt->close();
    }

    // Calcula el faltante por pagar
    $faltante = $total - $abono;

    $mensaje = "Encargo realizado exitosamente. Total: $total, Abono: $abono, Faltante por pagar: $faltante";
    
    // Cierra la conexión
    $conn->close();
}
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
                            <label for="nombre_cliente">Nombre del Cliente:</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                        </div>
                        <div class="form-group">
                            <label for="abono">Abono:</label>
                            <input type="number" class="form-control" id="abono" name="abono" required>
                        </div>
                        <div id="productos-container">
                            <div class="form-group">
                                <label for="codigo_producto_1">Código de Producto:</label>
                                <input type="text" class="form-control" name="productos[0][codigo]" required>
                            </div>
                            <div class="form-group">
                                <label for="cantidad_1">Cantidad:</label>
                                <input type="number" class="form-control" name="productos[0][cantidad]" required>
                            </div>
                        </div>
                        <button type="button" id="add-product" class="btn btn-secondary">Agregar Producto</button>
                        <button type="submit" class="btn btn-primary">Realizar Encargo</button>
                    </form>

                    <?php if (isset($mensaje)): ?>
                        <div class="alert alert-info mt-4">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var productCount = 1;
            $("#add-product").click(function() {
                productCount++;
                $("#productos-container").append(`
                    <div class="form-group">
                        <label for="codigo_producto_${productCount}">Código de Producto:</label>
                        <input type="text" class="form-control" name="productos[${productCount - 1}][codigo]" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_${productCount}">Cantidad:</label>
                        <input type="number" class="form-control" name="productos[${productCount - 1}][cantidad]" required>
                    </div>
                `);
            });
        });
    </script>

    <!-- Modales y Scripts -->
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
                    <div class="modal-header" style="background-color: #ef7980 !important;">
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

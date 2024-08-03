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
    
   

    // Variables para el total y faltante
    $total = 0;

    foreach ($productos as $producto) {
        $codigo_producto = $producto['codigo'];
        $cantidad = $producto['cantidad'];

        // Consulta para buscar el producto por código de barra
        $sql = "SELECT Cod_Barra, Nombre_Prod, Precio_Venta FROM Productos_POS WHERE Cod_Barra = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $codigo_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Producto encontrado por código de barra
            $producto_data = $result->fetch_assoc();
        } else {
            // Si no se encuentra, buscar por nombre
            $sql = "SELECT Cod_Barra, Nombre_Prod, Precio_Venta FROM Productos_POS WHERE Nombre_Prod = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $codigo_producto); // Usar nombre como código
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $producto_data = $result->fetch_assoc();
            } else {
                // Si no se encuentra el producto, usar valores introducidos manualmente
                $producto_data = [
                    'Cod_Barra' => $codigo_producto,
                    'Nombre_Prod' => $producto['nombre'],
                    'Precio_Venta' => $producto['precio']
                ];
            }
        }

        if (isset($producto_data)) {
            $precio = $producto_data['Precio_Venta'];
            $total += $precio * $cantidad;

            // Agrega el encargo a la base de datos
            $sql_encargo = "INSERT INTO Encargos (Cod_Barra, Cantidad, Fecha, Nombre_Cliente, Abono, Total) VALUES (?, ?, NOW(), ?, ?, ?)";
            $stmt_encargo = $conn->prepare($sql_encargo);
            $stmt_encargo->bind_param("sissi", $producto_data['Cod_Barra'], $cantidad, $nombre_cliente, $abono, $total);
            $stmt_encargo->execute();
            $stmt_encargo->close();
        } else {
            $mensaje = "No se pudo procesar el producto con código $codigo_producto.";
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
                            <div class="form-group product-item">
                                <label for="codigo_producto_1">Código de Producto:</label>
                                <input type="text" class="form-control" name="productos[0][codigo]" required>
                                <input type="hidden" name="productos[0][nombre]" class="product-name">
                                <input type="hidden" name="productos[0][precio]" class="product-price">
                            </div>
                            <div class="form-group">
                                <label for="cantidad_1">Cantidad:</label>
                                <input type="number" class="form-control" name="productos[0][cantidad]" required>
                            </div>
                            <button type="button" class="btn btn-danger remove-product">Eliminar</button>
                        </div>
                        <button type="button" id="add-product" class="btn btn-secondary">Agregar Producto</button>
                        <button type="submit" class="btn btn-primary">Realizar Encargo</button>
                    </form>

                    <?php if (isset($mensaje)): ?>
                        <div class="alert alert-info mt-4">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Vista previa -->
                    <h6 class="mt-4">Vista Previa:</h6>
                    <div id="preview-container"></div>
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
                    <div class="form-group product-item">
                        <label for="codigo_producto_${productCount}">Código de Producto:</label>
                        <input type="text" class="form-control" name="productos[${productCount - 1}][codigo]" required>
                        <input type="hidden" name="productos[${productCount - 1}][nombre]" class="product-name">
                        <input type="hidden" name="productos[${productCount - 1}][precio]" class="product-price">
                    </div>
                    <div class="form-group">
                        <label for="cantidad_${productCount}">Cantidad:</label>
                        <input type="number" class="form-control" name="productos[${productCount - 1}][cantidad]" required>
                    </div>
                    <button type="button" class="btn btn-danger remove-product">Eliminar</button>
                `);
            });

            // Eliminar un campo de producto
            $(document).on("click", ".remove-product", function() {
                $(this).closest(".product-item").next().remove(); // Remove quantity field
                $(this).closest(".product-item").remove(); // Remove product field
            });

            // Actualizar vista previa
            $("#formEncargo").on("input", function() {
                var previewHtml = "<h6>Productos:</h6><ul>";
                $("input[name^='productos'][name$='codigo']").each(function() {
                    var parentDiv = $(this).closest(".product-item");
                    var codigo = $(this).val();
                    var cantidad = parentDiv.find("input[name$='cantidad']").val();
                    var nombre = parentDiv.find("input.product-name").val();
                    var precio = parentDiv.find("input.product-price").val();

                    if (codigo) {
                        previewHtml += `<li>Producto: ${nombre ? nombre : codigo} - Cantidad: ${cantidad} - Precio: ${precio ? precio : 'No disponible'}</li>`;
                    }
                });
                previewHtml += "</ul>";
                previewHtml += `<h6>Nombre del Cliente:</h6><p>${$("#nombre_cliente").val()}</p>`;
                previewHtml += `<h6>Abono:</h6><p>${$("#abono").val()}</p>`;
                $("#preview-container").html(previewHtml);
            });

            // Autocompletar nombre de producto y buscar precios
            $(document).on("input", "input[name^='productos'][name$='codigo']", function() {
                var input = $(this);
                var codigo = input.val();

                if (codigo) {
                    $.ajax({
                        url: "search_product.php",
                        type: "POST",
                        data: { codigo: codigo },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.found) {
                                input.siblings("input.product-name").val(data.nombre);
                                input.siblings("input.product-price").val(data.precio);
                            } else {
                                input.siblings("input.product-name").val('');
                                input.siblings("input.product-price").val('');
                            }
                            $("#formEncargo").trigger("input"); // Actualiza vista previa
                        }
                    });
                }
            });

            // Mostrar mensaje si producto no existe
            $(document).on("input", "input[name^='productos'][name$='nombre']", function() {
                var input = $(this);
                var nombre = input.val();
                if (nombre) {
                    $.ajax({
                        url: "search_product_by_name.php",
                        type: "POST",
                        data: { nombre: nombre },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (!data.found) {
                                alert("Producto no encontrado en la base de datos. Asegúrate de ingresar todos los detalles manualmente.");
                            }
                            $("#formEncargo").trigger("input"); // Actualiza vista previa
                        }
                    });
                }
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

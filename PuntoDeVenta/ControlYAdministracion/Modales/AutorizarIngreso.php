<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Asegurarse de que se recibe el ID a través de POST y se valida
$idProdCedis = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($idProdCedis) {
    // Preparar la consulta para evitar inyecciones SQL
    $stmt = $conn->prepare(
        "SELECT 
            si.IdProdCedis, 
            si.ID_Prod_POS, 
            si.NumFactura, 
            si.Proveedor, 
            si.Cod_Barra, 
            si.Nombre_Prod, 
            si.Fk_Sucursal, 
            s.Nombre_Sucursal,
            si.Contabilizado, 
            si.Fecha_Caducidad, 
            si.Lote, 
            si.PrecioMaximo, 
            si.Precio_Venta, 
            si.Precio_C, 
            si.AgregadoPor, 
            si.AgregadoEl, 
            si.FechaInventario, 
            si.Estatus, 
            si.NumOrden
        FROM 
            Solicitudes_Ingresos si
        JOIN 
            Sucursales s ON si.Fk_Sucursal = s.ID_Sucursal
        WHERE 
            si.IdProdCedis = ?"
    );

    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $conn->error);
    }

    $stmt->bind_param("i", $idProdCedis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        ?>
        <!-- Formulario HTML -->
        <style>
            .form-container {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                max-width: 1200px;
                margin: 0 auto;
            }
            .form-group {
                display: flex;
                flex-direction: column;
            }
            .form-group label {
                margin-bottom: 5px;
                font-weight: bold;
            }
           
            .form-group select {
                padding: 8px;
                font-size: 1rem;
            }
        </style>
        <form action="javascript:void(0)" method="post" id="EliminaServiciosForm" class="form-container">
    <input type="text" id="IdProdCedis" hidden class="form-control" name="IdProdCedis" value="<?php echo htmlspecialchars($data['IdProdCedis'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
    <input type="text" id="ID_Prod_POS" class="form-control" name="ID_Prod_POS" value="<?php echo htmlspecialchars($data['ID_Prod_POS'], ENT_QUOTES, 'UTF-8'); ?>" hidden readonly>
    <div class="form-group">
        <label for="Proveedor">Proveedor:</label>
        <input type="text" id="Proveedor" class="form-control" name="Proveedor" value="<?php echo htmlspecialchars($data['Proveedor'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="NumFactura">Número de Factura:</label>
        <input type="text" id="NumFactura" class="form-control" name="NumFactura" value="<?php echo htmlspecialchars($data['NumFactura'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Cod_Barra">Código de Barra:</label>
        <input type="text" id="Cod_Barra" class="form-control" name="Cod_Barra" value="<?php echo htmlspecialchars($data['Cod_Barra'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Nombre_Prod">Nombre del Producto:</label>
        <input type="text" id="Nombre_Prod" class="form-control" name="Nombre_Prod" value="<?php echo htmlspecialchars($data['Nombre_Prod'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <input type="text" id="Fk_Sucursal" hidden name="Fk_Sucursal" value="<?php echo htmlspecialchars($data['Fk_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
    <div class="form-group">
        <label for="Nombre_Sucursal">Nombre de la Sucursal:</label>
        <input type="text" id="Nombre_Sucursal" class="form-control" name="Nombre_Sucursal" value="<?php echo htmlspecialchars($data['Nombre_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
    </div>
    <div class="form-group">
        <label for="Contabilizado">Piezas:</label>
        <input type="text" id="Contabilizado" class="form-control" name="Contabilizado" value="<?php echo htmlspecialchars($data['Contabilizado'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Fecha_Caducidad">Fecha de Caducidad:</label>
        <input type="date" id="Fecha_Caducidad" class="form-control" name="Fecha_Caducidad" value="<?php echo htmlspecialchars($data['Fecha_Caducidad'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Lote">Lote:</label>
        <input type="text" id="Lote" name="Lote" class="form-control" value="<?php echo htmlspecialchars($data['Lote'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="PrecioMaximo">Precio Máximo:</label>
        <input type="text" id="PrecioMaximo" class="form-control" name="PrecioMaximo" value="<?php echo htmlspecialchars($data['PrecioMaximo'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Precio_Venta">Precio de Venta:</label>
        <input type="text" id="Precio_Venta" class="form-control" name="Precio_Venta" value="<?php echo htmlspecialchars($data['Precio_Venta'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="Precio_C">Precio de Compra:</label>
        <input type="text" id="Precio_C" class="form-control" name="Precio_C" value="<?php echo htmlspecialchars($data['Precio_C'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="PrecioVentaAutorizado">Precio Autorizado de Venta:</label>
        <input type="text" id="PrecioVentaAutorizado" class="form-control" name="PrecioVentaAutorizado" value="<?php echo htmlspecialchars($data['PrecioVentaAutorizado'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <input hidden type="text" id="AgregadoPor" name="AgregadoPor" value="<?php echo htmlspecialchars($data['AgregadoPor'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
    <input hidden type="text" id="AgregadoEl" name="AgregadoEl" value="<?php echo htmlspecialchars($data['AgregadoEl'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
    <input hidden type="date" id="FechaInventario" name="FechaInventario" value="<?php echo htmlspecialchars($data['FechaInventario'], ENT_QUOTES, 'UTF-8'); ?>">
    <input hidden type="text" id="NumOrden" name="NumOrden" value="<?php echo htmlspecialchars($data['NumOrden'], ENT_QUOTES, 'UTF-8'); ?>">
    <input hidden type="text" id="SolicitadoPor" name="SolicitadoPor" value="<?php echo htmlspecialchars($data['SolicitadoPor'], ENT_QUOTES, 'UTF-8'); ?>">
    <div class="form-group text-center">
        <button type="submit" class="btn btn-success">Aprobar ingreso de medicamento</button>
    </div>
</form>

        <?php
    } else {
        echo "No se encontraron registros.";
    }

    $stmt->close();
} else {
    echo "ID inválido.";
}
?>
<script src="js/RegistraAutorizacionIngreso.js"></script>
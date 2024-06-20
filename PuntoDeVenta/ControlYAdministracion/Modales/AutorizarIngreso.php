<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Validar y sanitizar el ID desde GET o POST
$idProdCedis = filter_input(INPUT_GET, 'IdProdCedis', FILTER_SANITIZE_NUMBER_INT);
if (!$idProdCedis) {
    $idProdCedis = filter_input(INPUT_POST, 'IdProdCedis', FILTER_SANITIZE_NUMBER_INT);
}

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

    // Verificar si la declaración se preparó correctamente
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
            .form-group input {
                padding: 8px;
                font-size: 1rem;
            }
            .form-group select {
                padding: 8px;
                font-size: 1rem;
            }
        </style>
        
        <form action="tu_proceso.php" method="post" class="form-container">
            <div class="form-group">
                <label for="IdProdCedis">ID Prod Cedis:</label>
                <input type="text" id="IdProdCedis" name="IdProdCedis" value="<?php echo htmlspecialchars($data['IdProdCedis'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="ID_Prod_POS">ID Prod POS:</label>
                <input type="text" id="ID_Prod_POS" name="ID_Prod_POS" value="<?php echo htmlspecialchars($data['ID_Prod_POS'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="NumFactura">Número de Factura:</label>
                <input type="text" id="NumFactura" name="NumFactura" value="<?php echo htmlspecialchars($data['NumFactura'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Proveedor">Proveedor:</label>
                <input type="text" id="Proveedor" name="Proveedor" value="<?php echo htmlspecialchars($data['Proveedor'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Cod_Barra">Código de Barra:</label>
                <input type="text" id="Cod_Barra" name="Cod_Barra" value="<?php echo htmlspecialchars($data['Cod_Barra'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto:</label>
                <input type="text" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo htmlspecialchars($data['Nombre_Prod'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Fk_Sucursal">Sucursal:</label>
                <input type="text" id="Fk_Sucursal" name="Fk_Sucursal" value="<?php echo htmlspecialchars($data['Fk_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="Nombre_Sucursal">Nombre de la Sucursal:</label>
                <input type="text" id="Nombre_Sucursal" name="Nombre_Sucursal" value="<?php echo htmlspecialchars($data['Nombre_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="Contabilizado">Contabilizado:</label>
                <input type="text" id="Contabilizado" name="Contabilizado" value="<?php echo htmlspecialchars($data['Contabilizado'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Fecha_Caducidad">Fecha de Caducidad:</label>
                <input type="date" id="Fecha_Caducidad" name="Fecha_Caducidad" value="<?php echo htmlspecialchars($data['Fecha_Caducidad'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Lote">Lote:</label>
                <input type="text" id="Lote" name="Lote" value="<?php echo htmlspecialchars($data['Lote'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="PrecioMaximo">Precio Máximo:</label>
                <input type="text" id="PrecioMaximo" name="PrecioMaximo" value="<?php echo htmlspecialchars($data['PrecioMaximo'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Precio_Venta">Precio de Venta:</label>
                <input type="text" id="Precio_Venta" name="Precio_Venta" value="<?php echo htmlspecialchars($data['Precio_Venta'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Precio_C">Precio de Compra:</label>
                <input type="text" id="Precio_C" name="Precio_C" value="<?php echo htmlspecialchars($data['Precio_C'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="AgregadoPor">Agregado Por:</label>
                <input type="text" id="AgregadoPor" name="AgregadoPor" value="<?php echo htmlspecialchars($data['AgregadoPor'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="AgregadoEl">Agregado El:</label>
                <input type="text" id="AgregadoEl" name="AgregadoEl" value="<?php echo htmlspecialchars($data['AgregadoEl'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="FechaInventario">Fecha de Inventario:</label>
                <input type="date" id="FechaInventario" name="FechaInventario" value="<?php echo htmlspecialchars($data['FechaInventario'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="Estatus">Estatus:</label>
                <input type="text" id="Estatus" name="Estatus" value="<?php echo htmlspecialchars($data['Estatus'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="NumOrden">Número de Orden:</label>
                <input type="text" id="NumOrden" name="NumOrden" value="<?php echo htmlspecialchars($data['NumOrden'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Enviar</button>
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

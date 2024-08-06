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
                display: none; /* Ocultar formulario inicialmente */
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
            .form-group input,
            .form-group select {
                padding: 8px;
                font-size: 1rem;
            }
            .btn-confirm {
                background-color: #ff4d4d;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .btn-confirm:hover {
                background-color: #e60000;
            }
        </style>
        <div id="confirmation-message">
            <p>¿Estás seguro de querer eliminar esta solicitud?</p>
            
        </div>
        <!-- <form action="javascript:void(0)" method="post" id="GuardaMedicamentoAutorizados" class="form-container"> -->
            <input type="text" id="IdProdCedis" hidden class="form-control" name="IdProdCedis" value="<?php echo htmlspecialchars($data['IdProdCedis'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="text" id="ID_Prod_POS" class="form-control" name="ID_Prod_POS" value="<?php echo htmlspecialchars($data['ID_Prod_POS'], ENT_QUOTES, 'UTF-8'); ?>" hidden readonly>
            <div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="Proveedor">Proveedor:</label>
                <input type="text" id="Proveedor" class="form-control" name="Proveedor" value="<?php echo htmlspecialchars($data['Proveedor'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="NumFactura">Número de Factura:</label>
                <input type="text" id="NumFactura" class="form-control" name="NumFactura" value="<?php echo htmlspecialchars($data['NumFactura'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="Cod_Barra">Código de Barra:</label>
                <input type="text" id="Cod_Barra" class="form-control" name="Cod_Barra" value="<?php echo htmlspecialchars($data['Cod_Barra'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto:</label>
                <input type="text" id="Nombre_Prod" class="form-control" name="Nombre_Prod" value="<?php echo htmlspecialchars($data['Nombre_Prod'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <input type="text" id="Fk_Sucursal" hidden name="Fk_Sucursal" value="<?php echo htmlspecialchars($data['Fk_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <div class="form-group">
                <label for="Contabilizado">Piezas:</label>
                <input type="text" id="Contabilizado" class="form-control" name="Contabilizado" value="<?php echo htmlspecialchars($data['Contabilizado'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div 

           
           
            <input hidden type="text" id="AgregadoPor" name="AgregadoPor" value="<?php echo htmlspecialchars($data['AgregadoPor'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input hidden type="text" id="AgregadoEl" name="AgregadoEl" value="<?php echo htmlspecialchars($data['AgregadoEl'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input hidden type="date" id="FechaInventario" name="FechaInventario" value="<?php echo htmlspecialchars($data['FechaInventario'], ENT_QUOTES, 'UTF-8'); ?>">
            <input hidden type="text" id="NumOrden" name="NumOrden" value="<?php echo htmlspecialchars($data['NumOrden'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group text-center">
                <button type="button" id="show-form" class="btn btn-success">Eliminar solicitud</button>
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

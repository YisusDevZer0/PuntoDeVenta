<?php
    include_once 'db_connect.php';

    $CodBarraP = mysqli_real_escape_string($conn, $_POST['CodBarraP']);
    $Clav = mysqli_real_escape_string($conn, $_POST['Clav']);
    $NombreProd = mysqli_real_escape_string($conn, $_POST['NombreProd']);
    $ComponenteActivo = mysqli_real_escape_string($conn, $_POST['ComponenteActivo']);
    $PV = mysqli_real_escape_string($conn, $_POST['PV']);
    $PC = mysqli_real_escape_string($conn, $_POST['PC']);
    $TipoServicio = mysqli_real_escape_string($conn, $_POST['TipoServicio']);
    $Receta = mysqli_real_escape_string($conn, $_POST['Receta']);
    $Tip = mysqli_real_escape_string($conn, $_POST['Tip']);
    $Categoria = mysqli_real_escape_string($conn, $_POST['Categoria']);
    $Marca = mysqli_real_escape_string($conn, $_POST['Marca']);
    $Presentacion = mysqli_real_escape_string($conn, $_POST['Presentacion']);
    $Proveedor = mysqli_real_escape_string($conn, $_POST['Proveedor']);
    $Prov2 = mysqli_real_escape_string($conn, $_POST['Prov2']);
    $EmpresaProductos = mysqli_real_escape_string($conn, $_POST['EmpresaProductos']);
    $AgregaProductosBy = mysqli_real_escape_string($conn, $_POST['AgregaProductosBy']);
    $SistemaProductos = mysqli_real_escape_string($conn, $_POST['SistemaProductos']);

    // Consulta de inserción para agregar un nuevo registro
    $sql = "INSERT INTO `Productos_POS`(`Cod_Barra`, `Clave_adicional`, `Nombre_Prod`, `Componente_Activo`, `Precio_Venta`, `Precio_C`, `Tipo_Servicio`, `RecetaMedica`, `Tipo`, `FkCategoria`, `FkMarca`, `FkPresentacion`, `Proveedor1`, `Proveedor2`, `EmpresaProductos`, `AgregadoPor`, `AgregadoEl`, `Licencia`) 
            VALUES ('$CodBarraP', '$Clav', '$NombreProd', '$ComponenteActivo', '$PV', '$PC', '$TipoServicio', '$Receta', '$Tip', '$Categoria', '$Marca', '$Presentacion', '$Proveedor', '$Prov2', '$EmpresaProductos', '$AgregaProductosBy', '$SistemaProductos', '$Licencia')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode"=>201, "error" => mysqli_error($conn))); // Error en la inserción
    }

    mysqli_close($conn);
?>

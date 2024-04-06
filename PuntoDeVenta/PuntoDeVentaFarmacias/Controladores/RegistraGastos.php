<?php
include_once 'db_connect.php';

// Verificar si se recibieron todos los datos necesarios
if(isset($_POST['Concepto_Categoria'], $_POST['Importe_Total'], $_POST['Empleado'], $_POST['Fk_sucursal'], $_POST['Fk_Caja'], $_POST['Recibe'], $_POST['Sistema'], $_POST['AgregadoPor'], $_POST['Licencia'])) {
    // Escapar y asignar los valores recibidos
    $ConceptoCategoria = mysqli_real_escape_string($conn, $_POST['Concepto_Categoria']);
    $ImporteTotal = mysqli_real_escape_string($conn, $_POST['Importe_Total']);
    $Empleado = mysqli_real_escape_string($conn, $_POST['Empleado']);
    $FkSucursal = mysqli_real_escape_string($conn, $_POST['Fk_sucursal']);
    $FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
    $Recibe = mysqli_real_escape_string($conn, $_POST['Recibe']);
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $AgregadoPor = mysqli_real_escape_string($conn, $_POST['AgregadoPor']);
    $Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);

    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Concepto_Categoria, Licencia FROM GastosPOS WHERE Concepto_Categoria='$ConceptoCategoria' AND Licencia='$Licencia'";
    $resultset = mysqli_query($conn, $sql);

    if($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode"=>250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `GastosPOS`(`Concepto_Categoria`, `Importe_Total`, `Empleado`, `Fk_sucursal`, `Fk_Caja`, `Recibe`, `Sistema`, `Agregado_Por`, `Licencia`) 
                VALUES ('$ConceptoCategoria', '$ImporteTotal', '$Empleado', '$FkSucursal', '$FkCaja', '$Recibe', '$Sistema', '$AgregadoPor', '$Licencia')";

        if(mysqli_query($conn, $sql_insert)) {
            echo json_encode(array("statusCode"=>200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode"=>201, "error"=>mysqli_error($conn))); // Error en la inserción
        }
    }
} else {
    echo json_encode(array("statusCode"=>500, "error"=>"No se recibieron todos los datos necesarios")); // Error de datos faltantes
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<?php
include_once 'db_connect.php';

$Cod_Barra = mysqli_real_escape_string($conn, $_POST['CodBarraP']);
$Clave_adicional = mysqli_real_escape_string($conn, $_POST['Clav']);
$Clave_Levic = ''; // Este valor no está presente en el formulario, puedes asignarle un valor si lo deseas
$Nombre_Prod = mysqli_real_escape_string($conn, $_POST['NombreProd']);
$Precio_Venta = mysqli_real_escape_string($conn, $_POST['PV']);
$Precio_C = mysqli_real_escape_string($conn, $_POST['PC']);
$Tipo_Servicio = mysqli_real_escape_string($conn, $_POST['TipoServicio']);
$Componente_Activo = mysqli_real_escape_string($conn, $_POST['ComponenteActivo']);
$Tipo = mysqli_real_escape_string($conn, $_POST['Tip']);
$FkCategoria = mysqli_real_escape_string($conn, $_POST['Categoria']);
$FkMarca = mysqli_real_escape_string($conn, $_POST['Marca']);
$FkPresentacion = mysqli_real_escape_string($conn, $_POST['Presentacion']);
$Proveedor1 = mysqli_real_escape_string($conn, $_POST['Proveedor']);
$RecetaMedica = mysqli_real_escape_string($conn, $_POST['Receta']);
$Estatus = ''; // Este valor no está presente en el formulario, puedes asignarle un valor si lo deseas
$AgregadoPor = mysqli_real_escape_string($conn, $_POST['AgregadoPor']);
$AgregadoEl = ''; // Este valor no está presente en el formulario, puedes asignarle un valor si lo deseas
$Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);

// Consulta para verificar si ya existe un registro con los mismos valores
$sql = "SELECT Cod_Barra, Licencia FROM Productos_POS WHERE Cod_Barra='$Cod_Barra' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

if(mysqli_num_rows($resultset) > 0) {
    echo json_encode(array("statusCode"=>250)); // El registro ya existe
} else {
    // Consulta de inserción para agregar un nuevo registro
    $sql = "INSERT INTO `Productos_POS`(`Cod_Barra`, `Clave_adicional`, `Clave_Levic`, `Nombre_Prod`, `Precio_Venta`, `Precio_C`, `Tipo_Servicio`, `Componente_Activo`, `Tipo`, `FkCategoria`, `FkMarca`, `FkPresentacion`, `Proveedor1`, `RecetaMedica`, `Estatus`, `AgregadoPor`, `AgregadoEl`, `Licencia`) 
            VALUES ('$Cod_Barra', '$Clave_adicional', '$Clave_Levic', '$Nombre_Prod', '$Precio_Venta', '$Precio_C', '$Tipo_Servicio', '$Componente_Activo', '$Tipo', '$FkCategoria', '$FkMarca', '$FkPresentacion', '$Proveedor1', '$RecetaMedica', '$Estatus', '$AgregadoPor', '$AgregadoEl', '$Licencia')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode"=>201)); // Error en la inserción
    }
    mysqli_close($conn);
}
?>

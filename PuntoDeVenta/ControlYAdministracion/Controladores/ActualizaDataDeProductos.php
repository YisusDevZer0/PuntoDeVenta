<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos
// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $ID_Prod_POS = $_POST['ID_Prod_POS'];
    $Cod_Barra = $_POST['Cod_Barra'];
    $Clave_adicional = $_POST['Clave_adicional'];
    $Clave_Levic = $_POST['Clave_Levic'];
    $Nombre_Prod = $_POST['Nombre_Prod'];
    $Precio_Venta = $_POST['Precio_Venta'];
    $Precio_C = $_POST['Precio_C'];
    $Tipo_Servicio = $_POST['Tipo_Servicio'];
    $Componente_Activo = $_POST['Componente_Activo'];
    $Tipo = $_POST['Tipo'];
    $FkCategoria = $_POST['FkCategoria'];
    $FkMarca = $_POST['FkMarca'];
    $FkPresentacion = $_POST['FkPresentacion'];
    $Proveedor1 = $_POST['Proveedor1'];
    $Proveedor2 = $_POST['Proveedor2'];
    $RecetaMedica = $_POST['RecetaMedica'];
    $Ivaal16 = $_POST['Ivaal16'];
    $ActualizadoPor = $_POST['ActualizadoPor'];
    
    // Crear la consulta de actualización
    $sql = "UPDATE Productos_POS SET 
                Cod_Barra = ?, 
                Clave_adicional = ?, 
                Clave_Levic = ?, 
                Nombre_Prod = ?, 
                Precio_Venta = ?, 
                Precio_C = ?, 
                Tipo_Servicio = ?, 
                Componente_Activo = ?, 
                Tipo = ?, 
                FkCategoria = ?, 
                FkMarca = ?, 
                FkPresentacion = ?, 
                Proveedor1 = ?, 
                Proveedor2 = ?, 
                RecetaMedica = ?, 
                Ivaal16 = ?, 
                ActualizadoPor = ?,
                ActualizadoEl = NOW()
            WHERE ID_Prod_POS = ?";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param("sssssssssssssssssi", 
            $Cod_Barra, $Clave_adicional, $Clave_Levic, $Nombre_Prod, 
            $Precio_Venta, $Precio_C, $Tipo_Servicio, $Componente_Activo, 
            $Tipo, $FkCategoria, $FkMarca, $FkPresentacion, 
            $Proveedor1, $Proveedor2, $RecetaMedica, $Ivaal16, 
            $ActualizadoPor, $ID_Prod_POS);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Registro actualizado correctamente.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>

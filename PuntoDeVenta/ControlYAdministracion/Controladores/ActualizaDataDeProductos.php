<?php
include "db_connect.php"; // Asegúrate de que la conexión a la base de datos es correcta

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mostrar los datos recibidos para depuración
    echo "<h3>Datos recibidos:</h3><pre>";
    print_r($_POST);
    echo "</pre>";

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
    $Tipo = $_POST['Tip'];
    $FkCategoria = $_POST['Categoria'];
    $FkMarca = $_POST['Marca'];
    $FkPresentacion = $_POST['Presentacion'];
    $Proveedor1 = $_POST['Proveedor'];
    $Proveedor2 = $_POST['Prov2'];
    
    $RecetaMedica = $_POST['RecetaMedica'];
    $Ivaal16 = $_POST['Ivaal16'];
    $ActualizadoPor = $_POST['ActualizadoPor'];

    // Depuración: mostrar valores de las variables
    // echo "<h3>Valores asignados a variables:</h3>";
    // var_dump($ID_Prod_POS, $Cod_Barra, $Clave_adicional, $Clave_Levic, $Nombre_Prod, 
    //     $Precio_Venta, $Precio_C, $Tipo_Servicio, $Componente_Activo, 
    //     $Tipo, $FkCategoria, $FkMarca, $FkPresentacion, 
    //     $Proveedor1, $Proveedor2, $RecetaMedica, $Ivaal16, 
    //     $ActualizadoPor);

    // Consulta SQL para depuración (sin ejecución)
    $sql_debug = "UPDATE Productos_POS SET 
                Cod_Barra = '{$Cod_Barra}', 
                Clave_adicional = '{$Clave_adicional}', 
                Clave_Levic = '{$Clave_Levic}', 
                Nombre_Prod = '{$Nombre_Prod}', 
                Precio_Venta = '{$Precio_Venta}', 
                Precio_C = '{$Precio_C}', 
                Tipo_Servicio = '{$Tipo_Servicio}', 
                Componente_Activo = '{$Componente_Activo}', 
                Tipo = '{$Tipo}', 
                FkCategoria = '{$FkCategoria}', 
                FkMarca = '{$FkMarca}', 
                FkPresentacion = '{$FkPresentacion}', 
                Proveedor1 = '{$Proveedor1}', 
                Proveedor2 = '{$Proveedor2}', 
                RecetaMedica = '{$RecetaMedica}', 
                Ivaal16 = '{$Ivaal16}', 
                ActualizadoPor = '{$ActualizadoPor}',
                ActualizadoEl = NOW()
            WHERE ID_Prod_POS = '{$ID_Prod_POS}'";

    

    // Preparar la consulta segura con parámetros
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

    // Verificar conexión a la base de datos
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    } else {
        echo "<h3>Conexión exitosa a la base de datos.</h3>";
    }

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros a la consulta
        $stmt->bind_param("sssssssssssssssssi", 
            $Cod_Barra, $Clave_adicional, $Clave_Levic, $Nombre_Prod, 
            $Precio_Venta, $Precio_C, $Tipo_Servicio, $Componente_Activo, 
            $Tipo, $FkCategoria, $FkMarca, $FkPresentacion, 
            $Proveedor1, $Proveedor2, $RecetaMedica, $Ivaal16, 
            $ActualizadoPor, $ID_Prod_POS);

        // Ejecutar la consulta y verificar errores
        if ($stmt->execute()) {
            echo "<h3 style='color: green;'>Registro actualizado correctamente.</h3>";
        } else {
            echo "<h3 style='color: red;'>Error al actualizar: " . $stmt->error . "</h3>";
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "<h3 style='color: red;'>Error al preparar la consulta: " . $conn->error . "</h3>";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>

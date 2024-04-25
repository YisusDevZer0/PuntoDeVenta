<?php

// Verificar si se ha enviado un valor para 'id' en POST
if(isset($_POST['id'])) {
    
    // Incluir el archivo de conexión a la base de datos
  

    // Obtener la fecha actual
    $fcha = date("Y-m-d");

    // Evitar inyección SQL escapando el valor de 'id'
    $caja_id = $conn->real_escape_string($_POST['id']);

    // Consulta SQL con parámetros preparados para evitar inyección SQL
    $sql = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal FROM Ventas_POS WHERE Fk_Caja = ? ORDER BY Venta_POS_ID ASC LIMIT 1";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular el parámetro
    $stmt->bind_param("s", $caja_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado de la consulta
    $result = $stmt->get_result();

    // Verificar si se encontraron resultados
    if($result->num_rows > 0) {
        
        // Obtener la primera fila de resultados
        $especialista = $result->fetch_object();

        // Liberar el resultado y cerrar la declaración
        $result->close();
        $stmt->close();

        // Utilizar los datos obtenidos
        // Por ejemplo: $especialista->Venta_POS_ID, $especialista->Folio_Ticket, etc.

        // Aquí puedes realizar cualquier acción adicional con los datos obtenidos
    } else {
        // No se encontraron resultados
        echo "No se encontraron ventas para la caja con ID: $caja_id";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // No se ha enviado un valor para 'id' en POST
    echo "No se proporcionó un ID de caja en la solicitud";
}
?>

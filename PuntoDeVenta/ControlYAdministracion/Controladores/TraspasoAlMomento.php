<?php
include_once "db_connect.php";

// Verificar si hay datos recibidos
if (!isset($_POST["Idprod"]) || !isset($_POST["CodBarra"]) || !isset($_POST["NombreProducto"])) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos adecuados.', 'received_data' => $_POST]);
    exit;
}

// Contar el número de productos
$contador = count($_POST["Idprod"]);
$ProContador = 0;

// Preparar la consulta SQL con marcadores de posición
$query = "INSERT INTO Traspasos_generados (Folio_Prod_Stock, ID_Prod_POS, Num_Orden, Num_Factura, Cod_Barra, Nombre_Prod, Fk_SucDestino, Precio_Venta, Precio_Compra, Cantidad_Enviada, FechaEntrega, TraspasoGeneradoPor, TraspasoRecibidoPor, Estatus, AgregadoPor, ID_H_O_D, TotaldePiezas) VALUES ";

$queryValue = [];
$values = [];

// Crear la parte de los valores de la consulta
for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["Idprod"][$i]) && !empty($_POST["CodBarra"][$i]) && !empty($_POST["NombreProducto"][$i])) {
        $ProContador++;
        $queryValue[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Agregar valores al array de valores
        $values = array_merge($values, [
            $_POST["Idprod"][$i], $_POST["Idprod"][$i], $_POST["NumeroDelTraspaso"][$i], $_POST["NumeroDeFacturaTraspaso"][$i],
            $_POST["CodBarra"][$i], $_POST["NombreProducto"][$i], $_POST["SucursalDestino"][$i], $_POST["PrecioVenta"][$i],
            $_POST["PrecioDeCompra"][$i], $_POST["NTraspasos"][$i], $_POST["FechaAprox"][$i], $_POST["GeneradoPor"][$i],
            $_POST["Recibio"][$i], $_POST["Estatus"][$i], $_POST["GeneradoPor"][$i], $_POST["Empresa"][$i], $_POST["resultadepiezas"][$i]
        ]);
    } else {
        // Si falta algún dato, mostrar el índice del producto
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos en el producto con índice ' . $i, 'received_data' => $_POST]);
        exit;
    }
}

// Verificar si se encontraron productos válidos para agregar
if ($ProContador == 0) {
    echo json_encode(['status' => 'error', 'message' => 'No se encontraron productos válidos para agregar.', 'received_data' => $_POST]);
    exit;
}

// Construir la consulta final
$query .= implode(", ", $queryValue);

// Preparar la consulta y ejecutarla
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $conn->error, 'received_data' => $_POST]);
    exit;
}

// Bind parameters
$types = str_repeat('s', count($values)); // Todos los parámetros son strings
$stmt->bind_param($types, ...$values);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Registro(s) agregado correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la consulta: ' . $stmt->error, 'received_data' => $_POST]);
}

$stmt->close();
$conn->close();
?>

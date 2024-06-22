<?php
include_once 'db_connect.php';

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
        $values = array_merge($values, [
            $_POST["Idprod"][$i], $_POST["Idprod"][$i], $_POST["NumeroDelTraspaso"][$i], $_POST["NumeroDeFacturaTraspaso"][$i],
            $_POST["CodBarra"][$i], $_POST["NombreProducto"][$i], $_POST["SucursalDestino"][$i], $_POST["PrecioVenta"][$i],
            $_POST["PrecioDeCompra"][$i], $_POST["NTraspasos"][$i], $_POST["FechaAprox"][$i], $_POST["GeneradoPor"][$i],
            $_POST["Recibio"][$i], $_POST["Estatus"][$i], $_POST["GeneradoPor"][$i], $_POST["Empresa"][$i], $_POST["resultadepiezas"][$i]
        ]);
    }
}

if ($ProContador != 0) {
    $query .= implode(", ", $queryValue);
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registro(s) agregado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al agregar los registros.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontraron productos válidos para agregar.']);
}

$conn->close();
?>

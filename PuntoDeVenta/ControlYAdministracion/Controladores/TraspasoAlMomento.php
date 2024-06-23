<?php
include_once "db_connect.php";

// Verificar si hay datos recibidos adecuados
$required_fields = [
    "CodBarras",
    "NombreDelProducto",
    "PrecioCompra",
    "PrecioVenta",
    "Contabilizado",
    "IdBasedatos",
    "AgregoElVendedor",
    "Fk_sucursal",
    "Sistema",
    "ID_H_O_D",
    "FechaAprox",
    "GeneradoPor",
    "Estatus",
    "Empresa",
    "resultadepiezas",
    "Fecha_recepcion",
    "NumeroDeFacturaTraspaso"
];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "No se recibió el campo: $field", 'received_data' => $_POST]);
        exit;
    }
}

// Contar el número de productos
$contador = count($_POST["CodBarras"]);
$ProContador = 0;

// Preparar la consulta SQL con marcadores de posición
$query = "INSERT INTO Traspasos_generados (Num_Orden, Num_Factura, Cod_Barra, Nombre_Prod, Fk_SucDestino, Precio_Venta, Precio_Compra, Cantidad_Enviada, FechaEntrega, TraspasoGeneradoPor, Estatus, AgregadoPor, AgregadoEl, ID_H_O_D, TotaldePiezas, Fecha_recepcion) VALUES ";

$queryValue = [];
$values = [];

// Crear la parte de los valores de la consulta
for ($i = 0; $i < $contador; $i++) {
    if (!empty($_POST["CodBarras"][$i]) && !empty($_POST["NombreDelProducto"][$i]) && !empty($_POST["PrecioCompra"][$i]) && isset($_POST["PrecioVenta"][$i]) && !empty($_POST["Contabilizado"][$i]) && !empty($_POST["IdBasedatos"][$i]) && !empty($_POST["AgregoElVendedor"][$i]) && !empty($_POST["Fk_sucursal"][$i]) && !empty($_POST["Sistema"][$i]) && !empty($_POST["ID_H_O_D"][$i]) && !empty($_POST["FechaAprox"][$i]) && !empty($_POST["GeneradoPor"][$i])  && !empty($_POST["Estatus"][$i]) && !empty($_POST["Empresa"][$i]) && isset($_POST["resultadepiezas"][$i]) && !empty($_POST["Fecha_recepcion"][$i]) && !empty($_POST["NumeroDeFacturaTraspaso"][$i])) {
        $ProContador++;
        $queryValue[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Agregar valores al array de valores
        $values = array_merge($values, [
            $_POST["IdBasedatos"][$i], // Supongo que 'IdBasedatos' se refiere a 'Num_Orden'
            $_POST["NumeroDeFacturaTraspaso"][$i],
            $_POST["CodBarras"][$i],
            $_POST["NombreDelProducto"][$i],
            $_POST["Fk_sucursal"][$i],
            $_POST["PrecioVenta"][$i],
            $_POST["PrecioCompra"][$i],
            $_POST["Contabilizado"][$i],
            $_POST["FechaAprox"][$i],
            $_POST["GeneradoPor"][$i],
            $_POST["Estatus"][$i],
            $_POST["AgregoElVendedor"][$i], // AgregadoPor
            $_POST["FechaAprox"][$i], // AgregadoEl
            $_POST["ID_H_O_D"][$i],
            $_POST["resultadepiezas"][$i],
            $_POST["Fecha_recepcion"][$i]
        ]);
    } else {
        // Si falta algún dato, mostrar el índice del producto y qué dato falta
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field][$i])) {
                $missing_fields[] = $field;
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos en el producto con índice ' . $i . ': ' . implode(', ', $missing_fields), 'received_data' => $_POST]);
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

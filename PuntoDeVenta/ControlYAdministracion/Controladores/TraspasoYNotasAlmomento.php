<?php
include_once "db_connect.php";

// Verificar si hay datos recibidos adecuados
$required_fields = [
    "CodBarras",
    "NombreDelProducto",
    "Cantidad",
    "Fk_sucursal",
    "Fk_SucursalDestino",
    "TotalVenta",
    "Pc",
    "TipoDeMov",
    "FechaVenta",
    "Estatus",
    "Sistema",
    "AgregadoPor",
    "ID_H_O_D",
    "Folio_Ticket"
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
$query = "INSERT INTO TraspasosYNotasC (ID_Prod_POS, Folio_Ticket, Cod_Barra, Nombre_Prod, Cantidad, Fk_sucursal, Fk_SucursalDestino, Total_VentaG, Pc, TipoDeMov, Fecha_venta, Estatus, Sistema, AgregadoPor, ID_H_O_D) VALUES ";
$queryValue = [];
$values = [];

// Crear la parte de los valores de la consulta
for ($i = 0; $i < $contador; $i++) {
    // Validar valores por índice
    $fkSucursalDestino = $_POST["Fk_SucursalDestino"][$i] ?? null;
    $totalVenta = $_POST["TotalVenta"][$i] ?? null;

    if (
        !empty($_POST["CodBarras"][$i]) &&
        !empty($_POST["NombreDelProducto"][$i]) &&
        isset($_POST["Cantidad"][$i]) &&
        !empty($_POST["Fk_sucursal"][$i]) &&
        !empty($fkSucursalDestino) &&
        isset($totalVenta) &&
        !empty($_POST["Pc"][$i]) &&
        !empty($_POST["TipoDeMov"][$i]) &&
        !empty($_POST["FechaVenta"][$i]) &&
        !empty($_POST["Estatus"][$i]) &&
        !empty($_POST["Sistema"][$i]) &&
        !empty($_POST["AgregadoPor"][$i]) &&
        !empty($_POST["ID_H_O_D"][$i]) &&
        !empty($_POST["Folio_Ticket"][$i])
    ) {
        $ProContador++;
        $queryValue[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Agregar valores al array de valores
        $values = array_merge($values, [
            null, // `ID_Prod_POS` es generado automáticamente
            $_POST["Folio_Ticket"][$i],
            $_POST["CodBarras"][$i],
            $_POST["NombreDelProducto"][$i],
            $_POST["Cantidad"][$i],
            $_POST["Fk_sucursal"][$i],
            $fkSucursalDestino,
            $totalVenta,
            $_POST["Pc"][$i],
            $_POST["TipoDeMov"][$i],
            $_POST["FechaVenta"][$i],
            $_POST["Estatus"][$i],
            $_POST["Sistema"][$i],
            $_POST["AgregadoPor"][$i],
            $_POST["ID_H_O_D"][$i]
        ]);
    } else {
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field][$i] ?? null)) {
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

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

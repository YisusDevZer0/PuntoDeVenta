<?php
include_once "db_connect.php";

// Verificar si hay datos recibidos adecuados
$required_fields = [
    "CodBarras",
    "NombreDelProducto",
    "Cantidad",
    "Fk_sucursal",
    "Fk_SucursalDestino",
   
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

// Obtener el valor de TotalVenta solo una vez
$totalVenta = $_POST["TotalVenta"][0]; // Asumiendo que TotalVenta es el mismo para todos los productos

// Preparar la consulta SQL
$query = "INSERT INTO TraspasosYNotasC (Folio_Ticket, Cod_Barra, Nombre_Prod, Cantidad, Fk_sucursal, Fk_SucursalDestino, Total_VentaG, Pc, TipoDeMov, Fecha_venta, Estatus, Sistema, AgregadoPor, ID_H_O_D) VALUES ";
$queryValue = [];
$values = [];

// Crear la parte de los valores de la consulta
for ($i = 0; $i < $contador; $i++) {
    // Validar campos individuales
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field][$i])) {
            $missing_fields[] = $field;
        }
    }
    
    if (count($missing_fields) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos en el producto con índice ' . $i . ': ' . implode(', ', $missing_fields), 'received_data' => $_POST]);
        exit;
    }

    // Agregar consulta y valores (sin TotalVenta, ya que lo asignamos previamente)
    $ProContador++;
    $queryValue[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $values = array_merge($values, [
        $_POST["Folio_Ticket"][$i],
        $_POST["CodBarras"][$i],
        $_POST["NombreDelProducto"][$i],
        $_POST["Cantidad"][$i],
        $_POST["Fk_sucursal"][$i],
        $_POST["Fk_SucursalDestino"][$i],
        $totalVenta, // Usamos el valor de TotalVenta preestablecido
        $_POST["Pc"][$i],
        $_POST["TipoDeMov"][$i],
        $_POST["FechaVenta"][$i],
        $_POST["Estatus"][$i],
        $_POST["Sistema"][$i],
        $_POST["AgregadoPor"][$i],
        $_POST["ID_H_O_D"][$i]
    ]);
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
$types = str_repeat('s', count($values));
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

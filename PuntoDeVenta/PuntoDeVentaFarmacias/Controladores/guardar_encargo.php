<?php
include_once "ControladorUsuario.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_paciente = $_POST['nombre_paciente'];
    $fecha_encargo = $_POST['fecha_encargo'];
    $abono_parcial = $_POST['abono_parcial'] ?: 0.00;

    // Calcular costo total
    $total_costo = 0.00;
    foreach ($_POST['medicamentos'] as $medicamento) {
        $medicamento_id = $medicamento['id'];
        $cantidad = $medicamento['cantidad'];

        $query = "SELECT Precio_C FROM Productos_POS WHERE ID_Prod_POS = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("i", $medicamento_id);
            $stmt->execute();
            $stmt->bind_result($precio_c);
            $stmt->fetch();
            $total_costo += $precio_c * $cantidad;
            $stmt->close();
        }
    }

    // Insertar encargo
    $query = "INSERT INTO encargos (nombre_paciente, fecha_encargo, costo_total, abono_parcial) VALUES (?, ?, ?, ?)";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("ssdd", $nombre_paciente, $fecha_encargo, $total_costo, $abono_parcial);
        $stmt->execute();
        $encargo_id = $stmt->insert_id;
        $stmt->close();
    } else {
        echo "Error: " . $mysqli->error;
        exit;
    }

    // Insertar detalles de medicamentos
    $query = "INSERT INTO detalles_encargos (encargo_id, medicamento_id, cantidad) VALUES (?, ?, ?)";
    if ($stmt = $mysqli->prepare($query)) {
        foreach ($_POST['medicamentos'] as $medicamento) {
            $medicamento_id = $medicamento['id'];
            $cantidad = $medicamento['cantidad'];
            $stmt->bind_param("iii", $encargo_id, $medicamento_id, $cantidad);
            $stmt->execute();
        }
        $stmt->close();
    } else {
        echo "Error: " . $mysqli->error;
        exit;
    }

    $mysqli->close();
    echo "Encargo registrado correctamente.";
}
?>

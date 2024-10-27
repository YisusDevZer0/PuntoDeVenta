<?php
include "db_connect.php";

if(isset($_POST['folio_ticket'])) {
    $folio_ticket = $_POST['folio_ticket'];
    
    // Consulta para eliminar el ticket en la tabla Ventas_POS
    $sql = "DELETE FROM Ventas_POS WHERE Folio_Ticket = '$folio_ticket'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Ticket eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el ticket: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Folio de ticket no recibido']);
}
?>

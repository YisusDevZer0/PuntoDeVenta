<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id = null;

$fk_caja = isset($_POST['id']) ? $_POST['id'] : null;

if (!$fk_caja) {
    echo "Faltan parámetros necesarios.";
    exit;
}

// CONSULTA 1: Obtener la información completa del corte, excepto los servicios
$sql = "SELECT ID_Caja, Fk_Caja, Empleado, Sucursal, Turno, TotalTickets, 
               Valor_Total_Caja, TotalEfectivo, TotalTarjeta, TotalCreditos, 
               TotalTransferencias, Hora_Cierre, Sistema, ID_H_O_D, Comentarios 
        FROM Cortes_Cajas_POS 
        WHERE Fk_Caja = '$fk_caja'";
$query = $conn->query($sql);

$datosCorte = null;

if ($query && $query->num_rows > 0) {
    $datosCorte = $query->fetch_object();
} else {
    echo '<p class="alert alert-danger">No se encontraron datos para mostrar.</p>';
    exit;
}

// CONSULTA 2: Obtener solo los servicios
$sqlServicios = "SELECT Servicios FROM Cortes_Cajas_POS WHERE Fk_Caja = '$fk_caja'";
$queryServicios = $conn->query($sqlServicios);

$servicios = [];

if ($queryServicios && $queryServicios->num_rows > 0) {
    $resultServicios = $queryServicios->fetch_object();
    
    // Mostrar el contenido del campo Servicios para verificar si es un JSON válido
    echo "<pre>Contenido de Servicios antes de decodificar: " . htmlspecialchars($resultServicios->Servicios) . "</pre>";

    // Intentar decodificar el campo Servicios
    if (!empty($resultServicios->Servicios)) {
        $servicios = json_decode($resultServicios->Servicios, true);

        // Mostrar un mensaje detallado si falla la decodificación
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error al decodificar JSON: " . json_last_error_msg();
            exit;
        }
    }
} else {
    echo '<p class="alert alert-danger">No se encontraron servicios para mostrar.</p>';
}
?>

<!-- El resto del código para mostrar los datos -->
<?php if ($datosCorte): ?>
    <!-- Aquí va el código para mostrar los datos generales y servicios -->
<?php endif; ?>

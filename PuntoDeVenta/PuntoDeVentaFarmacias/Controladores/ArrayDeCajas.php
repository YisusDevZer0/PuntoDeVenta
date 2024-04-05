<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
        Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
        Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
        FROM Cajas
        INNER JOIN Sucursales ON Cajas.Sucursal = Sucursales.ID_Sucursal
        WHERE Cajas.Sucursal = ?"; // Se cambió la condición WHERE para utilizar Fk_Sucursal

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Obtener el valor de Fk_Sucursal
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Vincular parámetro
$stmt->bind_param("s", $fk_sucursal);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Definir el estilo y el texto según el valor de asignación
    $asignacion_estilo = '';
    $asignacion_leyenda = '';
    switch ($fila["Asignacion"]) {
        case 0:
            $asignacion_estilo = 'background-color: #1e90ff; color: white;'; // Azul oscuro
            $asignacion_leyenda = 'Bloqueada';
            break;
        case 1:
            $asignacion_estilo = 'background-color: green; color: white;'; // Verde
            $asignacion_leyenda = 'Activa';
            break;
        case 2:
            $asignacion_estilo = 'background-color: orange; color: white;'; // Naranja
            $asignacion_leyenda = 'Inactiva';
            break;
        default:
            $asignacion_estilo = 'background-color: black; color: white;'; // Estilo de reserva
            $asignacion_leyenda = 'Sin definir';
            break;
    }

    // Definir el estilo y el texto según el valor de estatus
    $estatus_estilo = '';
    $estatus_leyenda = '';
    switch ($fila["Estatus"]) {
        case 'Abierta':
            $estatus_estilo = 'background-color: #008080; color: white;'; // Verde marino
            $estatus_leyenda = 'Abierta';
            break;
        case 'Cerrada':
            $estatus_estilo = 'background-color: red; color: white;'; // Rojo
            $estatus_leyenda = 'Cerrada';
            break;
        default:
            $estatus_estilo = ''; // No se aplica estilo
            $estatus_leyenda = $fila["Estatus"];
            break;
    }

    // Verificar si la caja está cerrada
    if ($fila["Estatus"] == 'Cerrada') {
        // Si está cerrada, no mostrar ningún botón
        $desactivar_caja = '';
        $reactivar_caja = '';
    } else {
        // Si no está cerrada, mostrar el botón correspondiente según el estado de asignación
        if ($fila["Asignacion"] == 1) {
            // Si está activa, mostrar el botón de desactivar
            $desactivar_caja = '<td><a data-id="' . $fila["ID_Caja"] . '" class="btn btn-danger btn-sm btn-desactiva " style="background-color: #ff3131 !important;"><i class="fa-solid fa-lock"></i></a></td>';
            $reactivar_caja = '';
        } else {
            // Si está inactiva o sin definir, mostrar el botón de activar
            $desactivar_caja = '';
            $reactivar_caja = '<td><a data-id="' . $fila["ID_Caja"] . '" class="btn btn-primary btn-sm btn-reactiva " style="background-color: #0172b6 !important;"><i class="fa-solid fa-lock-open"></i></a></td>';
        }
    }

    // Construir el array de datos
    $data[] = [
        "IdCaja" => $fila["ID_Caja"],
        "Empleado" => $fila["Empleado"],
        "Cantidad_Fondo" => $fila["Cantidad_Fondo"],
        "Fecha_Apertura" => $fila["Fecha_Apertura"],
        "Estatus" => "<div style=\"$estatus_estilo; padding: 5px; border-radius: 5px;\">$estatus_leyenda</div>",
        "Turno" => $fila["Turno"],
        "Asignacion" => "<div style=\"$asignacion_estilo; padding: 5px; border-radius: 5px;\">$asignacion_leyenda</div>",
        "ValorTotalCaja" => $fila["Valor_Total_Caja"],
        "DesactivarCaja" => $desactivar_caja,
        "ReactivarCaja" => $reactivar_caja,
        "RegistrarGasto" => '<td><a data-id="' . $fila["ID_Caja"] . '" class="btn btn-primary btn-sm btn-registraGasto " style="background-color: #0172b6 !important;"><i class="fa-solid fa-comment-dollar"></i></a></td>',
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>

<?php
// Controlador para gestión de sorteos (CRUD)
include_once 'db_connect.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    case 'listar':
        listarSorteos($conn);
        break;
    case 'obtener':
        obtenerSorteo($conn);
        break;
    case 'crear':
        crearSorteo($conn);
        break;
    case 'actualizar':
        actualizarSorteo($conn);
        break;
    case 'toggleActivo':
        toggleActivo($conn);
        break;
    case 'eliminar':
        eliminarSorteo($conn);
        break;
    case 'obtenerSucursales':
        obtenerSucursalesSorteo($conn);
        break;
    case 'listarParticipaciones':
        listarParticipaciones($conn);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

function listarSorteos($conn) {
    $sql = "SELECT s.*, 
            (SELECT COUNT(*) FROM Sorteo_Participaciones sp WHERE sp.Fk_Sorteo = s.ID_Sorteo AND sp.Participa = 1) as TotalParticipaciones,
            CASE 
                WHEN s.Activo = 1 AND CURDATE() BETWEEN s.Fecha_Inicio AND s.Fecha_Fin THEN 'Activo'
                WHEN s.Activo = 1 AND CURDATE() < s.Fecha_Inicio THEN 'Programado'
                WHEN s.Activo = 0 THEN 'Inactivo'
                ELSE 'Finalizado'
            END as Estado
            FROM Sorteos s 
            ORDER BY s.ID_Sorteo DESC";
    
    $result = mysqli_query($conn, $sql);
    $data = [];
    $c = 0;

    while ($fila = $result->fetch_assoc()) {
        // Obtener sucursales asociadas
        $sqlSuc = "SELECT ss.Fk_Sucursal, su.Nombre_Sucursal 
                   FROM Sorteo_Sucursales ss 
                   LEFT JOIN Sucursales su ON su.ID_Sucursal = ss.Fk_Sucursal 
                   WHERE ss.Fk_Sorteo = " . intval($fila['ID_Sorteo']);
        $resSuc = mysqli_query($conn, $sqlSuc);
        $sucursales = [];
        while ($suc = mysqli_fetch_assoc($resSuc)) {
            $sucursales[] = $suc;
        }
        
        $data[$c] = $fila;
        $data[$c]['Sucursales'] = $sucursales;
        $c++;
    }

    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];

    echo json_encode($results);
}

function obtenerSorteo($conn) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Sorteos WHERE ID_Sorteo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sorteo = $result->fetch_assoc();

    if ($sorteo) {
        // Obtener sucursales
        $sqlSuc = "SELECT Fk_Sucursal FROM Sorteo_Sucursales WHERE Fk_Sorteo = ?";
        $stmtSuc = $conn->prepare($sqlSuc);
        $stmtSuc->bind_param("i", $id);
        $stmtSuc->execute();
        $resSuc = $stmtSuc->get_result();
        $sucursales = [];
        while ($suc = $resSuc->fetch_assoc()) {
            $sucursales[] = $suc['Fk_Sucursal'];
        }
        $sorteo['sucursales_ids'] = $sucursales;
        $stmtSuc->close();
        
        echo json_encode(['status' => 'success', 'data' => $sorteo]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sorteo no encontrado']);
    }
    $stmt->close();
}

function crearSorteo($conn) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? '';
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];
    $aplicaTodas = isset($_POST['aplica_todas']) ? intval($_POST['aplica_todas']) : 1;
    $prefijoFolio = $_POST['prefijo_folio'] ?? null;
    $folioInicio = isset($_POST['folio_inicio']) ? intval($_POST['folio_inicio']) : 1;
    $creadoPor = $_POST['creado_por'];

    $sql = "INSERT INTO Sorteos (Nombre_Sorteo, Descripcion, Fecha_Inicio, Fecha_Fin, Activo, Aplica_Todas_Sucursales, Prefijo_Folio, Folio_Inicio, CreadoPor) 
            VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisss", $nombre, $descripcion, $fechaInicio, $fechaFin, $aplicaTodas, $prefijoFolio, $folioInicio, $creadoPor);

    if ($stmt->execute()) {
        $sorteoId = $stmt->insert_id;
        
        // Si no aplica a todas, guardar las sucursales seleccionadas
        if ($aplicaTodas == 0 && isset($_POST['sucursales'])) {
            $sucursales = json_decode($_POST['sucursales'], true);
            if (is_array($sucursales)) {
                $stmtSuc = $conn->prepare("INSERT INTO Sorteo_Sucursales (Fk_Sorteo, Fk_Sucursal) VALUES (?, ?)");
                foreach ($sucursales as $sucId) {
                    $sucId = intval($sucId);
                    $stmtSuc->bind_param("ii", $sorteoId, $sucId);
                    $stmtSuc->execute();
                }
                $stmtSuc->close();
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Sorteo creado correctamente', 'id' => $sorteoId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear sorteo: ' . $conn->error]);
    }
    $stmt->close();
}

function actualizarSorteo($conn) {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? '';
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];
    $aplicaTodas = isset($_POST['aplica_todas']) ? intval($_POST['aplica_todas']) : 1;
    $prefijoFolio = $_POST['prefijo_folio'] ?? null;
    $folioInicio = isset($_POST['folio_inicio']) ? intval($_POST['folio_inicio']) : 1;
    $actualizadoPor = $_POST['actualizado_por'];

    $sql = "UPDATE Sorteos SET Nombre_Sorteo=?, Descripcion=?, Fecha_Inicio=?, Fecha_Fin=?, 
            Aplica_Todas_Sucursales=?, Prefijo_Folio=?, Folio_Inicio=?, ActualizadoPor=? 
            WHERE ID_Sorteo=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisisi", $nombre, $descripcion, $fechaInicio, $fechaFin, $aplicaTodas, $prefijoFolio, $folioInicio, $actualizadoPor, $id);

    if ($stmt->execute()) {
        // Actualizar sucursales
        $conn->query("DELETE FROM Sorteo_Sucursales WHERE Fk_Sorteo = " . $id);
        if ($aplicaTodas == 0 && isset($_POST['sucursales'])) {
            $sucursales = json_decode($_POST['sucursales'], true);
            if (is_array($sucursales)) {
                $stmtSuc = $conn->prepare("INSERT INTO Sorteo_Sucursales (Fk_Sorteo, Fk_Sucursal) VALUES (?, ?)");
                foreach ($sucursales as $sucId) {
                    $sucId = intval($sucId);
                    $stmtSuc->bind_param("ii", $id, $sucId);
                    $stmtSuc->execute();
                }
                $stmtSuc->close();
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Sorteo actualizado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    $stmt->close();
}

function toggleActivo($conn) {
    $id = intval($_POST['id']);
    $sql = "UPDATE Sorteos SET Activo = IF(Activo=1, 0, 1) WHERE ID_Sorteo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
    $stmt->close();
}

function eliminarSorteo($conn) {
    $id = intval($_POST['id']);
    
    // Verificar si tiene participaciones
    $check = $conn->query("SELECT COUNT(*) as total FROM Sorteo_Participaciones WHERE Fk_Sorteo = " . $id);
    $row = $check->fetch_assoc();
    
    if ($row['total'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar un sorteo con participaciones registradas. Desactívelo en su lugar.']);
        return;
    }
    
    $conn->query("DELETE FROM Sorteo_Sucursales WHERE Fk_Sorteo = " . $id);
    $stmt = $conn->prepare("DELETE FROM Sorteos WHERE ID_Sorteo = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Sorteo eliminado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
    $stmt->close();
}

function obtenerSucursalesSorteo($conn) {
    $id = intval($_GET['id']);
    $sql = "SELECT ss.Fk_Sucursal, su.Nombre_Sucursal 
            FROM Sorteo_Sucursales ss 
            LEFT JOIN Sucursales su ON su.ID_Sucursal = ss.Fk_Sucursal 
            WHERE ss.Fk_Sorteo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sucursales = [];
    while ($row = $result->fetch_assoc()) {
        $sucursales[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $sucursales]);
    $stmt->close();
}

function listarParticipaciones($conn) {
    $sorteoId = isset($_GET['sorteo_id']) ? intval($_GET['sorteo_id']) : 0;
    
    $where = $sorteoId > 0 ? "WHERE sp.Fk_Sorteo = " . $sorteoId : "";
    
    $sql = "SELECT sp.*, s.Nombre_Sorteo, su.Nombre_Sucursal
            FROM Sorteo_Participaciones sp
            LEFT JOIN Sorteos s ON s.ID_Sorteo = sp.Fk_Sorteo
            LEFT JOIN Sucursales su ON su.ID_Sucursal = sp.Fk_Sucursal
            $where
            ORDER BY sp.ID_Participacion DESC";
    
    $result = mysqli_query($conn, $sql);
    $data = [];
    $c = 0;
    while ($fila = $result->fetch_assoc()) {
        $data[$c] = $fila;
        $c++;
    }

    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];

    echo json_encode($results);
}

mysqli_close($conn);
?>

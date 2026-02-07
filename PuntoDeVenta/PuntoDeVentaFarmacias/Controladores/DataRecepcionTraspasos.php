<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    include_once __DIR__ . '/db_connect.php';
    include_once __DIR__ . '/ControladorUsuario.php';

    if (!isset($conn) || !$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }

    if (!isset($row) || !isset($row['Fk_Sucursal'])) {
        throw new Exception('Usuario no autenticado o sin sucursal asignada');
    }

    $sucursal = (int) ($row['Fk_Sucursal'] ?? $row['Fk_sucursal'] ?? 0);
    if ($sucursal <= 0) {
        throw new Exception('Sucursal inválida');
    }

    // Farmacias: solo traspasos destinados a esta sucursal
    $codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';

    $sql = "SELECT 
        tyc.TraspaNotID,
        tyc.Folio_Ticket,
        tyc.Cod_Barra,
        tyc.Nombre_Prod,
        tyc.Cantidad,
        tyc.Fk_sucursal,
        tyc.Fk_SucursalDestino,
        tyc.Total_VentaG,
        tyc.Pc,
        tyc.TipoDeMov,
        tyc.Fecha_venta,
        tyc.Estatus,
        tyc.Sistema,
        tyc.AgregadoPor,
        tyc.AgregadoEl,
        tyc.ID_H_O_D,
        suc_origen.Nombre_Sucursal AS Sucursal_Origen,
        suc_destino.Nombre_Sucursal AS Sucursal_Destino
    FROM TraspasosYNotasC tyc
    LEFT JOIN Sucursales suc_origen ON tyc.Fk_sucursal = suc_origen.ID_Sucursal
    LEFT JOIN Sucursales suc_destino ON tyc.Fk_SucursalDestino = suc_destino.ID_Sucursal
    WHERE tyc.Fk_SucursalDestino = ? AND tyc.Estatus = 'Generado'";

    $params = [$sucursal];
    $types = "i";

    if ($codigo !== '') {
        $sql .= " AND (tyc.Cod_Barra LIKE ? OR tyc.Nombre_Prod LIKE ?)";
        $like = "%{$codigo}%";
        $params[] = $like;
        $params[] = $like;
        $types .= "ss";
    }

    $sql .= " ORDER BY tyc.TraspaNotID DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar consulta: ' . $conn->error);
    }

    $data = [];
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $id = (int) $fila['TraspaNotID'];

        $fecha_venta = '';
        if (!empty($fila['Fecha_venta']) && $fila['Fecha_venta'] !== '0000-00-00') {
            try {
                $fecha_venta = date('d/m/Y', strtotime($fila['Fecha_venta']));
            } catch (Exception $e) {
                $fecha_venta = $fila['Fecha_venta'];
            }
        }
        if (empty($fecha_venta)) $fecha_venta = '-';

        $fecha_agregado = '';
        if (!empty($fila['AgregadoEl']) && $fila['AgregadoEl'] !== '0000-00-00 00:00:00') {
            try {
                $fecha_agregado = date('d/m/Y H:i', strtotime($fila['AgregadoEl']));
            } catch (Exception $e) {
                $fecha_agregado = $fila['AgregadoEl'];
            }
        }
        if (empty($fecha_agregado)) $fecha_agregado = '-';

        $clean = function($val) {
            if ($val === null || $val === '') return '-';
            return htmlspecialchars((string)$val);
        };

        $data[] = [
            'TraspaNotID' => $id,
            'Folio_Ticket' => $clean($fila['Folio_Ticket'] ?? null),
            'Cod_Barra' => $clean($fila['Cod_Barra'] ?? null),
            'Nombre_Prod' => $clean($fila['Nombre_Prod'] ?? null),
            'Cantidad' => isset($fila['Cantidad']) && $fila['Cantidad'] !== null ? (int) $fila['Cantidad'] : 0,
            'Fk_sucursal' => isset($fila['Fk_sucursal']) && $fila['Fk_sucursal'] !== null ? (int) $fila['Fk_sucursal'] : 0,
            'Fk_SucursalDestino' => isset($fila['Fk_SucursalDestino']) && $fila['Fk_SucursalDestino'] !== null ? (int) $fila['Fk_SucursalDestino'] : 0,
            'Total_VentaG' => isset($fila['Total_VentaG']) && $fila['Total_VentaG'] !== null ? (float) $fila['Total_VentaG'] : 0,
            'Pc' => isset($fila['Pc']) && $fila['Pc'] !== null ? (float) $fila['Pc'] : 0,
            'TipoDeMov' => $clean($fila['TipoDeMov'] ?? null),
            'Fecha_venta' => $fecha_venta,
            'Estatus' => $clean($fila['Estatus'] ?? null),
            'Sistema' => $clean($fila['Sistema'] ?? null),
            'AgregadoPor' => $clean($fila['AgregadoPor'] ?? null),
            'AgregadoEl' => $fecha_agregado,
            'ID_H_O_D' => isset($fila['ID_H_O_D']) && $fila['ID_H_O_D'] !== null ? (int) $fila['ID_H_O_D'] : 0,
            'Sucursal_Origen' => $clean($fila['Sucursal_Origen'] ?? null),
            'Sucursal_Destino' => $clean($fila['Sucursal_Destino'] ?? null),
            'Recibir' => "<button type='button' class='btn btn-sm btn-primary btn-recibir-traspaso' data-id='{$id}' title='Recibir y registrar lote/caducidad'><i class='fa-solid fa-truck-ramp-box'></i> Recibir</button>"
        ];
    }
    $stmt->close();

    echo json_encode([
        'sEcho' => 1,
        'iTotalRecords' => count($data),
        'iTotalDisplayRecords' => count($data),
        'aaData' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sEcho' => 1,
        'iTotalRecords' => 0,
        'iTotalDisplayRecords' => 0,
        'aaData' => [],
        'error' => $e->getMessage()
    ]);
    error_log('Error en DataRecepcionTraspasos.php (Farmacias): ' . $e->getMessage());
}

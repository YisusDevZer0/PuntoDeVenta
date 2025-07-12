<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    if ($accion === 'buscar_producto') {
        header('Content-Type: application/json');
        $fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
        $q = isset($_POST['q']) ? $_POST['q'] : '';
        $sql = "SELECT ID_Prod_POS, Nombre_Prod FROM Stock_POS WHERE Fk_sucursal = ? AND Nombre_Prod LIKE ? AND Existencias_R > 0 ORDER BY Nombre_Prod ASC LIMIT 20";
        $stmt = $conn->prepare($sql);
        $like = "%$q%";
        $stmt->bind_param("ss", $fk_sucursal, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        $productos = [];
        while($prod = $res->fetch_assoc()) {
            $productos[] = $prod;
        }
        error_log('Busqueda producto: '.json_encode(['q'=>$q,'sql'=>$sql,'result'=>$productos]));
        echo json_encode(['status' => 'ok', 'data' => $productos]);
        exit;
    }
    if ($accion === 'productos') {
        $fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
        $sql = "SELECT ID_Prod_POS, Nombre_Prod FROM Stock_POS WHERE Fk_sucursal = ? AND Existencias_R > 0 ORDER BY Nombre_Prod ASC LIMIT 100";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $fk_sucursal);
        $stmt->execute();
        $res = $stmt->get_result();
        $productos = [];
        while($prod = $res->fetch_assoc()) {
            $productos[] = $prod;
        }
        echo json_encode(['status' => 'ok', 'data' => $productos]);
        exit;
    }
    if ($accion === 'crear') {
        $producto_id = intval($_POST['producto_id']);
        if ($producto_id <= 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Producto inválido']);
            exit;
        }
        $cantidad = floatval($_POST['cantidad']);
        $observaciones = mysqli_real_escape_string($conn, $_POST['observaciones']);
        $usuario_id = $row['Id_PvUser'];
        $sucursal_id = $row['Fk_Sucursal'];
        $sql = "INSERT INTO pedidos (usuario_id, sucursal_id, estado, observaciones, tipo_origen) VALUES (?, ?, 'pendiente', ?, 'farmacia')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $usuario_id, $sucursal_id, $observaciones);
        if ($stmt->execute()) {
            $pedido_id = $conn->insert_id;
            $sql_det = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, observaciones) VALUES (?, ?, ?, '')";
            $stmt2 = $conn->prepare($sql_det);
            $stmt2->bind_param("iid", $pedido_id, $producto_id, $cantidad);
            $stmt2->execute();
            echo json_encode(['status' => 'ok', 'msg' => 'Pedido creado']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al crear pedido']);
        }
        exit;
    }
    if ($accion === 'listar') {
        $sucursal_id = $row['Fk_Sucursal'];
        $sql = "SELECT p.id, d.cantidad, s.Nombre_Prod FROM pedidos p LEFT JOIN pedido_detalles d ON p.id = d.pedido_id LEFT JOIN Stock_POS s ON d.producto_id = s.ID_Prod_POS WHERE p.sucursal_id = ? AND d.producto_id IS NOT NULL ORDER BY p.id DESC LIMIT 50";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = [];
        while($rowp = $res->fetch_assoc()) {
            $data[] = $rowp;
        }
        echo json_encode(['status' => 'ok', 'data' => $data]);
        exit;
    }
    if ($accion === 'detalle') {
        $id = intval($_POST['id']);
        $sql = "SELECT p.*, d.cantidad, s.Nombre_Prod FROM pedidos p LEFT JOIN pedido_detalles d ON p.id = d.pedido_id LEFT JOIN Stock_POS s ON d.producto_id = s.ID_Prod_POS WHERE p.id = ? AND d.producto_id IS NOT NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rowd = $res->fetch_assoc();
        echo json_encode(['status' => 'ok', 'data' => $rowd]);
        exit;
    }
} 
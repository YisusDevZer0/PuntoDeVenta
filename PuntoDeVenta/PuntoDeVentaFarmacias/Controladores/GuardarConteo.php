<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once "db_connect.php";

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['VentasPos'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Obtener y validar datos
$codigos = $_POST['CodBarra'] ?? [];
$nombres = $_POST['NombreProd'] ?? [];
$existenciasR = $_POST['Existencias_R'] ?? [];
$stockFisico = $_POST['StockFisico'] ?? [];
$sucursal = $_POST['Sucursal'][0] ?? null;
$agregadoPor = $_POST['Agrego'][0] ?? null;
$enPausa = isset($_POST['EnPausa']) ? (int)$_POST['EnPausa'] : 0;
$id_conteo = isset($_POST['id_conteo']) ? (int)$_POST['id_conteo'] : null;

// Validar que todos los arrays tengan la misma longitud
if (count($codigos) !== count($nombres) || 
    count($codigos) !== count($existenciasR) || 
    count($codigos) !== count($stockFisico)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o corruptos']);
    exit;
}

// Validar datos de sucursal y usuario
if (!$sucursal || !$agregadoPor) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos de sucursal o usuario']);
    exit;
}

// Si no está en pausa, validar que todos los campos de stock físico estén llenos
if (!$enPausa) {
    foreach ($stockFisico as $stock) {
        if ($stock === '' || $stock === null) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos de Stock Físico deben estar llenos para finalizar el conteo']);
            exit;
        }
    }
}

try {
    // Iniciar transacción
    $conn->begin_transaction();

    if ($id_conteo) {
        // Continuar un conteo existente
        // Verificar que el conteo existe y pertenece al usuario
        $sql_verificar = "SELECT id, EnPausa, AgregadoEl FROM ConteosDiarios 
                          WHERE id = ? AND AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1 
                          LIMIT 1";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("iss", $id_conteo, $agregadoPor, $sucursal);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        
        if ($result_verificar->num_rows === 0) {
            throw new Exception("Conteo no encontrado o no tienes permisos para continuarlo");
        }
        $row_conteo = $result_verificar->fetch_assoc();
        $fechaConteo = $row_conteo['AgregadoEl'];
        
        $productos_contados = 0;
        $ids_conteo = $_POST['IdConteo'] ?? [];
        for ($i = 0; $i < count($ids_conteo); $i++) {
            $id_registro = $ids_conteo[$i];
            $codigo = $_POST['CodBarra'][$i] ?? '';
            $nombre = $_POST['NombreProd'][$i] ?? '';
            $existenciaR = $_POST['Existencias_R'][$i] ?? '';
            $stock = isset($_POST['StockFisico'][$i]) ? $_POST['StockFisico'][$i] : null;
            if ($id_registro) {
                // UPDATE directo por ID
                $valorStock = ($stock === '' || $stock === null) ? null : $stock;
                if ($valorStock === null) {
                    // Solo actualizar EnPausa, Nombre_Producto y Existencias_R, NO ExistenciaFisica
                    $sql_update = "UPDATE ConteosDiarios SET Nombre_Producto = ?, Existencias_R = ?, EnPausa = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("siii", $nombre, $existenciaR, $enPausa, $id_registro);
                } else {
                    // Actualizar todo
                    $sql_update = "UPDATE ConteosDiarios SET ExistenciaFisica = ?, Nombre_Producto = ?, Existencias_R = ?, EnPausa = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("isiii", $valorStock, $nombre, $existenciaR, $enPausa, $id_registro);
                }
                if (!$stmt_update->execute()) {
                    throw new Exception("Error al actualizar el producto: " . $stmt_update->error);
                }
                $productos_contados++;
            } else {
                // INSERT (caso raro)
                $stmt_insertar = $conn->prepare("
                    INSERT INTO ConteosDiarios (
                        Cod_Barra, 
                        Nombre_Producto, 
                        Fk_sucursal, 
                        Existencias_R, 
                        ExistenciaFisica, 
                        AgregadoPor, 
                        AgregadoEl, 
                        EnPausa
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $valorStock = ($stock === '' || $stock === null) ? null : $stock;
                $stmt_insertar->bind_param(
                    "sssdisis",
                    $codigo,
                    $nombre,
                    $sucursal,
                    $existenciaR,
                    $valorStock,
                    $agregadoPor,
                    $fechaConteo,
                    $enPausa
                );
                if (!$stmt_insertar->execute()) {
                    throw new Exception("Error al guardar el producto: " . $stmt_insertar->error);
                }
                $productos_contados++;
            }
        }
        
        // Si se está finalizando el conteo, actualizar el inventario
        if (!$enPausa && $productos_contados > 0) {
            for ($i = 0; $i < count($codigos); $i++) {
                if ($stockFisico[$i] !== '' && $stockFisico[$i] !== null) {
                    $sql_inventario = "UPDATE Stock_POS 
                                      SET Existencias_R = ? 
                                      WHERE Cod_Barra = ? AND Fk_sucursal = ?";
                    $stmt_inventario = $conn->prepare($sql_inventario);
                    $stmt_inventario->bind_param("iss", $stockFisico[$i], $codigos[$i], $sucursal);
                    $stmt_inventario->execute();
                    $stmt_inventario->close();
                }
            }
        }
        
    } else {
        if ($enPausa) {
            // GUARDAR/ACTUALIZAR EN PAUSA
            for ($i = 0; $i < count($codigos); $i++) {
                $stmt = $conn->prepare("
                    REPLACE INTO ConteosDiarios_Pausados (
                        Folio_Ingreso, Cod_Barra, Nombre_Producto, Fk_sucursal, Existencias_R, ExistenciaFisica, AgregadoPor, AgregadoEl, EnPausa
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, NOW(), 1
                    )
                ");
                $folio = isset($_POST['IdConteo'][$i]) ? $_POST['IdConteo'][$i] : null;
                $valorStock = ($stockFisico[$i] === '' || $stockFisico[$i] === null) ? null : $stockFisico[$i];
                $stmt->bind_param(
                    "issiiis",
                    $folio,
                    $codigos[$i],
                    $nombres[$i],
                    $sucursal,
                    $existenciasR[$i],
                    $valorStock,
                    $agregadoPor
                );
                if (!$stmt->execute()) {
                    throw new Exception("Error al guardar en pausa: " . $stmt->error);
                }
            }
        } else {
            // GUARDAR DIRECTO EN FINAL
            // Antes de guardar, si existen productos en pausa, finalízalos todos
            $sql_finaliza_pausa = "UPDATE ConteosDiarios_Pausados SET EnPausa = 0 WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1";
            $stmt_finaliza_pausa = $conn->prepare($sql_finaliza_pausa);
            $stmt_finaliza_pausa->bind_param("ss", $agregadoPor, $sucursal);
            $stmt_finaliza_pausa->execute();
            $stmt_finaliza_pausa->close();
            // Luego guarda los productos nuevos (si aplica)
            for ($i = 0; $i < count($codigos); $i++) {
                $stmt = $conn->prepare("
                    INSERT INTO ConteosDiarios (
                        Cod_Barra, Nombre_Producto, Fk_sucursal, Existencias_R, ExistenciaFisica, AgregadoPor, AgregadoEl, EnPausa
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, NOW(), 0
                    )
                    ON DUPLICATE KEY UPDATE
                        ExistenciaFisica = VALUES(ExistenciaFisica),
                        Existencias_R = VALUES(Existencias_R),
                        Nombre_Producto = VALUES(Nombre_Producto),
                        AgregadoEl = VALUES(AgregadoEl),
                        EnPausa = 0
                ");
                $valorStock = ($stockFisico[$i] === '' || $stockFisico[$i] === null) ? null : $stockFisico[$i];
                $stmt->bind_param(
                    "ssiiis",
                    $codigos[$i],
                    $nombres[$i],
                    $sucursal,
                    $existenciasR[$i],
                    $valorStock,
                    $agregadoPor
                );
                if (!$stmt->execute()) {
                    throw new Exception("Error al guardar definitivo: " . $stmt->error);
                }
                $stmt->close();
            }
        }
    }

    // Confirmar transacción
    $conn->commit();

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => $enPausa ? 
            'Conteo guardado y pausado correctamente. Podrás completar los campos vacíos más tarde.' : 
            'Conteo guardado correctamente',
        'registros' => count($codigos)
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    // Registrar el error en el log
    error_log("Error en GuardarConteo.php: " . $e->getMessage());
    
    // Devolver respuesta de error
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el conteo: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
if (isset($stmt_verificar)) $stmt_verificar->close();
$conn->close();
?> 
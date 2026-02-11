<?php
/**
 * Función para descontar lotes automáticamente de las ventas
 * Utiliza método FEFO (First Expired First Out) - primero los que caducan primero
 */

header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";

/**
 * Descuenta lotes según la cantidad vendida usando FEFO
 * @param int $id_prod_pos ID del producto
 * @param string $cod_barra Código de barras
 * @param int $sucursal ID de sucursal
 * @param int $cantidad_vendida Cantidad a descontar
 * @param string $folio_ticket Folio de ticket de venta
 * @param string $usuario Usuario que realiza la venta
 * @return array Resultado de la operación
 */
function descontarLotesVenta($id_prod_pos, $cod_barra, $sucursal, $cantidad_vendida, $folio_ticket, $usuario) {
    global $conn;
    
    // Log inicial
    error_log("DEBUG descontarLotesVenta: ID={$id_prod_pos}, Código={$cod_barra}, Sucursal={$sucursal}, Cantidad={$cantidad_vendida}");
    
    try {
        // Verificar conexión
        if (!isset($conn) || !$conn) {
            throw new Exception('No hay conexión a la base de datos');
        }
        
        // Iniciar transacción (compatible con versiones antiguas de PHP)
        if (function_exists('mysqli_begin_transaction')) {
            mysqli_begin_transaction($conn);
        } else {
            mysqli_query($conn, "START TRANSACTION");
        }
        
        $cantidad_restante = $cantidad_vendida;
        $lotes_utilizados = [];
        
        // Obtener solo lotes con registro válido (lote y fecha de caducidad reales).
        // No usar filas con Lote NaN/vacío ni Fecha_Caducidad 0000-00-00.
        // Si no hay ningún registro válido, no descontar y no crear contenido vacío.
        $sql_lotes = "SELECT 
                        ID_Historial,
                        Lote,
                        Fecha_Caducidad,
                        Existencias,
                        DATEDIFF(Fecha_Caducidad, CURDATE()) as Dias_restantes
                      FROM Historial_Lotes
                      WHERE ID_Prod_POS = ? 
                        AND Fk_sucursal = ?
                        AND Existencias > 0
                        AND Lote IS NOT NULL AND TRIM(Lote) != '' 
                        AND LOWER(TRIM(Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote')
                        AND Fecha_Caducidad IS NOT NULL 
                        AND Fecha_Caducidad > '1900-01-01' 
                        AND Fecha_Caducidad != '0000-00-00'
                      ORDER BY 
                        CASE 
                          WHEN DATEDIFF(Fecha_Caducidad, CURDATE()) < 0 THEN 0  -- Vencidos primero
                          WHEN DATEDIFF(Fecha_Caducidad, CURDATE()) <= 15 THEN 1  -- Próximos a vencer
                          ELSE 2  -- Los demás
                        END,
                        Fecha_Caducidad ASC";
        
        $stmt_lotes = mysqli_prepare($conn, $sql_lotes);
        if (!$stmt_lotes) {
            throw new Exception('Error al preparar consulta de lotes: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_lotes, "ii", $id_prod_pos, $sucursal);
        mysqli_stmt_execute($stmt_lotes);
        $result_lotes = mysqli_stmt_get_result($stmt_lotes);
        
        $num_lotes = mysqli_num_rows($result_lotes);
        error_log("DEBUG: Se encontraron {$num_lotes} lotes disponibles para producto {$cod_barra}");
        
        if ($num_lotes == 0) {
            // No hay registros en Historial_Lotes: no descontar ahí, el stock ya se descontó en el trigger
            mysqli_commit($conn);
            error_log("DEBUG: Sin lotes en Historial_Lotes para {$cod_barra}; proceso normal de stock aplicado por trigger.");
            return [
                'success' => true,
                'solo_stock' => true,
                'mensaje' => 'Sin registros en historial de lotes; descuento aplicado solo al stock.',
                'lotes_utilizados' => [],
                'cantidad_descontada' => 0
            ];
        }
        
        // Descontar de cada lote hasta cubrir la cantidad vendida
        while (($lote = mysqli_fetch_assoc($result_lotes)) && $cantidad_restante > 0) {
            error_log("DEBUG: Descontando del lote {$lote['Lote']}, existencias actuales: {$lote['Existencias']}, cantidad a descontar: " . min($cantidad_restante, $lote['Existencias']));
            $cantidad_a_descontar = min($cantidad_restante, $lote['Existencias']);
            $nueva_existencia = $lote['Existencias'] - $cantidad_a_descontar;
            
            // Actualizar existencias del lote
            $sql_update = "UPDATE Historial_Lotes 
                          SET Existencias = ?,
                              Usuario_Modifico = ?,
                              Fecha_Registro = NOW()
                          WHERE ID_Historial = ?";
            
            $stmt_update = mysqli_prepare($conn, $sql_update);
            if (!$stmt_update) {
                throw new Exception('Error al preparar actualización: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt_update, "isi", $nueva_existencia, $usuario, $lote['ID_Historial']);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);
            
            // Registrar descuento en tabla de auditoría
            $sql_descuento = "INSERT INTO Lotes_Descuentos_Ventas (
                                ID_Venta, Folio_Ticket, ID_Prod_POS, Cod_Barra, Fk_sucursal,
                                Lote, Fecha_Caducidad, Cantidad_Descontada,
                                Existencias_Antes, Existencias_Despues, Usuario_Venta, Tipo_Descuento
                              ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'automatico')";
            
            $stmt_descuento = mysqli_prepare($conn, $sql_descuento);
            if (!$stmt_descuento) {
                throw new Exception('Error al preparar descuento: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param(
                $stmt_descuento,
                "sississssss",
                $folio_ticket,
                $id_prod_pos,
                $cod_barra,
                $sucursal,
                $lote['Lote'],
                $lote['Fecha_Caducidad'],
                $cantidad_a_descontar,
                $lote['Existencias'],
                $nueva_existencia,
                $usuario
            );
            mysqli_stmt_execute($stmt_descuento);
            mysqli_stmt_close($stmt_descuento);
            
            $lotes_utilizados[] = [
                'lote' => $lote['Lote'],
                'fecha_caducidad' => $lote['Fecha_Caducidad'],
                'cantidad' => $cantidad_a_descontar,
                'dias_restantes' => $lote['Dias_restantes']
            ];
            
            $cantidad_restante -= $cantidad_a_descontar;
        }
        
        mysqli_stmt_close($stmt_lotes);
        
        // Si no se pudo cubrir toda la cantidad, hacer rollback
        if ($cantidad_restante > 0) {
            throw new Exception("No hay suficiente stock en lotes. Faltan $cantidad_restante unidades.");
        }
        
        // NO actualizar Stock_POS aquí: el trigger RestarExistenciasDespuesInsert ya restó del stock.
        // Solo actualizamos Historial_Lotes cuando hay registros válidos.
        
        mysqli_commit($conn);
        
        error_log("DEBUG: Descuento exitoso. Lotes utilizados: " . count($lotes_utilizados) . ", Cantidad descontada: {$cantidad_vendida}");
        
        return [
            'success' => true,
            'lotes_utilizados' => $lotes_utilizados,
            'cantidad_descontada' => $cantidad_vendida
        ];
        
    } catch (Exception $e) {
        if (isset($conn) && $conn) {
            mysqli_rollback($conn);
        }
        error_log("ERROR descontarLotesVenta: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Si se llama directamente desde AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prod_pos = isset($_POST['id_prod_pos']) ? (int)$_POST['id_prod_pos'] : 0;
    $cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';
    $sucursal = isset($_POST['sucursal']) ? (int)$_POST['sucursal'] : 0;
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $folio_ticket = isset($_POST['folio_ticket']) ? trim($_POST['folio_ticket']) : '';
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : 'Sistema';
    
    if ($id_prod_pos <= 0 || $sucursal <= 0 || $cantidad <= 0) {
        echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
        exit;
    }
    
    $resultado = descontarLotesVenta($id_prod_pos, $cod_barra, $sucursal, $cantidad, $folio_ticket, $usuario);
    echo json_encode($resultado);
}
?>
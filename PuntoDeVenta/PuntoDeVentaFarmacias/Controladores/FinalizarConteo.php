<?php
include_once "../Consultas/db_connect.php";
include_once "ControladorUsuario.php";

// Verificar que el usuario esté autenticado
if (!isset($row['Nombre_Apellidos'])) {
    echo json_encode(['success' => false, 'message' => 'No se ha iniciado sesión']);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['id_conteo']) || !isset($_POST['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id_conteo = intval($_POST['id_conteo']);
$accion = $_POST['accion'];
$usuario = $row['Nombre_Apellidos'];
$sucursal = $row['Fk_Sucursal'];

// Verificar que el conteo pertenece al usuario y está en pausa
$sql_verificar = "SELECT id, AgregadoPor, Fk_sucursal, EnPausa, Productos_Contados, Total_Productos 
                  FROM ConteosDiarios 
                  WHERE id = ? AND AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("iss", $id_conteo, $usuario, $sucursal);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Conteo no encontrado o no tienes permisos para finalizarlo']);
    exit;
}

$conteo = $result_verificar->fetch_assoc();

if ($accion === 'finalizar') {
    // Finalizar el conteo (marcar como completado)
    $sql_finalizar = "UPDATE ConteosDiarios 
                      SET EnPausa = 0, 
                          Fecha_Finalizacion = NOW(),
                          Estado = 'Completado'
                      WHERE id = ?";
    
    $stmt_finalizar = $conn->prepare($sql_finalizar);
    $stmt_finalizar->bind_param("i", $id_conteo);
    
    if ($stmt_finalizar->execute()) {
        // Si el conteo tiene productos contados, actualizar el inventario
        if ($conteo['Productos_Contados'] > 0) {
            // Obtener los productos contados del conteo
            $sql_productos = "SELECT Cod_Barra, Stock_Fisico 
                             FROM DetalleConteosDiarios 
                             WHERE Fk_ConteoDiario = ?";
            $stmt_productos = $conn->prepare($sql_productos);
            $stmt_productos->bind_param("i", $id_conteo);
            $stmt_productos->execute();
            $result_productos = $stmt_productos->get_result();
            
            // Actualizar el inventario con los conteos realizados
            while ($producto = $result_productos->fetch_assoc()) {
                $sql_actualizar = "UPDATE Stock_POS 
                                  SET Existencias_R = ? 
                                  WHERE Cod_Barra = ? AND Fk_sucursal = ?";
                $stmt_actualizar = $conn->prepare($sql_actualizar);
                $stmt_actualizar->bind_param("iss", $producto['Stock_Fisico'], $producto['Cod_Barra'], $sucursal);
                $stmt_actualizar->execute();
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Conteo finalizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al finalizar el conteo']);
    }
    
    $stmt_finalizar->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

$stmt_verificar->close();
?> 
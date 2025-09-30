<?php
// Configurar headers para JSON y manejo de errores
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla, solo en logs

include_once 'db_connect.php';

// Función para logging de errores del servidor
function logServerError($context, $message, $data = []) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] ERROR en $context: $message";
    
    if (!empty($data)) {
        $logMessage .= " | Datos: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    $logMessage .= " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $logMessage .= " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . PHP_EOL;
    
    // Escribir al log de errores de PHP
    error_log($logMessage);
    
    // Opcional: escribir a un archivo de log personalizado
    // file_put_contents('logs/inventario_errors.log', $logMessage, FILE_APPEND | LOCK_EX);
}

// Función para validar datos de entrada
function validarDatosInventario($data, $index) {
    $errores = [];
    $camposRequeridos = [
        'IdBasedatos' => 'ID del producto',
        'CodBarras' => 'Código de barras',
        'NombreDelProducto' => 'Nombre del producto',
        'Fk_sucursal' => 'Sucursal',
        'PrecioVenta' => 'Precio de venta',
        'PrecioCompra' => 'Precio de compra',
        'Contabilizado' => 'Cantidad contabilizada',
        'StockActual' => 'Stock actual',
        'Diferencia' => 'Diferencia',
        'Sistema' => 'Sistema',
        'AgregoElVendedor' => 'Vendedor',
        'ID_H_O_D' => 'ID de la empresa',
        'FechaInv' => 'Fecha de inventario',
        'Tipodeajusteaplicado' => 'Tipo de ajuste',
        'AnaquelSeleccionado' => 'Anaquel',
        'RepisaSeleccionada' => 'Repisa'
    ];
    
    foreach ($camposRequeridos as $campo => $descripcion) {
        if (!isset($data[$campo][$index]) || $data[$campo][$index] === '') {
            $errores[] = "Campo '$descripcion' es requerido en el registro " . ($index + 1);
        }
    }
    
    // Validaciones específicas
    if (isset($data['PrecioVenta'][$index]) && (!is_numeric($data['PrecioVenta'][$index]) || $data['PrecioVenta'][$index] < 0)) {
        $errores[] = "Precio de venta debe ser un número positivo en el registro " . ($index + 1);
    }
    
    if (isset($data['PrecioCompra'][$index]) && (!is_numeric($data['PrecioCompra'][$index]) || $data['PrecioCompra'][$index] < 0)) {
        $errores[] = "Precio de compra debe ser un número positivo en el registro " . ($index + 1);
    }
    
    if (isset($data['Contabilizado'][$index]) && (!is_numeric($data['Contabilizado'][$index]) || $data['Contabilizado'][$index] < 0)) {
        $errores[] = "Cantidad contabilizada debe ser un número positivo en el registro " . ($index + 1);
    }
    
    if (isset($data['StockActual'][$index]) && (!is_numeric($data['StockActual'][$index]) || $data['StockActual'][$index] < 0)) {
        $errores[] = "Stock actual debe ser un número positivo en el registro " . ($index + 1);
    }
    
    return $errores;
}

// Función para sanitizar datos
function sanitizarDatos($data) {
    if (is_array($data)) {
        return array_map('sanitizarDatos', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

try {
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Solo se aceptan peticiones POST.');
    }
    
    // Verificar conexión a la base de datos
    if (!$conn || mysqli_connect_error()) {
        throw new Exception('Error de conexión a la base de datos: ' . mysqli_connect_error());
    }
    
    // Verificar si $_POST["IdBasedatos"] está definido y es un arreglo
    if (!isset($_POST["IdBasedatos"]) || !is_array($_POST["IdBasedatos"])) {
        throw new Exception('No se recibieron datos de productos para procesar. Verifica que el formulario esté enviando los datos correctamente.');
    }
    
    $contador = count($_POST["IdBasedatos"]);
    logServerError('inicio_proceso', "Iniciando procesamiento de $contador registros", [
        'campos_recibidos' => array_keys($_POST),
        'tamaño_datos' => array_map('count', $_POST)
    ]);
    
    // Validar que todos los arrays tengan el mismo tamaño
    $camposArray = ['IdBasedatos', 'CodBarras', 'NombreDelProducto', 'Fk_sucursal', 'PrecioVenta', 'PrecioCompra', 'Contabilizado', 'StockActual', 'Diferencia', 'Sistema', 'AgregoElVendedor', 'ID_H_O_D', 'FechaInv', 'Tipodeajusteaplicado', 'AnaquelSeleccionado', 'RepisaSeleccionada'];
    
    foreach ($camposArray as $campo) {
        if (!isset($_POST[$campo]) || !is_array($_POST[$campo])) {
            throw new Exception("Campo requerido '$campo' no está presente o no es un array válido.");
        }
        if (count($_POST[$campo]) !== $contador) {
            throw new Exception("El campo '$campo' tiene " . count($_POST[$campo]) . " elementos, pero se esperaban $contador.");
        }
    }
    
    $ProContador = 0;
    $erroresValidacion = [];
    $query = "INSERT INTO InventariosStocks_Conteos (`ID_Prod_POS`, `Cod_Barra`, `Nombre_Prod`, `Fk_sucursal`, `Precio_Venta`, `Precio_C`, `Contabilizado`, `StockEnMomento`, `Diferencia`, `Sistema`, `AgregadoPor`, `ID_H_O_D`, `FechaInventario`, `Tipo_Ajuste`, `Anaquel`, `Repisa`) VALUES ";
    
    $placeholders = [];
    $values = [];
    $valueTypes = '';
    
    // Sanitizar todos los datos POST
    $_POST = sanitizarDatos($_POST);
    
    for ($i = 0; $i < $contador; $i++) {
        // Validar datos del registro actual
        $erroresRegistro = validarDatosInventario($_POST, $i);
        
        if (!empty($erroresRegistro)) {
            $erroresValidacion = array_merge($erroresValidacion, $erroresRegistro);
            continue; // Saltar este registro si tiene errores
        }
        
        // Verificar si al menos uno de los campos principales no está vacío
        if (!empty($_POST["IdBasedatos"][$i]) || !empty($_POST["CodBarras"][$i]) || !empty($_POST["NombreDelProducto"][$i])) {
            $ProContador++;
            $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Agregar valores en el orden correcto
            $values[] = $_POST["IdBasedatos"][$i];
            $values[] = $_POST["CodBarras"][$i];
            $values[] = $_POST["NombreDelProducto"][$i];
            $values[] = $_POST["Fk_sucursal"][$i];
            $values[] = floatval($_POST["PrecioVenta"][$i]);
            $values[] = floatval($_POST["PrecioCompra"][$i]);
            $values[] = intval($_POST["Contabilizado"][$i]);
            $values[] = intval($_POST["StockActual"][$i]);
            $values[] = intval($_POST["Diferencia"][$i]);
            $values[] = $_POST["Sistema"][$i];
            $values[] = $_POST["AgregoElVendedor"][$i];
            $values[] = $_POST["ID_H_O_D"][$i];
            $values[] = $_POST["FechaInv"][$i];
            $values[] = $_POST["Tipodeajusteaplicado"][$i];
            $values[] = $_POST["AnaquelSeleccionado"][$i];
            $values[] = $_POST["RepisaSeleccionada"][$i];
            
            $valueTypes .= 'ssssddiiissssss'; // Tipos correctos: s=string, d=double, i=integer
        }
    }
    
    // Si hay errores de validación, devolverlos
    if (!empty($erroresValidacion)) {
        logServerError('validacion_fallida', 'Errores de validación encontrados', $erroresValidacion);
        
        $response = [
            'status' => 'error',
            'message' => 'Se encontraron errores de validación en los datos enviados',
            'errors' => $erroresValidacion,
            'code' => 'VALIDATION_ERROR',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $response = [];
    
    if ($ProContador == 0) {
        throw new Exception('No se encontraron registros válidos para procesar. Verifica que los datos estén completos y sean válidos.');
    }
    
    // Construir y ejecutar la consulta
    $query .= implode(', ', $placeholders);
    
    logServerError('ejecutando_consulta', "Ejecutando inserción de $ProContador registros", [
        'query_length' => strlen($query),
        'values_count' => count($values)
    ]);
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta SQL: ' . mysqli_error($conn));
    }
    
    // Enlazar parámetros
    if (!mysqli_stmt_bind_param($stmt, $valueTypes, ...$values)) {
        mysqli_stmt_close($stmt);
        throw new Exception('Error al enlazar parámetros de la consulta: ' . mysqli_stmt_error($stmt));
    }
    
    // Ejecutar consulta
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new Exception('Error al ejecutar la consulta de inserción: ' . $error);
    }
    
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    logServerError('operacion_exitosa', "Inventario registrado correctamente", [
        'registros_procesados' => $ProContador,
        'filas_afectadas' => $affectedRows
    ]);
    
    $response = [
        'status' => 'success',
        'message' => "Se registraron exitosamente $ProContador productos en el inventario.",
        'data' => [
            'registros_procesados' => $ProContador,
            'filas_afectadas' => $affectedRows,
            'timestamp' => date('Y-m-d H:i:s')
        ],
        'code' => 'SUCCESS'
    ];
    
} catch (Exception $e) {
    logServerError('excepcion', $e->getMessage(), [
        'archivo' => $e->getFile(),
        'linea' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $response = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'code' => 'EXCEPTION',
        'timestamp' => date('Y-m-d H:i:s')
    ];
} catch (Error $e) {
    logServerError('error_fatal', $e->getMessage(), [
        'archivo' => $e->getFile(),
        'linea' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $response = [
        'status' => 'error',
        'message' => 'Error interno del servidor. Contacta al administrador.',
        'code' => 'FATAL_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ];
} finally {
    // Cerrar conexión si está abierta
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}

// Enviar respuesta JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

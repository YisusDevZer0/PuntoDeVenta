<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include("db_connect.php");
include_once "ControladorUsuario.php";

// Verificar que se haya subido un archivo
if (!isset($_FILES['archivoInventario']) || $_FILES['archivoInventario']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al subir el archivo. Por favor, verifique que el archivo sea válido.'
    ]);
    exit;
}

// Verificar que se haya proporcionado la fecha
if (!isset($_POST['fechaInventario']) || empty($_POST['fechaInventario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, seleccione la fecha del inventario.'
    ]);
    exit;
}

$fechaInventario = $_POST['fechaInventario'];
$archivo = $_FILES['archivoInventario'];

// Validar extensión del archivo
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
    echo json_encode([
        'success' => false,
        'message' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv).'
    ]);
    exit;
}

try {
    // Cargar PhpSpreadsheet
    // Intentar diferentes rutas posibles para el autoload
    $autoloadPaths = [
        __DIR__ . '/../../../../vendor/autoload.php',
        __DIR__ . '/../../../vendor/autoload.php',
        __DIR__ . '/../../vendor/autoload.php',
        __DIR__ . '/../../../PuntoDeVenta/vendor/autoload.php'
    ];
    
    $autoloadFound = false;
    foreach ($autoloadPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $autoloadFound = true;
            break;
        }
    }
    
    if (!$autoloadFound) {
        throw new Exception('No se encontró el archivo autoload.php de Composer. Asegúrese de que PhpSpreadsheet esté instalado.');
    }
    
    use PhpOffice\PhpSpreadsheet\IOFactory;
    
    // Leer el archivo Excel
    $spreadsheet = IOFactory::load($archivo['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    if (count($rows) < 2) {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo está vacío o no tiene datos válidos.'
        ]);
        exit;
    }
    
    // Obtener encabezados (primera fila)
    $headers = array_map('trim', $rows[0]);
    
    // Mapear índices de columnas esperadas
    $codBarraIndex = array_search('Cod_Barra', $headers);
    $nombreProdIndex = array_search('Nombre_Prod', $headers);
    $existenciasIndex = array_search('Existencias_R', $headers);
    
    if ($codBarraIndex === false || $nombreProdIndex === false || $existenciasIndex === false) {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo no tiene el formato esperado. Debe contener las columnas: Cod_Barra, Nombre_Prod, Existencias_R'
        ]);
        exit;
    }
    
    // Procesar datos del inventario
    $inventario = [];
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        if (empty($row[$codBarraIndex])) continue;
        
        $codBarra = trim($row[$codBarraIndex]);
        $nombreProd = isset($row[$nombreProdIndex]) ? trim($row[$nombreProdIndex]) : '';
        $existencias = isset($row[$existenciasIndex]) ? floatval($row[$existenciasIndex]) : 0;
        
        if (!empty($codBarra)) {
            $inventario[$codBarra] = [
                'cod_barra' => $codBarra,
                'nombre_prod' => $nombreProd,
                'existencias' => $existencias
            ];
        }
    }
    
    // Obtener sucursal del usuario (desde ControladorUsuario.php)
    if (!isset($row) || !isset($row['Fk_Sucursal'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo determinar la sucursal del usuario. Por favor, inicie sesión nuevamente.'
        ]);
        exit;
    }
    
    $fk_sucursal = intval($row['Fk_Sucursal']);
    if ($fk_sucursal <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'La sucursal del usuario no es válida.'
        ]);
        exit;
    }
    
    // Obtener ventas del día del inventario
    $sqlVentas = "SELECT 
                    Cod_Barra,
                    Nombre_Prod,
                    SUM(Cantidad_Venta) as cantidad_vendida,
                    COUNT(DISTINCT Folio_Ticket) as total_tickets,
                    SUM(Total_Venta) as total_venta
                  FROM Ventas_POSV2
                  WHERE Fk_sucursal = ? 
                    AND DATE(Fecha_venta) = ?
                    AND (Estatus IS NULL OR Estatus != 'Cancelada')
                  GROUP BY Cod_Barra, Nombre_Prod";
    
    $stmtVentas = $conn->prepare($sqlVentas);
    if (!$stmtVentas) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta de ventas: ' . $conn->error
        ]);
        exit;
    }
    
    $stmtVentas->bind_param("is", $fk_sucursal, $fechaInventario);
    $stmtVentas->execute();
    $resultVentas = $stmtVentas->get_result();
    
    $ventas = [];
    while ($rowVenta = $resultVentas->fetch_assoc()) {
        $codBarra = trim($rowVenta['Cod_Barra']);
        $ventas[$codBarra] = [
            'cod_barra' => $codBarra,
            'nombre_prod' => $rowVenta['Nombre_Prod'],
            'cantidad_vendida' => floatval($rowVenta['cantidad_vendida']),
            'total_tickets' => intval($rowVenta['total_tickets']),
            'total_venta' => floatval($rowVenta['total_venta'])
        ];
    }
    $stmtVentas->close();
    
    // Comparar inventario con ventas
    $comparacion = [];
    $productosEnInventario = 0;
    $productosVendidos = 0;
    $productosConDiferencias = 0;
    $totalDiferencia = 0;
    
    // Productos en inventario
    foreach ($inventario as $codBarra => $prodInventario) {
        $productosEnInventario++;
        $cantidadVendida = isset($ventas[$codBarra]) ? $ventas[$codBarra]['cantidad_vendida'] : 0;
        $existenciasInventario = $prodInventario['existencias'];
        $diferencia = $existenciasInventario - $cantidadVendida;
        
        $comparacion[] = [
            'cod_barra' => $codBarra,
            'nombre_prod' => $prodInventario['nombre_prod'],
            'existencias_inventario' => $existenciasInventario,
            'cantidad_vendida' => $cantidadVendida,
            'diferencia' => $diferencia,
            'total_tickets' => isset($ventas[$codBarra]) ? $ventas[$codBarra]['total_tickets'] : 0,
            'total_venta' => isset($ventas[$codBarra]) ? $ventas[$codBarra]['total_venta'] : 0,
            'tiene_diferencia' => abs($diferencia) > 0.01
        ];
        
        if ($cantidadVendida > 0) {
            $productosVendidos++;
        }
        
        if (abs($diferencia) > 0.01) {
            $productosConDiferencias++;
            $totalDiferencia += abs($diferencia);
        }
    }
    
    // Productos vendidos que no están en inventario
    foreach ($ventas as $codBarra => $prodVenta) {
        if (!isset($inventario[$codBarra])) {
            $comparacion[] = [
                'cod_barra' => $codBarra,
                'nombre_prod' => $prodVenta['nombre_prod'],
                'existencias_inventario' => 0,
                'cantidad_vendida' => $prodVenta['cantidad_vendida'],
                'diferencia' => -$prodVenta['cantidad_vendida'],
                'total_tickets' => $prodVenta['total_tickets'],
                'total_venta' => $prodVenta['total_venta'],
                'tiene_diferencia' => true
            ];
            $productosConDiferencias++;
        }
    }
    
    // Ordenar por diferencia (mayores diferencias primero)
    usort($comparacion, function($a, $b) {
        return abs($b['diferencia']) <=> abs($a['diferencia']);
    });
    
    echo json_encode([
        'success' => true,
        'fecha_inventario' => $fechaInventario,
        'resumen' => [
            'productos_en_inventario' => $productosEnInventario,
            'productos_vendidos' => $productosVendidos,
            'productos_con_diferencias' => $productosConDiferencias,
            'total_diferencia' => $totalDiferencia
        ],
        'comparacion' => $comparacion
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el archivo: ' . $e->getMessage()
    ]);
}

<?php
// Archivo de prueba para verificar las consultas del dashboard
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Prueba de Consultas del Dashboard</h2>";

// Incluir el archivo de consultas
include_once "Controladores/ConsultaDashboard.php";

echo "<h3>Resultados de las consultas:</h3>";
echo "<ul>";
echo "<li><strong>Cajas abiertas:</strong> " . $CajasAbiertas . "</li>";
echo "<li><strong>Venta del día:</strong> MX$ " . $formattedTotal . "</li>";
echo "<li><strong>Venta del mes:</strong> MX$ " . $ventasMes . "</li>";
echo "<li><strong>Productos bajo stock:</strong> " . $productosBajoStock . "</li>";
echo "<li><strong>Productos sin stock:</strong> " . $productosSinStock . "</li>";
echo "<li><strong>Total productos:</strong> " . $totalProductos . "</li>";
echo "<li><strong>Traspasos pendientes:</strong> " . $traspasosPendientes . "</li>";
echo "<li><strong>Formas de pago hoy:</strong> " . count($ventasPorFormaPago) . " tipos</li>";
echo "</ul>";

echo "<h3>Productos más vendidos del mes:</h3>";
if (!empty($productosMasVendidos)) {
    echo "<ul>";
    foreach ($productosMasVendidos as $producto) {
        echo "<li>" . $producto['Nombre_Prod'] . " - Cantidad: " . $producto['Total_Vendido'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay datos disponibles</p>";
}

echo "<h3>Productos menos vendidos del mes:</h3>";
if (!empty($productosMenosVendidos)) {
    echo "<ul>";
    foreach ($productosMenosVendidos as $producto) {
        echo "<li>" . $producto['Nombre_Prod'] . " - Cantidad: " . $producto['Total_Vendido'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay datos disponibles</p>";
}

echo "<h3>Últimas ventas:</h3>";
if (!empty($ultimasVentas)) {
    echo "<ul>";
    foreach ($ultimasVentas as $venta) {
        echo "<li>Ticket: " . $venta['Folio_Ticket'] . " - " . $venta['Nombre_Prod'] . " - MX$ " . number_format($venta['Total_Venta'], 2) . " - " . $venta['Fecha_venta'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay datos disponibles</p>";
}

echo "<h3>Formas de pago del día:</h3>";
if (!empty($ventasPorFormaPago)) {
    echo "<ul>";
    foreach ($ventasPorFormaPago as $forma) {
        echo "<li>" . $forma['FormaDePago'] . " - Cantidad: " . $forma['Cantidad'] . " - Total: MX$ " . number_format($forma['Total'], 2) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay datos disponibles</p>";
}

echo "<h3>Verificación de tablas:</h3>";
try {
    include_once "db_connect.php";
    
    // Verificar si las tablas existen
    $tables = ['Ventas_POS', 'Stock_POS', 'Cajas', 'Traspasos_generados', 'Sucursales'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✓ Tabla '$table' existe</p>";
        } else {
            echo "<p>✗ Tabla '$table' NO existe</p>";
        }
    }
    
    // Verificar columnas específicas
    echo "<h4>Verificación de columnas en Ventas_POS:</h4>";
    $result = $conn->query("DESCRIBE Ventas_POS");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], ['Fecha_venta', 'Estatus', 'Pagos_tarjeta', 'Importe', 'Total_Venta'])) {
                echo "<p>✓ Columna '{$row['Field']}' existe</p>";
            }
        }
    }
    
    echo "<h4>Verificación de columnas en Stock_POS:</h4>";
    $result = $conn->query("DESCRIBE Stock_POS");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], ['Min_Existencia', 'Existencias_R', 'ID_Prod_POS'])) {
                echo "<p>✓ Columna '{$row['Field']}' existe</p>";
            }
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p>Error al verificar tablas: " . $e->getMessage() . "</p>";
}
?>

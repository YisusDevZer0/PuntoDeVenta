<?php
// Inicializar variables con valores por defecto
$CajasAbiertas = 0;
$formattedTotal = "0.00";
$ventasMes = "0.00";
$productosBajoStock = 0;
$traspasosPendientes = 0;
$productosMasVendidos = [];
$productosMenosVendidos = [];
$ultimasVentas = [];
$productosSinStock = 0;
$totalProductos = 0;
$ventasPorFormaPago = [];

try {
    include_once("db_connect.php"); // Abrir la conexión una sola vez
    
    // Verificar conexión
    if (!$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta para contar cajas abiertas
    $sqlCajas = "SELECT COUNT(*) AS CajasAbiertas FROM Cajas WHERE Estatus = 'Abierta' AND Sucursal != 4";
    $resultCajas = $conn->query($sqlCajas);
    
    if ($resultCajas && $resultCajas->num_rows > 0) {
        $cajasData = $resultCajas->fetch_assoc();
        $CajasAbiertas = $cajasData['CajasAbiertas'] ?? 0;
    } else {
        error_log("Error en consulta de cajas: " . $conn->error);
    }

    // Consulta para calcular total de ventas del día
    $sqlVentas = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta FROM Ventas_POS WHERE DATE(Fecha_venta) = CURDATE()";
    $resultVentas = $conn->query($sqlVentas);
    
    if ($resultVentas && $resultVentas->num_rows > 0) {
        $ventasData = $resultVentas->fetch_assoc();
        $formattedTotal = number_format($ventasData['Total_Venta'] ?? 0, 2, '.', ',');
    } else {
        error_log("Error en consulta de ventas del día: " . $conn->error);
    }

    // Consulta para ventas del mes
    $sqlVentasMes = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta_Mes FROM Ventas_POS WHERE MONTH(Fecha_venta) = MONTH(CURDATE()) AND YEAR(Fecha_venta) = YEAR(CURDATE())";
    $resultVentasMes = $conn->query($sqlVentasMes);
    
    if ($resultVentasMes && $resultVentasMes->num_rows > 0) {
        $ventasMesData = $resultVentasMes->fetch_assoc();
        $ventasMes = number_format($ventasMesData['Total_Venta_Mes'] ?? 0, 2, '.', ',');
    } else {
        error_log("Error en consulta de ventas del mes: " . $conn->error);
    }

    // Consulta para productos con bajo stock
    $sqlBajoStock = "SELECT COUNT(*) AS ProductosBajoStock FROM Stock_POS WHERE Min_Existencia >= Existencias_R AND Existencias_R > 0";
    $resultBajoStock = $conn->query($sqlBajoStock);
    
    if ($resultBajoStock && $resultBajoStock->num_rows > 0) {
        $bajoStockData = $resultBajoStock->fetch_assoc();
        $productosBajoStock = $bajoStockData['ProductosBajoStock'] ?? 0;
    } else {
        error_log("Error en consulta de bajo stock: " . $conn->error);
    }

    // Consulta para traspasos pendientes
    $sqlTraspasos = "SELECT COUNT(*) AS TraspasosPendientes FROM Traspasos_generados WHERE Estatus = 'Pendiente'";
    $resultTraspasos = $conn->query($sqlTraspasos);
    
    if ($resultTraspasos && $resultTraspasos->num_rows > 0) {
        $traspasosData = $resultTraspasos->fetch_assoc();
        $traspasosPendientes = $traspasosData['TraspasosPendientes'] ?? 0;
    } else {
        error_log("Error en consulta de traspasos: " . $conn->error);
    }

    // Consulta para productos más vendidos del mes
    $sqlMasVendidos = "SELECT v.Nombre_Prod, SUM(v.Cantidad_Venta) AS Total_Vendido 
                       FROM Ventas_POS v 
                       WHERE MONTH(v.Fecha_venta) = MONTH(CURDATE()) 
                       AND YEAR(v.Fecha_venta) = YEAR(CURDATE())
                       AND v.Estatus = 'Pagado'
                       AND v.Cantidad_Venta > 0
                       GROUP BY v.ID_Prod_POS, v.Nombre_Prod 
                       ORDER BY Total_Vendido DESC 
                       LIMIT 5";
    $resultMasVendidos = $conn->query($sqlMasVendidos);
    
    if ($resultMasVendidos && $resultMasVendidos->num_rows > 0) {
        while ($row = $resultMasVendidos->fetch_assoc()) {
            $productosMasVendidos[] = $row;
        }
    } else {
        error_log("Error en consulta de productos más vendidos: " . $conn->error);
    }

    // Consulta para productos menos vendidos del mes
    $sqlMenosVendidos = "SELECT v.Nombre_Prod, SUM(v.Cantidad_Venta) AS Total_Vendido 
                         FROM Ventas_POS v 
                         WHERE MONTH(v.Fecha_venta) = MONTH(CURDATE()) 
                         AND YEAR(v.Fecha_venta) = YEAR(CURDATE())
                         AND v.Estatus = 'Pagado'
                         AND v.Cantidad_Venta > 0
                         GROUP BY v.ID_Prod_POS, v.Nombre_Prod 
                         ORDER BY Total_Vendido ASC 
                         LIMIT 5";
    $resultMenosVendidos = $conn->query($sqlMenosVendidos);
    
    if ($resultMenosVendidos && $resultMenosVendidos->num_rows > 0) {
        while ($row = $resultMenosVendidos->fetch_assoc()) {
            $productosMenosVendidos[] = $row;
        }
    } else {
        error_log("Error en consulta de productos menos vendidos: " . $conn->error);
    }

    // Consulta para últimas ventas
    $sqlUltimasVentas = "SELECT v.Folio_Ticket, v.Nombre_Prod, (v.Importe + v.Pagos_tarjeta) AS Total_Venta, v.Fecha_venta, s.Nombre_Sucursal
                          FROM Ventas_POS v
                          LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
                          WHERE v.Estatus = 'Pagado'
                          ORDER BY v.Fecha_venta DESC, v.AgregadoEl DESC
                          LIMIT 10";
    $resultUltimasVentas = $conn->query($sqlUltimasVentas);
    
    if ($resultUltimasVentas && $resultUltimasVentas->num_rows > 0) {
        while ($row = $resultUltimasVentas->fetch_assoc()) {
            $ultimasVentas[] = $row;
        }
    } else {
        error_log("Error en consulta de últimas ventas: " . $conn->error);
    }

    // Consulta para productos sin stock
    $sqlSinStock = "SELECT COUNT(*) AS ProductosSinStock FROM Stock_POS WHERE Existencias_R = 0";
    $resultSinStock = $conn->query($sqlSinStock);
    
    if ($resultSinStock && $resultSinStock->num_rows > 0) {
        $sinStockData = $resultSinStock->fetch_assoc();
        $productosSinStock = $sinStockData['ProductosSinStock'] ?? 0;
    } else {
        error_log("Error en consulta de productos sin stock: " . $conn->error);
    }

    // Consulta para total de productos
    $sqlTotalProductos = "SELECT COUNT(DISTINCT ID_Prod_POS) AS TotalProductos FROM Stock_POS";
    $resultTotalProductos = $conn->query($sqlTotalProductos);
    
    if ($resultTotalProductos && $resultTotalProductos->num_rows > 0) {
        $totalProductosData = $resultTotalProductos->fetch_assoc();
        $totalProductos = $totalProductosData['TotalProductos'] ?? 0;
    } else {
        error_log("Error en consulta de total de productos: " . $conn->error);
    }

    // Consulta para ventas por forma de pago del día
    $sqlFormaPago = "SELECT FormaDePago, COUNT(*) AS Cantidad, SUM(Importe + Pagos_tarjeta) AS Total
                      FROM Ventas_POS 
                      WHERE DATE(Fecha_venta) = CURDATE() 
                      AND Estatus = 'Pagado'
                      GROUP BY FormaDePago
                      ORDER BY Total DESC";
    $resultFormaPago = $conn->query($sqlFormaPago);
    
    if ($resultFormaPago && $resultFormaPago->num_rows > 0) {
        while ($row = $resultFormaPago->fetch_assoc()) {
            $ventasPorFormaPago[] = $row;
        }
    } else {
        error_log("Error en consulta de formas de pago: " . $conn->error);
    }

    // Cerrar la conexión
    if (isset($conn)) {
        $conn->close();
    }

} catch (Exception $e) {
    // En caso de error, mantener los valores por defecto
    error_log("Error en ConsultaDashboard.php: " . $e->getMessage());
    
    // Cerrar la conexión si existe
    if (isset($conn)) {
        $conn->close();
    }
}
?>

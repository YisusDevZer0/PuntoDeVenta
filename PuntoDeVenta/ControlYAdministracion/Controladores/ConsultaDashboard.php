<?php
include_once("db_connect.php"); // Abrir la conexión una sola vez

// Consulta para contar cajas abiertas
$sqlCajas = "SELECT COUNT(*) AS CajasAbiertas FROM Cajas WHERE Estatus = 'Abierta' AND Sucursal != 4";
$resultCajas = $conn->query($sqlCajas);
$CajasAbiertas = 0; // Inicializamos la variable

if ($resultCajas && $resultCajas->num_rows > 0) {
    $cajasData = $resultCajas->fetch_assoc();
    $CajasAbiertas = $cajasData['CajasAbiertas'] ?? 0;
}

// Consulta para calcular total de ventas del día
$sqlVentas = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta FROM Ventas_POS WHERE DATE(Fecha_venta) = CURDATE()";
$resultVentas = $conn->query($sqlVentas);
$formattedTotal = "0.00"; // Valor predeterminado

if ($resultVentas && $resultVentas->num_rows > 0) {
    $ventasData = $resultVentas->fetch_assoc();
    $formattedTotal = number_format($ventasData['Total_Venta'], 2, '.', ',') ?? "0.00";
}

// Consulta para ventas del mes
$sqlVentasMes = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta_Mes FROM Ventas_POS WHERE MONTH(Fecha_venta) = MONTH(CURDATE()) AND YEAR(Fecha_venta) = YEAR(CURDATE())";
$resultVentasMes = $conn->query($sqlVentasMes);
$ventasMes = "0.00";

if ($resultVentasMes && $resultVentasMes->num_rows > 0) {
    $ventasMesData = $resultVentasMes->fetch_assoc();
    $ventasMes = number_format($ventasMesData['Total_Venta_Mes'], 2, '.', ',') ?? "0.00";
}

// Consulta para productos con bajo stock
$sqlBajoStock = "SELECT COUNT(*) AS ProductosBajoStock FROM Productos_POS WHERE Stock_Minimo >= Stock_Actual";
$resultBajoStock = $conn->query($sqlBajoStock);
$productosBajoStock = 0;

if ($resultBajoStock && $resultBajoStock->num_rows > 0) {
    $bajoStockData = $resultBajoStock->fetch_assoc();
    $productosBajoStock = $bajoStockData['ProductosBajoStock'] ?? 0;
}

// Consulta para traspasos pendientes
$sqlTraspasos = "SELECT COUNT(*) AS TraspasosPendientes FROM Traspasos WHERE Estatus = 'Pendiente'";
$resultTraspasos = $conn->query($sqlTraspasos);
$traspasosPendientes = 0;

if ($resultTraspasos && $resultTraspasos->num_rows > 0) {
    $traspasosData = $resultTraspasos->fetch_assoc();
    $traspasosPendientes = $traspasosData['TraspasosPendientes'] ?? 0;
}

// Consulta para productos más vendidos del mes
$sqlMasVendidos = "SELECT v.Nombre_Prod, SUM(v.Cantidad_Venta) AS Total_Vendido 
                   FROM Ventas_POS v 
                   WHERE MONTH(v.Fecha_venta) = MONTH(CURDATE()) 
                   AND YEAR(v.Fecha_venta) = YEAR(CURDATE())
                   AND v.Estatus = 'Pagado'
                   GROUP BY v.ID_Prod_POS, v.Nombre_Prod 
                   ORDER BY Total_Vendido DESC 
                   LIMIT 5";
$resultMasVendidos = $conn->query($sqlMasVendidos);
$productosMasVendidos = [];

if ($resultMasVendidos && $resultMasVendidos->num_rows > 0) {
    while ($row = $resultMasVendidos->fetch_assoc()) {
        $productosMasVendidos[] = $row;
    }
}

// Consulta para productos menos vendidos del mes
$sqlMenosVendidos = "SELECT v.Nombre_Prod, SUM(v.Cantidad_Venta) AS Total_Vendido 
                     FROM Ventas_POS v 
                     WHERE MONTH(v.Fecha_venta) = MONTH(CURDATE()) 
                     AND YEAR(v.Fecha_venta) = YEAR(CURDATE())
                     AND v.Estatus = 'Pagado'
                     GROUP BY v.ID_Prod_POS, v.Nombre_Prod 
                     ORDER BY Total_Vendido ASC 
                     LIMIT 5";
$resultMenosVendidos = $conn->query($sqlMenosVendidos);
$productosMenosVendidos = [];

if ($resultMenosVendidos && $resultMenosVendidos->num_rows > 0) {
    while ($row = $resultMenosVendidos->fetch_assoc()) {
        $productosMenosVendidos[] = $row;
    }
}

// Consulta para últimas ventas
$sqlUltimasVentas = "SELECT v.Folio_Ticket, v.Nombre_Prod, v.Total_Venta, v.Fecha_venta, s.Nombre_Sucursal
                      FROM Ventas_POS v
                      LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
                      WHERE v.Estatus = 'Pagado'
                      ORDER BY v.Fecha_venta DESC
                      LIMIT 10";
$resultUltimasVentas = $conn->query($sqlUltimasVentas);
$ultimasVentas = [];

if ($resultUltimasVentas && $resultUltimasVentas->num_rows > 0) {
    while ($row = $resultUltimasVentas->fetch_assoc()) {
        $ultimasVentas[] = $row;
    }
}

// Consulta para productos sin stock
$sqlSinStock = "SELECT COUNT(*) AS ProductosSinStock FROM Productos_POS WHERE Stock_Actual = 0";
$resultSinStock = $conn->query($sqlSinStock);
$productosSinStock = 0;

if ($resultSinStock && $resultSinStock->num_rows > 0) {
    $sinStockData = $resultSinStock->fetch_assoc();
    $productosSinStock = $sinStockData['ProductosSinStock'] ?? 0;
}

// Consulta para total de productos
$sqlTotalProductos = "SELECT COUNT(*) AS TotalProductos FROM Productos_POS";
$resultTotalProductos = $conn->query($sqlTotalProductos);
$totalProductos = 0;

if ($resultTotalProductos && $resultTotalProductos->num_rows > 0) {
    $totalProductosData = $resultTotalProductos->fetch_assoc();
    $totalProductos = $totalProductosData['TotalProductos'] ?? 0;
}

// Consulta para ventas por forma de pago del día
$sqlFormaPago = "SELECT FormaDePago, COUNT(*) AS Cantidad, SUM(Importe) AS Total
                  FROM Ventas_POS 
                  WHERE DATE(Fecha_venta) = CURDATE() 
                  AND Estatus = 'Pagado'
                  GROUP BY FormaDePago
                  ORDER BY Total DESC";
$resultFormaPago = $conn->query($sqlFormaPago);
$ventasPorFormaPago = [];

if ($resultFormaPago && $resultFormaPago->num_rows > 0) {
    while ($row = $resultFormaPago->fetch_assoc()) {
        $ventasPorFormaPago[] = $row;
    }
}

// Cerrar la conexión
$conn->close();
?>

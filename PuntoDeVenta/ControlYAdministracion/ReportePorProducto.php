<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas por Producto - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>

<body>
    <!-- Spinner End -->
    <?php include_once "Menu.php" ?>

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Table Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">
                        <i class="fas fa-chart-bar me-2"></i>
                        Reporte de Ventas por Producto - <?php echo $row['Licencia']?>
                    </h6>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">
                                <i class="fas fa-calendar me-1"></i> Fecha Inicio
                            </label>
                            <input type="date" id="fecha_inicio" class="form-control" 
                                   value="<?php echo date('Y-m-01'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">
                                <i class="fas fa-calendar me-1"></i> Fecha Fin
                            </label>
                            <input type="date" id="fecha_fin" class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="sucursal" class="form-label">
                                <i class="fas fa-store me-1"></i> Sucursal
                            </label>
                            <select id="sucursal" class="form-control">
                                <option value="">Todas las sucursales</option>
                                <?php
                                // Cargar sucursales dinámicamente
                                $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 1 ORDER BY Nombre_Sucursal";
                                $result_sucursales = $conn->query($sql_sucursales);
                                if ($result_sucursales) {
                                    while ($sucursal = $result_sucursales->fetch_assoc()) {
                                        echo "<option value='" . $sucursal['ID_Sucursal'] . "'>" . $sucursal['Nombre_Sucursal'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportarExcel()">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4" id="statsRow" style="display: none;">
                        <div class="col-md-3">
                            <div class="bg-primary text-white rounded p-3 text-center">
                                <h4 class="mb-0" id="totalProductos">0</h4>
                                <small>Productos Vendidos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-success text-white rounded p-3 text-center">
                                <h4 class="mb-0" id="totalVentas">$0</h4>
                                <small>Total Ventas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-info text-white rounded p-3 text-center">
                                <h4 class="mb-0" id="totalUnidades">0</h4>
                                <small>Unidades Vendidas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-warning text-white rounded p-3 text-center">
                                <h4 class="mb-0" id="promedioVenta">$0</h4>
                                <small>Promedio por Venta</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table id="tablaReporte" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID Producto</th>
                                    <th>Código de Barras</th>
                                    <th>Nombre del Producto</th>
                                    <th>Tipo</th>
                                    <th>Sucursal</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Compra</th>
                                    <th>Existencias</th>
                                    <th>Total Vendido</th>
                                    <th>Total Importe</th>
                                    <th>Total Venta</th>
                                    <th>Total Descuento</th>
                                    <th>Número Ventas</th>
                                    <th>Vendedor</th>
                                    <th>Primera Venta</th>
                                    <th>Última Venta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php 
    include "Modales/NuevoFondoDeCaja.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";?>
    
    <script src="js/ReportePorProducto.js"></script>
</body>
</html>
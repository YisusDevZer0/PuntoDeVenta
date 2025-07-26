<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/ConsultaDashboard.php";



?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Pantalla de inicio administrativa <?php echo $row['Licencia']?> </title>
    <!-- <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #87CEEB;
            overflow: hidden;
            height: 100vh;
         
            align-items: center;
            justify-content: center;
        }

        /* Animación de Olas */
        .olas {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="1" d="M0,64L60,85.3C120,107,240,149,360,149.3C480,149,600,107,720,96C840,85,960,107,1080,138.7C1200,171,1320,213,1380,234.7L1440,256V320H0Z"></path></svg>');
            background-size: cover;
            animation: moverOlas 4s linear infinite;
        }
        @keyframes moverOlas {
            0% { background-position: 0 0; }
            100% { background-position: 100px 0; }
        }

        /* Animación de Burbujas */
        .burbuja {
            position: absolute;
            bottom: 0;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            opacity: 0.7;
            animation: subirBurbujas 4s infinite ease-in-out;
        }
        @keyframes subirBurbujas {
            0% {
                transform: translateY(0);
                opacity: 0.7;
            }
            100% {
                transform: translateY(-100vh);
                opacity: 0;
            }
        }

        /* Animación de Pez Nadando */
        .pez {
            position: absolute;
            left: -100px;
            width: 50px;
            height: 30px;
            background: orange;
            border-radius: 50%;
            clip-path: polygon(0% 50%, 100% 0, 80% 50%, 100% 100%);
            animation: nadar 5s linear infinite;
        }
        @keyframes nadar {
            0% { left: -100px; }
            100% { left: 100vw; }
        }
    </style> -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
   
   <?php
   include "header.php";?>
   
   <style>
       /* Estilos personalizados para el dashboard */
       .dashboard-card {
           transition: transform 0.2s ease-in-out;
           border: none;
           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
       }
       
       .dashboard-card:hover {
           transform: translateY(-2px);
           box-shadow: 0 4px 8px rgba(0,0,0,0.15);
       }
       
       .alert-card {
           border-left: 4px solid;
       }
       
       .alert-card.warning {
           border-left-color: #ffc107;
       }
       
       .alert-card.danger {
           border-left-color: #dc3545;
       }
       
       .alert-card.info {
           border-left-color: #17a2b8;
       }
       
       .alert-card.primary {
           border-left-color: #007bff;
       }
       
       .table-hover tbody tr:hover {
           background-color: #f8f9fa;
       }
       
       .chart-container {
           position: relative;
           height: 300px;
       }
       
       .stats-number {
           font-size: 1.5rem;
           font-weight: bold;
       }
       
       .stats-label {
           font-size: 0.9rem;
           color: #6c757d;
       }
   </style>


<body>
<!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            let animacion = Math.floor(Math.random() * 3);
            let elemento;
            if (animacion === 0) {
                elemento = document.createElement("div");
                elemento.className = "olas";
                document.body.appendChild(elemento);
            } else if (animacion === 1) {
                for (let i = 0; i < 10; i++) {
                    let burbuja = document.createElement("div");
                    burbuja.className = "burbuja";
                    burbuja.style.left = Math.random() * 100 + "vw";
                    burbuja.style.animationDuration = (2 + Math.random() * 3) + "s";
                    burbuja.style.width = burbuja.style.height = (10 + Math.random() * 20) + "px";
                    document.body.appendChild(burbuja);
                }
            } else {
                elemento = document.createElement("div");
                elemento.className = "pez";
                document.body.appendChild(elemento);
            }
        });
    </script> -->
        <!-- Spinner End -->


        <!-- Sidebar Start -->
       <?php include_once "Menu.php" ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Venta del día</p>
                                <h6 class="mb-0 stats-number"><?php echo "MX$ " . $formattedTotal; ?></h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card">
                            <i class="fa fa-chart-area fa-3x text-success"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Venta del mes</p>
                                <h6 class="mb-0 stats-number"><?php echo "MX$ " . $ventasMes; ?></h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card">
                            <i class="fa fa-cash-register fa-3x text-warning"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Cajas abiertas</p>
                                <h6 class="mb-0 stats-number"><?php echo $CajasAbiertas; ?></h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card">
                            <i class="fa fa-box fa-3x text-info"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Total productos</p>
                                <h6 class="mb-0 stats-number"><?php echo $totalProductos; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->

            <!-- Alertas y Estado Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card alert-card warning">
                            <i class="fa fa-exclamation-triangle fa-3x text-warning"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Bajo stock</p>
                                <h6 class="mb-0 stats-number"><?php echo $productosBajoStock; ?> productos</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card alert-card danger">
                            <i class="fa fa-times-circle fa-3x text-danger"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Sin stock</p>
                                <h6 class="mb-0 stats-number"><?php echo $productosSinStock; ?> productos</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card alert-card info">
                            <i class="fa fa-truck fa-3x text-info"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Traspasos pendientes</p>
                                <h6 class="mb-0 stats-number"><?php echo $traspasosPendientes; ?></h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 dashboard-card alert-card primary">
                            <i class="fa fa-credit-card fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2 stats-label">Formas de pago hoy</p>
                                <h6 class="mb-0 stats-number"><?php echo count($ventasPorFormaPago); ?> tipos</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Alertas y Estado End -->


            <!-- Sales Chart Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Productos Más Vendidos del Mes</h6>
                                <a href="ReporteProductosMasVendidos">Ver Reporte</a>
                            </div>
                            <canvas id="vendidos"></canvas>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Ventas por Forma de Pago (Hoy)</h6>
                                <a href="ReporteFormaDePago">Ver Reporte</a>
                            </div>
                            <canvas id="forma-pago"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sales Chart End -->

            <!-- Últimas Ventas Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Últimas Ventas</h6>
                        <a href="VentasDelDia">Ver Todas</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">Ticket</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasVentas as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['Folio_Ticket']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['Nombre_Prod']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['Nombre_Sucursal']); ?></td>
                                    <td>MX$ <?php echo number_format($venta['Total_Venta'], 2); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($venta['Fecha_venta'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Últimas Ventas End -->

            <!-- Información Adicional Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Productos Menos Vendidos del Mes</h6>
                                <a href="ReporteProductosMasVendidos">Ver Reporte</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table text-start align-middle table-bordered table-hover mb-0">
                                    <thead>
                                        <tr class="text-dark">
                                            <th scope="col">Producto</th>
                                            <th scope="col">Cantidad Vendida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productosMenosVendidos as $producto): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($producto['Nombre_Prod']); ?></td>
                                            <td><?php echo number_format($producto['Total_Vendido']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Formas de Pago del Día</h6>
                                <a href="ReporteFormaDePago">Ver Reporte</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table text-start align-middle table-bordered table-hover mb-0">
                                    <thead>
                                        <tr class="text-dark">
                                            <th scope="col">Forma de Pago</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ventasPorFormaPago as $forma): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($forma['FormaDePago']); ?></td>
                                            <td><?php echo number_format($forma['Cantidad']); ?></td>
                                            <td>MX$ <?php echo number_format($forma['Total'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Información Adicional End -->

            <!-- Sales Chart End -->


            <!-- Recent Sales Start -->
            <!-- <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Recent Salse</h6>
                        <a href="">Show All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col"><input class="form-check-input" type="checkbox"></th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>01 Jan 2045</td>
                                    <td>INV-0123</td>
                                    <td>Jhon Doe</td>
                                    <td>$123</td>
                                    <td>Paid</td>
                                    <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>01 Jan 2045</td>
                                    <td>INV-0123</td>
                                    <td>Jhon Doe</td>
                                    <td>$123</td>
                                    <td>Paid</td>
                                    <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>01 Jan 2045</td>
                                    <td>INV-0123</td>
                                    <td>Jhon Doe</td>
                                    <td>$123</td>
                                    <td>Paid</td>
                                    <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>01 Jan 2045</td>
                                    <td>INV-0123</td>
                                    <td>Jhon Doe</td>
                                    <td>$123</td>
                                    <td>Paid</td>
                                    <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>01 Jan 2045</td>
                                    <td>INV-0123</td>
                                    <td>Jhon Doe</td>
                                    <td>$123</td>
                                    <td>Paid</td>
                                    <td><a class="btn btn-sm btn-primary" href="">Detail</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> -->
            <!-- Recent Sales End -->


            <!-- Widgets Start -->
            <!-- <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100 bg-light rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Messages</h6>
                                <a href="">Show All</a>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center pt-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100 bg-light rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Calender</h6>
                                <a href="">Show All</a>
                            </div>
                            <div id="calender"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100 bg-light rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">To Do List</h6>
                                <a href="">Show All</a>
                            </div>
                            <div class="d-flex mb-2">
                                <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                                <button type="button" class="btn btn-primary ms-2">Add</button>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox" checked>
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span><del>Short task goes here...</del></span>
                                        <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center pt-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Widgets End -->
 

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Variables para mantener las instancias de los gráficos
    let chartVendidos = null;
    let chartFormaPago = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Datos de productos más vendidos del mes (desde PHP)
        const productosMasVendidos = <?php echo json_encode(array_column($productosMasVendidos, 'Nombre_Prod')); ?>;
        const cantidadesVendidas = <?php echo json_encode(array_column($productosMasVendidos, 'Total_Vendido')); ?>;
        
        // Datos de formas de pago del día (desde PHP)
        const formasPago = <?php echo json_encode(array_column($ventasPorFormaPago, 'FormaDePago')); ?>;
        const totalesFormaPago = <?php echo json_encode(array_column($ventasPorFormaPago, 'Total')); ?>;

        // Configuración del gráfico de productos más vendidos
        const ctx1 = document.getElementById('vendidos').getContext('2d');
        if (chartVendidos) {
            chartVendidos.destroy();
        }
        chartVendidos = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: productosMasVendidos,
                datasets: [{
                    label: 'Total Vendido',
                    data: cantidadesVendidas,
                    backgroundColor: 'rgba(239, 121, 128, 0.6)',
                    borderColor: 'rgba(239, 121, 128, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Configuración del gráfico de formas de pago
        const ctx2 = document.getElementById('forma-pago').getContext('2d');
        if (chartFormaPago) {
            chartFormaPago.destroy();
        }
        chartFormaPago = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: formasPago,
                datasets: [{
                    label: 'Total por Forma de Pago',
                    data: totalesFormaPago,
                    backgroundColor: [
                        'rgba(239, 121, 128, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(239, 121, 128, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': MX$ ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

        <?php include "Footer.php";?>
</body>

</html>
<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/ConsultaDashboard.php"; // Aquí puedes incluir tus consultas para obtener datos del dashboard
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Dashboard Administrativo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-light">
                <?php include "navbar.php"; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="container mt-4">
                    <h1 class="text-center mb-4">Dashboard Administrativo</h1>

                    <!-- Tarjetas de Resumen -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Ventas del Día</h5>
                                    <p class="card-text">MX$ <?php echo $formattedTotal; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Cajas Abiertas</h5>
                                    <p class="card-text"><?php echo $CajasAbiertas; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Productos Vendidos</h5>
                                    <p class="card-text"><?php echo $ProductosVendidos; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Productos No Vendidos</h5>
                                    <p class="card-text"><?php echo $ProductosNoVendidos; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Productos Más Vendidos</div>
                                <div class="card-body">
                                    <canvas id="productosMasVendidos"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Productos No Vendidos</div>
                                <div class="card-body">
                                    <canvas id="productosNoVendidos"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Datos -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">Detalles de Productos</div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Código de Barra</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Aquí puedes iterar sobre los datos de productos
                                            foreach ($productos as $producto) {
                                                echo "<tr>
                                                    <td>{$producto['ID_Prod_POS']}</td>
                                                    <td>{$producto['Nombre_Prod']}</td>
                                                    <td>{$producto['Cod_Barra']}</td>
                                                    <td>MX$ {$producto['Precio']}</td>
                                                    <td>{$producto['Stock']}</td>
                                                </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Productos Más Vendidos
        const ctx1 = document.getElementById('productosMasVendidos').getContext('2d');
        const productosMasVendidos = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productosMasVendidosLabels); ?>,
                datasets: [{
                    label: 'Cantidad Vendida',
                    data: <?php echo json_encode($productosMasVendidosData); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Productos No Vendidos
        const ctx2 = document.getElementById('productosNoVendidos').getContext('2d');
        const productosNoVendidos = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($productosNoVendidosLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($productosNoVendidosData); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>
<?php
include_once "../Controladores/db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

if (empty($folio) || empty($codigo)) {
    echo '<div class="alert alert-danger">Faltan parámetros requeridos</div>';
    exit;
}

// Obtener datos del conteo con comparación de stock
$sql = "SELECT 
    cd.*,
    s.Nombre_Sucursal,
    COALESCE(sp.Existencias_R, 0) as StockRealSistema,
    COALESCE(iss.StockEnMomento, 0) as StockInventarioSucursal,
    sp.Precio_Venta,
    sp.Precio_C,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND COALESCE(sp.Existencias_R, 0) > 0 THEN 
            ROUND(((cd.ExistenciaFisica - COALESCE(sp.Existencias_R, 0)) / COALESCE(sp.Existencias_R, 0)) * 100, 2)
        ELSE NULL
    END as DiferenciaPorcentajeSistema,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND COALESCE(iss.StockEnMomento, 0) > 0 THEN 
            ROUND(((cd.ExistenciaFisica - COALESCE(iss.StockEnMomento, 0)) / COALESCE(iss.StockEnMomento, 0)) * 100, 2)
        ELSE NULL
    END as DiferenciaPorcentajeInventario,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL THEN 
            (cd.ExistenciaFisica - COALESCE(sp.Existencias_R, 0))
        ELSE NULL
    END as DiferenciaUnidadesSistema,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL THEN 
            (cd.ExistenciaFisica - COALESCE(iss.StockEnMomento, 0))
        ELSE NULL
    END as DiferenciaUnidadesInventario
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
LEFT JOIN Stock_POS sp ON cd.Cod_Barra = sp.Cod_Barra AND cd.Fk_sucursal = sp.Fk_sucursal
LEFT JOIN InventariosSucursales iss ON cd.Cod_Barra = iss.Cod_Barra AND cd.Fk_sucursal = iss.Fk_Sucursal
WHERE cd.Folio_Ingreso = ? AND cd.Cod_Barra = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Información del Conteo
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Folio:</strong></td>
                                <td><?php echo htmlspecialchars($row['Folio_Ingreso']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Código de Barras:</strong></td>
                                <td><?php echo htmlspecialchars($row['Cod_Barra']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Producto:</strong></td>
                                <td><?php echo htmlspecialchars($row['Nombre_Producto']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Sucursal:</strong></td>
                                <td><?php echo htmlspecialchars($row['Nombre_Sucursal']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Conteo Físico:</strong></td>
                                <td><strong><?php echo $row['ExistenciaFisica'] !== null ? number_format($row['ExistenciaFisica']) : 'No registrado'; ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Agregado Por:</strong></td>
                                <td><?php echo htmlspecialchars($row['AgregadoPor']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Fecha:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['AgregadoEl'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-chart-line me-2"></i>
                            Comparación de Stocks
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Stock Sistema:</strong></td>
                                <td><strong><?php echo number_format($row['StockRealSistema']); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Stock Inventario:</strong></td>
                                <td><strong><?php echo number_format($row['StockInventarioSucursal']); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Precio Venta:</strong></td>
                                <td>MX$ <?php echo number_format($row['Precio_Venta'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Precio Costo:</strong></td>
                                <td>MX$ <?php echo number_format($row['Precio_C'], 2); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($row['ExistenciaFisica'] !== null): ?>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-calculator me-2"></i>
                            Diferencia vs Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $diferenciaSistema = $row['DiferenciaUnidadesSistema'];
                        $diferenciaPorcentajeSistema = $row['DiferenciaPorcentajeSistema'];
                        $diferenciaSistemaClass = '';
                        
                        if ($diferenciaSistema !== null) {
                            if ($diferenciaSistema > 0) {
                                $diferenciaSistemaClass = 'text-success';
                            } elseif ($diferenciaSistema < 0) {
                                $diferenciaSistemaClass = 'text-danger';
                            } else {
                                $diferenciaSistemaClass = 'text-muted';
                            }
                        }
                        ?>
                        
                        <div class="text-center">
                            <h3 class="<?php echo $diferenciaSistemaClass; ?>">
                                <?php 
                                if ($diferenciaSistema !== null) {
                                    $signo = $diferenciaSistema > 0 ? '+' : '';
                                    echo $signo . number_format($diferenciaSistema);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </h3>
                            <p class="mb-0">Unidades</p>
                            <h5 class="<?php echo $diferenciaSistemaClass; ?>">
                                <?php 
                                if ($diferenciaPorcentajeSistema !== null) {
                                    $signo = $diferenciaPorcentajeSistema > 0 ? '+' : '';
                                    echo $signo . $diferenciaPorcentajeSistema . '%';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </h5>
                            <p class="mb-0">Porcentaje</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-calculator me-2"></i>
                            Diferencia vs Inventario
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $diferenciaInventario = $row['DiferenciaUnidadesInventario'];
                        $diferenciaPorcentajeInventario = $row['DiferenciaPorcentajeInventario'];
                        $diferenciaInventarioClass = '';
                        
                        if ($diferenciaInventario !== null) {
                            if ($diferenciaInventario > 0) {
                                $diferenciaInventarioClass = 'text-success';
                            } elseif ($diferenciaInventario < 0) {
                                $diferenciaInventarioClass = 'text-danger';
                            } else {
                                $diferenciaInventarioClass = 'text-muted';
                            }
                        }
                        ?>
                        
                        <div class="text-center">
                            <h3 class="<?php echo $diferenciaInventarioClass; ?>">
                                <?php 
                                if ($diferenciaInventario !== null) {
                                    $signo = $diferenciaInventario > 0 ? '+' : '';
                                    echo $signo . number_format($diferenciaInventario);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </h3>
                            <p class="mb-0">Unidades</p>
                            <h5 class="<?php echo $diferenciaInventarioClass; ?>">
                                <?php 
                                if ($diferenciaPorcentajeInventario !== null) {
                                    $signo = $diferenciaPorcentajeInventario > 0 ? '+' : '';
                                    echo $signo . $diferenciaPorcentajeInventario . '%';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </h5>
                            <p class="mb-0">Porcentaje</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Análisis de impacto económico -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-dollar-sign me-2"></i>
                            Análisis de Impacto Económico
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h5 class="text-primary">
                                    MX$ <?php 
                                    if ($diferenciaSistema !== null && $row['Precio_Venta'] > 0) {
                                        echo number_format(abs($diferenciaSistema * $row['Precio_Venta']), 2);
                                    } else {
                                        echo '0.00';
                                    }
                                    ?>
                                </h5>
                                <p class="mb-0">Impacto en Ventas</p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-warning">
                                    MX$ <?php 
                                    if ($diferenciaSistema !== null && $row['Precio_C'] > 0) {
                                        echo number_format(abs($diferenciaSistema * $row['Precio_C']), 2);
                                    } else {
                                        echo '0.00';
                                    }
                                    ?>
                                </h5>
                                <p class="mb-0">Impacto en Costos</p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-info">
                                    <?php 
                                    if ($diferenciaSistema !== null && $row['Precio_Venta'] > 0 && $row['Precio_C'] > 0) {
                                        $margen = $row['Precio_Venta'] - $row['Precio_C'];
                                        echo 'MX$ ' . number_format(abs($diferenciaSistema * $margen), 2);
                                    } else {
                                        echo 'MX$ 0.00';
                                    }
                                    ?>
                                </h5>
                                <p class="mb-0">Impacto en Margen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-success" onclick="ExportarConteoIndividual(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-file-excel me-1"></i>Exportar a Excel
                    </button>
                    <button class="btn btn-secondary" onclick="ImprimirConteoIndividual(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-print me-1"></i>Imprimir
                    </button>
                    <button class="btn btn-primary" onclick="GenerarReporteComparativo(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-chart-bar me-1"></i>Reporte Comparativo
                    </button>
                </div>
            </div>
        </div>
        
        <script>
        function ExportarConteoIndividual(folio, codigo) {
            const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarConteoIndividual.php?folio=${folio}&codigo=${codigo}`;
            window.open(url, '_blank');
        }
        
        function ImprimirConteoIndividual(folio, codigo) {
            const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ImprimirConteoIndividual.php?folio=${folio}&codigo=${codigo}`;
            window.open(url, '_blank');
        }
        
        function GenerarReporteComparativo(folio, codigo) {
            const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ReporteComparativoConteo.php?folio=${folio}&codigo=${codigo}`;
            window.open(url, '_blank');
        }
        </script>
        
        <?php
    } else {
        echo '<div class="alert alert-danger">No se encontró el conteo especificado</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta: ' . $conn->error . '</div>';
}

$conn->close();
?>

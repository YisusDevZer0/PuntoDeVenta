<?php
include_once "../Controladores/db_connect.php";

$folio = isset($_POST['folio']) ? $_POST['folio'] : '';
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';

if (empty($folio) || empty($codigo)) {
    echo '<div class="alert alert-danger">Faltan parámetros requeridos</div>';
    exit;
}

// Obtener datos del conteo pausado
$sql = "SELECT 
    cd.*,
    s.Nombre_Sucursal,
    TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) as HorasPausado,
    TIMESTAMPDIFF(DAY, cd.AgregadoEl, NOW()) as DiasPausado
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
WHERE cd.Folio_Ingreso = ? AND cd.Cod_Barra = ? AND cd.EnPausa = 1";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("is", $folio, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $prioridadClass = '';
        $prioridadText = '';
        
        if ($row['HorasPausado'] > 24) {
            $prioridadClass = 'danger';
            $prioridadText = 'Alta';
        } elseif ($row['HorasPausado'] > 12) {
            $prioridadClass = 'warning';
            $prioridadText = 'Media';
        } else {
            $prioridadClass = 'info';
            $prioridadText = 'Baja';
        }
        
        $tiempoPausado = '';
        if ($row['DiasPausado'] > 0) {
            $tiempoPausado = $row['DiasPausado'] . ' días, ' . ($row['HorasPausado'] % 24) . ' horas';
        } else {
            $tiempoPausado = $row['HorasPausado'] . ' horas';
        }
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-<?php echo $prioridadClass; ?> text-white">
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
                                <td><strong>Existencia Real:</strong></td>
                                <td><?php echo number_format($row['Existencias_R']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Existencia Física:</strong></td>
                                <td><?php echo $row['ExistenciaFisica'] !== null ? number_format($row['ExistenciaFisica']) : 'No registrada'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-clock me-2"></i>
                            Estado del Conteo
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td><span class="badge bg-warning">En Pausa</span></td>
                            </tr>
                            <tr>
                                <td><strong>Prioridad:</strong></td>
                                <td><span class="badge bg-<?php echo $prioridadClass; ?>"><?php echo $prioridadText; ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Tiempo Pausado:</strong></td>
                                <td><strong><?php echo $tiempoPausado; ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Agregado Por:</strong></td>
                                <td><?php echo htmlspecialchars($row['AgregadoPor']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Pausa:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['AgregadoEl'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($row['ExistenciaFisica'] !== null): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-calculator me-2"></i>
                            Análisis de Diferencias
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $diferencia = $row['ExistenciaFisica'] - $row['Existencias_R'];
                        $diferenciaPorcentaje = round(($diferencia / $row['Existencias_R']) * 100, 2);
                        $diferenciaClass = '';
                        
                        if ($diferencia > 0) {
                            $diferenciaClass = 'text-success';
                        } elseif ($diferencia < 0) {
                            $diferenciaClass = 'text-danger';
                        } else {
                            $diferenciaClass = 'text-muted';
                        }
                        ?>
                        
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h5 class="<?php echo $diferenciaClass; ?>">
                                    <?php echo number_format($diferencia); ?>
                                </h5>
                                <p class="mb-0">Diferencia en Unidades</p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="<?php echo $diferenciaClass; ?>">
                                    <?php echo $diferenciaPorcentaje; ?>%
                                </h5>
                                <p class="mb-0">Diferencia Porcentual</p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="<?php echo $diferenciaClass; ?>">
                                    <?php echo $diferencia > 0 ? 'Sobrante' : ($diferencia < 0 ? 'Faltante' : 'Exacto'); ?>
                                </h5>
                                <p class="mb-0">Tipo de Diferencia</p>
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
                    <button class="btn btn-success" onclick="ReanudarConteo(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-play me-1"></i>Reanudar Conteo
                    </button>
                    
                    <?php if ($row['ExistenciaFisica'] !== null): ?>
                    <button class="btn btn-warning" onclick="FinalizarConteo(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-check me-1"></i>Finalizar Conteo
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($row['HorasPausado'] > 12): ?>
                    <button class="btn btn-primary" onclick="EnviarRecordatorio(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-bell me-1"></i>Enviar Recordatorio
                    </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-danger" onclick="EliminarConteo(<?php echo $row['Folio_Ingreso']; ?>, '<?php echo htmlspecialchars($row['Cod_Barra']); ?>')">
                        <i class="fa-solid fa-trash me-1"></i>Eliminar Conteo
                    </button>
                </div>
            </div>
        </div>
        
        <?php
    } else {
        echo '<div class="alert alert-danger">No se encontró el conteo pausado especificado</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta: ' . $conn->error . '</div>';
}

$conn->close();
?>

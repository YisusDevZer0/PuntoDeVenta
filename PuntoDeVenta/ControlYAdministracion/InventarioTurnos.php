<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar si hay un turno activo para el usuario
$sql_turno_activo = "SELECT * FROM Inventario_Turnos 
                     WHERE Usuario_Actual = ? 
                     AND Fk_sucursal = ? 
                     AND Estado IN ('activo', 'pausado')
                     AND Fecha_Turno = CURDATE()
                     ORDER BY Hora_Inicio DESC 
                     LIMIT 1";
$stmt_turno = $conn->prepare($sql_turno_activo);
$turno_activo = null;
if ($stmt_turno) {
    $stmt_turno->bind_param("si", $row['Nombre_Apellidos'], $row['Fk_sucursal']);
    $stmt_turno->execute();
    $result_turno = $stmt_turno->get_result();
    $turno_activo = $result_turno->fetch_assoc();
    $stmt_turno->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Inventario por Turnos - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <style>
        .turno-activo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .producto-bloqueado {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .producto-disponible {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
        }
        .badge-estado {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>

<body>
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0" style="color:#0172b6;">
                            <i class="fa-solid fa-clipboard-list me-2"></i>
                            Inventario por Turnos Diarios - <?php echo $row['Licencia']?>
                        </h6>
                        <div class="btn-group">
                            <?php if (!$turno_activo): ?>
                                <button type="button" class="btn btn-success btn-sm" id="btn-iniciar-turno">
                                    <i class="fa-solid fa-play me-2"></i>Iniciar Turno
                                </button>
                            <?php else: ?>
                                <?php if ($turno_activo['Estado'] == 'activo'): ?>
                                    <button type="button" class="btn btn-warning btn-sm" id="btn-pausar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>">
                                        <i class="fa-solid fa-pause me-2"></i>Pausar Turno
                                    </button>
                                <?php elseif ($turno_activo['Estado'] == 'pausado'): ?>
                                    <button type="button" class="btn btn-success btn-sm" id="btn-reanudar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>">
                                        <i class="fa-solid fa-play me-2"></i>Reanudar Turno
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-danger btn-sm" id="btn-finalizar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>">
                                    <i class="fa-solid fa-stop me-2"></i>Finalizar Turno
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-info btn-sm" id="btn-ver-historial">
                                <i class="fa-solid fa-history me-2"></i>Historial
                            </button>
                        </div>
                    </div>
                    
                    <?php if ($turno_activo): ?>
                    <div class="turno-activo">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Folio:</strong> <?php echo $turno_activo['Folio_Turno']; ?><br>
                                <strong>Estado:</strong> 
                                <span class="badge badge-estado bg-<?php echo $turno_activo['Estado'] == 'activo' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($turno_activo['Estado']); ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Inicio:</strong> <?php echo date('d/m/Y H:i', strtotime($turno_activo['Hora_Inicio'])); ?><br>
                                <?php if ($turno_activo['Hora_Pausa']): ?>
                                    <strong>Pausa:</strong> <?php echo date('d/m/Y H:i', strtotime($turno_activo['Hora_Pausa'])); ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Progreso:</strong> 
                                <?php 
                                $porcentaje = $turno_activo['Total_Productos'] > 0 
                                    ? round(($turno_activo['Productos_Completados'] / $turno_activo['Total_Productos']) * 100, 2) 
                                    : 0;
                                ?>
                                <div class="progress mt-1">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%">
                                        <?php echo $porcentaje; ?>%
                                    </div>
                                </div>
                                <small><?php echo $turno_activo['Productos_Completados']; ?> / <?php echo $turno_activo['Total_Productos']; ?> productos</small>
                            </div>
                            <div class="col-md-3">
                                <strong>Usuario:</strong> <?php echo $turno_activo['Usuario_Actual']; ?><br>
                                <strong>Sucursal:</strong> <?php echo $row['Fk_sucursal']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar producto:</label>
                            <input type="text" class="form-control" id="buscar-producto" placeholder="Código de barras o nombre">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filtrar por estado:</label>
                            <select class="form-select" id="filtro-estado-producto">
                                <option value="">Todos</option>
                                <option value="disponible">Disponibles</option>
                                <option value="bloqueado">Bloqueados</option>
                                <option value="completado">Completados</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" id="btn-limpiar-filtros">
                                <i class="fa-solid fa-eraser me-2"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                    
                    <div id="DataInventarioTurnos"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para acciones -->
    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;">
        <div id="Di" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #ef7980 !important;">
                        <p class="heading lead" id="TitulosCajas" style="color:white;"></p>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div id="FormCajas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/InventarioTurnos.js"></script>
    <script>
        var turnoActivo = <?php echo $turno_activo ? json_encode($turno_activo) : 'null'; ?>;
        
        $(document).ready(function() {
            if (turnoActivo) {
                CargarProductosTurno(turnoActivo.ID_Turno);
            }
            
            // Iniciar turno
            $('#btn-iniciar-turno').on('click', function() {
                iniciarTurno();
            });
            
            // Pausar turno
            $('#btn-pausar-turno').on('click', function() {
                var idTurno = $(this).data('turno');
                pausarTurno(idTurno);
            });
            
            // Reanudar turno
            $('#btn-reanudar-turno').on('click', function() {
                var idTurno = $(this).data('turno');
                reanudarTurno(idTurno);
            });
            
            // Finalizar turno
            $('#btn-finalizar-turno').on('click', function() {
                var idTurno = $(this).data('turno');
                finalizarTurno(idTurno);
            });
            
            // Ver historial
            $('#btn-ver-historial').on('click', function() {
                verHistorialTurnos();
            });
            
            // Filtros
            $('#buscar-producto, #filtro-estado-producto').on('change keyup', function() {
                if (turnoActivo) {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }
            });
            
            $('#btn-limpiar-filtros').on('click', function() {
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                if (turnoActivo) {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }
            });
        });
    </script>

    <!-- Footer Start -->
    <?php 
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";
    ?>
</body>
</html>

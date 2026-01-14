<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar si hay un turno activo para el usuario
$nombre_usuario = isset($row['Nombre_Apellidos']) ? trim($row['Nombre_Apellidos']) : '';
$sucursal_id = isset($row['Fk_Sucursal']) ? (int)$row['Fk_Sucursal'] : 0;

// Consulta más flexible para detectar turnos activos
$sql_turno_activo = "SELECT * FROM Inventario_Turnos 
                     WHERE Fk_sucursal = ? 
                     AND Estado IN ('activo', 'pausado')
                     AND Fecha_Turno = CURDATE()
                     AND (Usuario_Actual LIKE ? OR Usuario_Inicio LIKE ?)
                     ORDER BY Hora_Inicio DESC 
                     LIMIT 1";
$stmt_turno = $conn->prepare($sql_turno_activo);
$turno_activo = null;

if ($stmt_turno && !empty($nombre_usuario) && $sucursal_id > 0) {
    // Usar LIKE para manejar variaciones en el nombre
    $usuario_pattern = "%" . $nombre_usuario . "%";
    $stmt_turno->bind_param("iss", $sucursal_id, $usuario_pattern, $usuario_pattern);
    $stmt_turno->execute();
    $result_turno = $stmt_turno->get_result();
    $turno_activo = $result_turno->fetch_assoc();
    $stmt_turno->close();
    
    // Si no se encontró con LIKE, intentar búsqueda exacta también
    if (!$turno_activo) {
        $sql_turno_exacto = "SELECT * FROM Inventario_Turnos 
                            WHERE Fk_sucursal = ? 
                            AND Estado IN ('activo', 'pausado')
                            AND Fecha_Turno = CURDATE()
                            AND (Usuario_Actual = ? OR Usuario_Inicio = ?)
                            ORDER BY Hora_Inicio DESC 
                            LIMIT 1";
        $stmt_exacto = $conn->prepare($sql_turno_exacto);
        if ($stmt_exacto) {
            $stmt_exacto->bind_param("iss", $sucursal_id, $nombre_usuario, $nombre_usuario);
            $stmt_exacto->execute();
            $result_exacto = $stmt_exacto->get_result();
            $turno_activo = $result_exacto->fetch_assoc();
            $stmt_exacto->close();
        }
    }
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
                                $limite_productos = isset($turno_activo['Limite_Productos']) ? $turno_activo['Limite_Productos'] : 50;
                                $total_seleccionados = $turno_activo['Total_Productos'];
                                $completados = $turno_activo['Productos_Completados'];
                                $porcentaje = $total_seleccionados > 0 
                                    ? round(($completados / $total_seleccionados) * 100, 2) 
                                    : 0;
                                ?>
                                <div class="progress mt-1">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%">
                                        <?php echo $porcentaje; ?>%
                                    </div>
                                </div>
                                <small>
                                    <?php echo $completados; ?> / <?php echo $total_seleccionados; ?> completados
                                    (Máx: <?php echo $limite_productos; ?> productos)
                                </small>
                            </div>
                            <div class="col-md-3">
                                <strong>Usuario:</strong> <?php echo $turno_activo['Usuario_Actual']; ?><br>
                                <strong>Sucursal:</strong> <?php echo $row['Fk_sucursal']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="fa-solid fa-barcode me-2"></i>Buscar producto (código o nombre):
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #ef7980; color: white;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" class="form-control" id="buscar-producto" 
                                       placeholder="Escribe código de barras o nombre del producto..." 
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">Comienza a escribir para buscar productos automáticamente</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por estado:</label>
                            <select class="form-select" id="filtro-estado-producto">
                                <option value="">Todos</option>
                                <option value="disponible">Disponibles</option>
                                <option value="bloqueado">Bloqueados</option>
                                <option value="completado">Completados</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary me-2" id="btn-limpiar-filtros">
                                <i class="fa-solid fa-eraser me-2"></i>Limpiar Filtros
                            </button>
                            <button type="button" class="btn btn-info" id="btn-refrescar-productos" title="Refrescar lista">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="DataInventarioTurnos">
                        <?php if ($turno_activo): ?>
                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Cargando productos del inventario...
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                No hay un turno activo. Inicia un nuevo turno para comenzar.
                            </div>
                        <?php endif; ?>
                    </div>
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
        
        // Debug: mostrar información del turno
        console.log('Turno activo:', turnoActivo);
        
        $(document).ready(function() {
            if (turnoActivo && turnoActivo.ID_Turno) {
                console.log('Cargando productos para turno:', turnoActivo.ID_Turno);
                // Crear tabla si no existe
                if ($('#tablaInventarioTurnos').length === 0) {
                    $('#DataInventarioTurnos').html(`
                        <table id="tablaInventarioTurnos" class="table table-striped table-hover">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    `);
                }
                // Esperar un momento para asegurar que el DOM esté listo
                setTimeout(function() {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }, 300);
            } else {
                console.log('No hay turno activo. Usuario:', '<?php echo addslashes($nombre_usuario); ?>', 'Sucursal:', <?php echo $sucursal_id; ?>);
                // NO verificar automáticamente para evitar bucles infinitos
                // Si necesitas verificar un turno, hazlo manualmente con el botón
            }
            
            // Iniciar turno (usar off para evitar duplicados)
            $('#btn-iniciar-turno').off('click').on('click', function() {
                iniciarTurno();
            });
            
            // Pausar turno (usar off para evitar duplicados)
            $('#btn-pausar-turno').off('click').on('click', function() {
                var idTurno = $(this).data('turno');
                pausarTurno(idTurno);
            });
            
            // Reanudar turno (usar off para evitar duplicados)
            $('#btn-reanudar-turno').off('click').on('click', function() {
                var idTurno = $(this).data('turno');
                reanudarTurno(idTurno);
            });
            
            // Finalizar turno (usar off para evitar duplicados)
            $('#btn-finalizar-turno').off('click').on('click', function() {
                var idTurno = $(this).data('turno');
                finalizarTurno(idTurno);
            });
            
            // Ver historial (usar off para evitar duplicados)
            $('#btn-ver-historial').off('click').on('click', function() {
                verHistorialTurnos();
            });
            
            // El sistema de búsqueda activa se inicializa en InventarioTurnos.js
            // No duplicar aquí para evitar conflictos
            
            // Filtro de estado (inmediato)
            $('#filtro-estado-producto').on('change', function() {
                if (turnoActivo && turnoActivo.ID_Turno) {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }
            });
            
            // Limpiar filtros
            $('#btn-limpiar-filtros').on('click', function() {
                clearTimeout(buscarTimeout);
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                if (turnoActivo && turnoActivo.ID_Turno) {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }
            });
            
            // Refrescar productos
            $('#btn-refrescar-productos').on('click', function() {
                if (turnoActivo && turnoActivo.ID_Turno) {
                    $('#buscar-producto').val('');
                    $('#filtro-estado-producto').val('');
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }
            });
            
            // Función para seleccionar producto desde búsqueda
            window.seleccionarProductoDesdeBusqueda = function(idProducto, codigo) {
                if (!turnoActivo || !turnoActivo.ID_Turno) {
                    Swal.fire('Error', 'No hay un turno activo', 'error');
                    return;
                }
                
                $.ajax({
                    url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                    type: 'POST',
                    data: {
                        accion: 'seleccionar_producto',
                        id_turno: turnoActivo.ID_Turno,
                        id_producto: idProducto,
                        cod_barra: codigo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // No mostrar alerta, solo actualizar la tabla
                            CargarProductosTurno(turnoActivo.ID_Turno);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                    }
                });
            };
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

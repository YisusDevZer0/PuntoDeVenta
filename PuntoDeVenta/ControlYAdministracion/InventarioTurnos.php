<?php
include_once "Controladores/ControladorUsuario.php";

$nombre_usuario = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : '';
$sucursal_id = 0;
if (isset($row['Fk_Sucursal']) && $row['Fk_Sucursal'] > 0) {
    $sucursal_id = (int)$row['Fk_Sucursal'];
} elseif (isset($row['Fk_sucursal']) && $row['Fk_sucursal'] > 0) {
    $sucursal_id = (int)$row['Fk_sucursal'];
} elseif (isset($_SESSION['Fk_Sucursal']) && $_SESSION['Fk_Sucursal'] > 0) {
    $sucursal_id = (int)$_SESSION['Fk_Sucursal'];
}

$turno_activo = null;

if ($sucursal_id > 0 && !empty($nombre_usuario)) {
    $sql_turno_activo = "SELECT * FROM Inventario_Turnos 
                         WHERE Usuario_Actual = ? 
                         AND Fk_sucursal = ? 
                         AND Estado IN ('activo', 'pausado')
                         ORDER BY Hora_Inicio DESC
                         LIMIT 1";
    $stmt_turno = $conn->prepare($sql_turno_activo);
    if ($stmt_turno) {
        $stmt_turno->bind_param("si", $nombre_usuario, $sucursal_id);
        $stmt_turno->execute();
        $result_turno = $stmt_turno->get_result();
        $turno_activo = $result_turno->fetch_assoc();
        $stmt_turno->close();
    }
    if (!$turno_activo) {
        $sql_turno_inicio = "SELECT * FROM Inventario_Turnos 
                            WHERE Usuario_Inicio = ? 
                            AND Fk_sucursal = ? 
                            AND Estado IN ('activo', 'pausado')
                            ORDER BY Hora_Inicio DESC
                            LIMIT 1";
        $stmt_inicio = $conn->prepare($sql_turno_inicio);
        if ($stmt_inicio) {
            $stmt_inicio->bind_param("si", $nombre_usuario, $sucursal_id);
            $stmt_inicio->execute();
            $result_inicio = $stmt_inicio->get_result();
            $turno_activo = $result_inicio->fetch_assoc();
            $stmt_inicio->close();
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
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #084298 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.25);
        }
        .turno-activo .row { align-items: center; }
        .turno-activo strong { opacity: 0.9; font-weight: 600; }
        .turno-activo .progress { height: 1.25rem; border-radius: 8px; background: rgba(255,255,255,0.25); }
        .turno-activo .progress-bar { font-size: 0.8rem; font-weight: 600; }
        .producto-bloqueado { background-color: #fff8e6; border-left: 4px solid #ffc107; }
        .producto-disponible { background-color: #e8f4fc; border-left: 4px solid #0d6efd; }
        .producto-ya-contado-otro { background-color: #f0f0f0; border-left: 4px solid #6c757d; }
        .table-success { border-left: 4px solid #198754 !important; }
        .table-warning { border-left: 4px solid #ffc107 !important; }
        .badge-estado { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        #DataInventarioTurnos .table { border-radius: 8px; overflow: hidden; }
        #DataInventarioTurnos .table thead th {
            background: #f8f9fa; font-weight: 600; color: #212529;
            border-bottom: 2px solid #dee2e6; padding: 0.85rem 0.75rem;
        }
        #DataInventarioTurnos .table tbody td { padding: 0.75rem; vertical-align: middle; }
        .search-box-wrap .input-group-text { border-radius: 8px 0 0 8px; }
        .search-box-wrap .form-control { border-radius: 0 8px 8px 0; }
        #DataInventarioTurnos .table thead th.sorting,
        #DataInventarioTurnos .table thead th.sorting_asc,
        #DataInventarioTurnos .table thead th.sorting_desc {
            position: relative; padding-right: 1.5rem !important; cursor: pointer; background-image: none !important;
        }
        #DataInventarioTurnos .table thead th.sorting::after,
        #DataInventarioTurnos .table thead th.sorting_asc::after,
        #DataInventarioTurnos .table thead th.sorting_desc::after {
            position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%);
            font-size: 0.65rem; opacity: 0.5; font-family: inherit;
        }
        #DataInventarioTurnos .table thead th.sorting::after { content: "\21C5"; opacity: 0.35; }
        #DataInventarioTurnos .table thead th.sorting_asc::after { content: "\2191"; opacity: 0.8; color: #0d6efd; }
        #DataInventarioTurnos .table thead th.sorting_desc::after { content: "\2193"; opacity: 0.8; color: #0d6efd; }
    </style>
</head>
<body>
    <?php include_once "Menu.php" ?>
    <div class="content">
        <?php include "navbar.php";?>
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold" style="color:#0d6efd;">
                            <i class="fa-solid fa-clipboard-list me-2"></i>Inventario por Turnos Diarios
                        </h5>
                        <span class="text-muted small"><?php echo htmlspecialchars($row['Licencia']); ?></span>
                        <div class="btn-group btn-group-sm">
                            <?php if (!$turno_activo): ?>
                                <button type="button" class="btn btn-success btn-sm" id="btn-iniciar-turno"><i class="fa-solid fa-play me-2"></i>Iniciar Turno</button>
                            <?php else: ?>
                                <?php if ($turno_activo['Estado'] == 'activo'): ?>
                                    <button type="button" class="btn btn-warning btn-sm" id="btn-pausar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>"><i class="fa-solid fa-pause me-2"></i>Pausar Turno</button>
                                <?php elseif ($turno_activo['Estado'] == 'pausado'): ?>
                                    <button type="button" class="btn btn-success btn-sm" id="btn-reanudar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>"><i class="fa-solid fa-play me-2"></i>Reanudar Turno</button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-danger btn-sm" id="btn-finalizar-turno" data-turno="<?php echo $turno_activo['ID_Turno']; ?>"><i class="fa-solid fa-stop me-2"></i>Finalizar Turno</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-info btn-sm" id="btn-ver-historial"><i class="fa-solid fa-history me-2"></i>Historial</button>
                        </div>
                    </div>
                    
                    <?php if ($turno_activo): ?>
                    <div class="turno-activo">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Folio:</strong> <?php echo $turno_activo['Folio_Turno']; ?><br>
                                <strong>Estado:</strong> 
                                <span class="badge badge-estado bg-<?php echo $turno_activo['Estado'] == 'activo' ? 'success' : 'warning'; ?>"><?php echo ucfirst($turno_activo['Estado']); ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Inicio:</strong> <?php echo date('d/m/Y H:i', strtotime($turno_activo['Hora_Inicio'])); ?><br>
                                <?php if (!empty($turno_activo['Hora_Pausa'])): ?>
                                    <strong>Pausa:</strong> <?php echo date('d/m/Y H:i', strtotime($turno_activo['Hora_Pausa'])); ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Progreso:</strong>
                                <?php 
                                $limite_productos = isset($turno_activo['Limite_Productos']) ? (int)$turno_activo['Limite_Productos'] : 50;
                                $total_seleccionados = (int)$turno_activo['Total_Productos'];
                                $completados = (int)$turno_activo['Productos_Completados'];
                                $porcentaje = $limite_productos > 0 ? min(100, round(($completados / $limite_productos) * 100, 2)) : 0;
                                ?>
                                <div class="progress mt-1">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%"><?php echo $porcentaje; ?>%</div>
                                </div>
                                <small class="texto-progreso">
                                    <?php echo $completados; ?> de <?php echo $limite_productos; ?> requeridos
                                    <?php if ($total_seleccionados > 0): ?>(<?php echo $total_seleccionados; ?> seleccionados)<?php endif; ?>
                                </small>
                            </div>
                            <div class="col-md-3">
                                <strong>Usuario:</strong> <?php echo $turno_activo['Usuario_Actual']; ?><br>
                                <strong>Sucursal:</strong> <?php echo (int)$turno_activo['Fk_sucursal']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4 g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold"><i class="fa-solid fa-barcode me-2 text-primary"></i>Buscar producto</label>
                            <div class="input-group search-box-wrap">
                                <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control" id="buscar-producto" placeholder="Código de barras o nombre del producto..." autocomplete="off">
                            </div>
                            <small class="form-text text-muted">Escribe o escanea para buscar</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <select class="form-select" id="filtro-estado-producto">
                                <option value="">Todos</option>
                                <option value="disponible">Disponibles</option>
                                <option value="en_proceso">En proceso</option>
                                <option value="bloqueado">Bloqueados</option>
                                <option value="completado">Completados</option>
                                <option value="ya_contado_otro_turno">Ya contado (otro turno)</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-filtros"><i class="fa-solid fa-eraser me-1"></i>Limpiar</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-refrescar-productos" title="Refrescar lista"><i class="fa-solid fa-rotate"></i></button>
                        </div>
                    </div>
                    
                    <div id="DataInventarioTurnos">
                        <?php if ($turno_activo): ?>
                            <div class="alert alert-info"><i class="fa-solid fa-info-circle me-2"></i>Cargando productos del inventario...</div>
                        <?php else: ?>
                            <div class="alert alert-info text-center"><i class="fa-solid fa-info-circle me-2"></i>No hay un turno activo. Inicia un nuevo turno para comenzar.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;">
        <div id="Di" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <p class="heading lead mb-0" id="TitulosCajas"></p>
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
            if ($('#tablaInventarioTurnos').length === 0) {
                $('#DataInventarioTurnos').html(`
                    <table id="tablaInventarioTurnos" class="table table-striped table-hover">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                `);
            }
            if (turnoActivo && turnoActivo.ID_Turno) {
                setTimeout(function() { CargarProductosTurno(turnoActivo.ID_Turno); }, 300);
            } else {
                setTimeout(function() { CargarProductosTurno(0); }, 300);
            }
            $('#btn-iniciar-turno').off('click').on('click', function() { iniciarTurno(); });
            $('#btn-pausar-turno').off('click').on('click', function() { pausarTurno($(this).data('turno')); });
            $('#btn-reanudar-turno').off('click').on('click', function() { reanudarTurno($(this).data('turno')); });
            $('#btn-finalizar-turno').off('click').on('click', function() { finalizarTurno($(this).data('turno')); });
            $('#btn-ver-historial').off('click').on('click', function() { verHistorialTurnos(); });
            $('#buscar-producto').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: 'Controladores/AutocompleteInventarioTurnos.php',
                        type: 'GET',
                        dataType: 'json',
                        data: { term: request.term, id_turno: (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0 },
                        success: function (data) { response(data); },
                        error: function() { response([]); }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();
                    var codigo = ui.item.value || ui.item.codigo;
                    var idProducto = ui.item.id;
                    $('#buscar-producto').val('');
                    if (turnoActivo && turnoActivo.ID_Turno && idProducto) {
                        $.ajax({
                            url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                            type: 'POST',
                            data: { accion: 'seleccionar_producto', id_turno: turnoActivo.ID_Turno, id_producto: idProducto, cod_barra: codigo },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({ icon: 'success', title: 'Producto agregado', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
                                    CargarProductosTurno(turnoActivo.ID_Turno);
                                    $('#buscar-producto').focus();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                    $('#buscar-producto').focus();
                                }
                            },
                            error: function() { Swal.fire('Error', 'Error al comunicarse con el servidor', 'error'); $('#buscar-producto').focus(); }
                        });
                    } else {
                        $('#buscar-producto').val(codigo);
                        CargarProductosTurno(0);
                        Swal.fire({ icon: 'info', title: 'Sin turno activo', text: 'Inicia un turno para poder seleccionar productos', timer: 2000, showConfirmButton: false });
                    }
                    return false;
                }
            });
            $('#buscar-producto').on('keyup', function() {
                clearTimeout(buscarTimeout);
                buscarTimeout = setTimeout(function() {
                    CargarProductosTurno((turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0);
                }, 500);
            });
            $('#filtro-estado-producto').off('change').on('change', function() {
                CargarProductosTurno((turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0);
            });
            $('#btn-limpiar-filtros').off('click').on('click', function() {
                clearTimeout(buscarTimeout);
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                CargarProductosTurno((turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0);
            });
            $('#btn-refrescar-productos').off('click').on('click', function() {
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                CargarProductosTurno((turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0);
            });
            window.buscarYSeleccionarProducto = function(codigo) {
                if (!codigo || codigo.trim() === '') return;
                if (!turnoActivo || !turnoActivo.ID_Turno) {
                    Swal.fire('Error', 'No hay un turno activo. Inicia un turno primero.', 'error');
                    return;
                }
                var formData = new FormData();
                formData.append('codigo', codigo.trim());
                formData.append('id_turno', turnoActivo.ID_Turno);
                $.ajax({
                    url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/BusquedaEscanerInventarioTurnos.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.producto) {
                            if (response.bloqueado_por_otro) {
                                Swal.fire({ icon: 'warning', title: 'Producto bloqueado', text: 'Este producto está siendo contado por: ' + response.usuario_bloqueador, timer: 2000, showConfirmButton: false });
                                $('#buscar-producto').val('').focus();
                                return;
                            }
                            if (response.ya_contado_otro_turno) {
                                Swal.fire({ icon: 'warning', title: 'Producto ya contado', text: 'Este producto ya fue contado en el turno ' + (response.folio_turno_anterior || '') + ' hoy.', timer: 3500, showConfirmButton: false });
                                $('#buscar-producto').val('').focus();
                                return;
                            }
                            seleccionarProductoEnTurno(response.producto.id, response.producto.codigo, response.producto.nombre);
                        } else {
                            Swal.fire({ icon: 'info', title: 'Producto no encontrado', text: response.message || 'No se encontró el producto', timer: 2000, showConfirmButton: false });
                            $('#buscar-producto').val('').focus();
                        }
                    },
                    error: function() { Swal.fire('Error', 'Error al buscar el producto', 'error'); $('#buscar-producto').val('').focus(); }
                });
            };
            window.seleccionarProductoDesdeBusqueda = function(idProducto, codigo) {
                if (codigo) buscarYSeleccionarProducto(codigo);
            };
        });
    </script>
    <?php 
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";
    ?>
</body>
</html>

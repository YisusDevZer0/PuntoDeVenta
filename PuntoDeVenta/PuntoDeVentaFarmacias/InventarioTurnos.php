<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar si hay un turno activo para el usuario
// IMPORTANTE: Usar EXACTAMENTE la misma lógica que gestion_turnos.php
$nombre_usuario = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : ''; // SIN trim para coincidir con gestion_turnos.php

// Obtener sucursal - intentar múltiples formas (mayúscula, minúscula, sesión)
$sucursal_id = 0;
if (isset($row['Fk_Sucursal']) && $row['Fk_Sucursal'] > 0) {
    $sucursal_id = (int)$row['Fk_Sucursal'];
} elseif (isset($row['Fk_sucursal']) && $row['Fk_sucursal'] > 0) {
    $sucursal_id = (int)$row['Fk_sucursal'];
} elseif (isset($_SESSION['Fk_Sucursal']) && $_SESSION['Fk_Sucursal'] > 0) {
    $sucursal_id = (int)$_SESSION['Fk_Sucursal'];
}

$turno_activo = null;

if ($sucursal_id > 0) {
    // Usar la MISMA consulta que gestion_turnos.php usa para verificar turno existente
    // Esto asegura consistencia entre ambas verificaciones
    
    // Primero: Buscar por Usuario_Actual (igual que gestion_turnos.php)
    if (!empty($nombre_usuario)) {
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
    }
    
    // Segundo: Si no se encontró, buscar por Usuario_Inicio
    if (!$turno_activo && !empty($nombre_usuario)) {
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
    
    // Tercero: Si aún no se encontró, buscar cualquier turno activo en la sucursal
    // Esto es útil si el nombre de usuario tiene variaciones o espacios
    if (!$turno_activo) {
        $sql_turno_sucursal = "SELECT * FROM Inventario_Turnos 
                              WHERE Fk_sucursal = ? 
                              AND Estado IN ('activo', 'pausado')
                              ORDER BY Hora_Inicio DESC 
                              LIMIT 1";
        $stmt_sucursal = $conn->prepare($sql_turno_sucursal);
        if ($stmt_sucursal) {
            $stmt_sucursal->bind_param("i", $sucursal_id);
            $stmt_sucursal->execute();
            $result_sucursal = $stmt_sucursal->get_result();
            $turno_activo = $result_sucursal->fetch_assoc();
            $stmt_sucursal->close();
        }
    }
    
    // DEBUG: Si no se encontró turno, consultar todos los turnos activos para debug
    if (!$turno_activo && $sucursal_id > 0) {
        $sql_debug = "SELECT ID_Turno, Folio_Turno, Usuario_Actual, Usuario_Inicio, Estado, Fk_sucursal 
                     FROM Inventario_Turnos 
                     WHERE Fk_sucursal = ? 
                     AND Estado IN ('activo', 'pausado')
                     ORDER BY Hora_Inicio DESC";
        $stmt_debug = $conn->prepare($sql_debug);
        if ($stmt_debug) {
            $stmt_debug->bind_param("i", $sucursal_id);
            $stmt_debug->execute();
            $result_debug = $stmt_debug->get_result();
            $turnos_debug = [];
            while ($row_debug = $result_debug->fetch_assoc()) {
                $turnos_debug[] = $row_debug;
            }
            $stmt_debug->close();
            
            // Guardar en variable JavaScript para debug
            if (!empty($turnos_debug)) {
                echo "<script>console.log('DEBUG: Turnos encontrados en BD:', " . json_encode($turnos_debug) . ");</script>";
                echo "<script>console.log('DEBUG: Usuario buscado:', " . json_encode($nombre_usuario) . ");</script>";
            }
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
        
        // Debug: mostrar información del turno y usuario
        console.log('=== DEBUG TURNO ===');
        console.log('Usuario buscado:', '<?php echo addslashes($nombre_usuario); ?>');
        console.log('Sucursal:', <?php echo $sucursal_id; ?>);
        console.log('Fk_Sucursal en row:', <?php echo isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 'null'; ?>);
        console.log('Fk_Sucursal en SESSION:', <?php echo isset($_SESSION['Fk_Sucursal']) ? $_SESSION['Fk_Sucursal'] : 'null'; ?>);
        console.log('Turno encontrado:', turnoActivo);
        if (turnoActivo) {
            console.log('Usuario_Actual en BD:', turnoActivo.Usuario_Actual);
            console.log('Usuario_Inicio en BD:', turnoActivo.Usuario_Inicio);
            console.log('Fk_sucursal en BD:', turnoActivo.Fk_sucursal);
        }
        console.log('==================');
        
        $(document).ready(function() {
            // Crear tabla siempre, incluso sin turno activo
            if ($('#tablaInventarioTurnos').length === 0) {
                $('#DataInventarioTurnos').html(`
                    <table id="tablaInventarioTurnos" class="table table-striped table-hover">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                `);
            }
            
            if (turnoActivo && turnoActivo.ID_Turno) {
                console.log('Cargando productos para turno:', turnoActivo.ID_Turno);
                // Esperar un momento para asegurar que el DOM esté listo
                setTimeout(function() {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                }, 300);
            } else {
                console.log('No hay turno activo detectado. Usuario:', '<?php echo addslashes($nombre_usuario); ?>', 'Sucursal:', <?php echo $sucursal_id; ?>);
                // Intentar verificar si hay un turno que no se detectó
                $.ajax({
                    url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
                    type: 'POST',
                    data: { accion: 'verificar_turno' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.turno) {
                            console.log('Turno encontrado después de verificar:', response.turno);
                            // Recargar la página para mostrar el turno
                            location.reload();
                        } else {
                            // Cargar productos disponibles sin turno
                            setTimeout(function() {
                                CargarProductosTurno(0); // 0 indica sin turno
                            }, 300);
                        }
                    },
                    error: function() {
                        // Cargar productos disponibles sin turno
                        setTimeout(function() {
                            CargarProductosTurno(0);
                        }, 300);
                    }
                });
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
            
            // Inicializar autocomplete EXACTAMENTE como en InventarioSucursales
            $('#buscar-producto').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: 'Controladores/AutocompleteInventarioTurnos.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: request.term,
                            id_turno: (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0
                        },
                        success: function (data) {
                            response(data);
                        },
                        error: function() {
                            response([]);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();
                    var codigo = ui.item.value || ui.item.codigo;
                    var idProducto = ui.item.id;
                    
                    // Limpiar el campo primero
                    $('#buscar-producto').val('');
                    
                    // Si hay turno activo, seleccionar producto directamente
                    if (turnoActivo && turnoActivo.ID_Turno && idProducto) {
                        // Hacer la selección directamente sin esperar
                        $.ajax({
                            url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Producto agregado',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                    CargarProductosTurno(turnoActivo.ID_Turno);
                                    $('#buscar-producto').focus();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                    $('#buscar-producto').focus();
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                                $('#buscar-producto').focus();
                            }
                        });
                    } else {
                        // Sin turno activo, solo actualizar la tabla con la búsqueda
                        $('#buscar-producto').val(codigo);
                        CargarProductosTurno(0);
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin turno activo',
                            text: 'Inicia un turno para poder seleccionar productos',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    return false;
                }
            });
            
            // Actualizar tabla mientras se escribe (con debounce)
            $('#buscar-producto').on('keyup', function() {
                clearTimeout(buscarTimeout);
                buscarTimeout = setTimeout(function() {
                    var idTurno = (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0;
                    CargarProductosTurno(idTurno);
                }, 500);
            });
            
            // Filtro de estado
            $('#filtro-estado-producto').off('change').on('change', function() {
                var idTurno = (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0;
                CargarProductosTurno(idTurno);
            });
            
            // Limpiar filtros
            $('#btn-limpiar-filtros').off('click').on('click', function() {
                clearTimeout(buscarTimeout);
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                var idTurno = (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0;
                CargarProductosTurno(idTurno);
            });
            
            // Refrescar productos
            $('#btn-refrescar-productos').off('click').on('click', function() {
                $('#buscar-producto').val('');
                $('#filtro-estado-producto').val('');
                var idTurno = (turnoActivo && turnoActivo.ID_Turno) ? turnoActivo.ID_Turno : 0;
                CargarProductosTurno(idTurno);
            });
            
            // Función para buscar y seleccionar producto (disponible globalmente)
            window.buscarYSeleccionarProducto = function(codigo) {
                if (!codigo || codigo.trim() === '') {
                    return;
                }
                
                if (!turnoActivo || !turnoActivo.ID_Turno) {
                    Swal.fire('Error', 'No hay un turno activo. Inicia un turno primero.', 'error');
                    return;
                }
                
                // Buscar producto primero
                var formData = new FormData();
                formData.append('codigo', codigo.trim());
                formData.append('id_turno', turnoActivo.ID_Turno);
                
                $.ajax({
                    url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/BusquedaEscanerInventarioTurnos.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.producto) {
                            // Verificar si está bloqueado
                            if (response.bloqueado_por_otro) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Producto bloqueado',
                                    text: 'Este producto está siendo contado por: ' + response.usuario_bloqueador,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#buscar-producto').val('').focus();
                                return;
                            }
                            
                            // Seleccionar el producto
                            $.ajax({
                                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
                                type: 'POST',
                                data: {
                                    accion: 'seleccionar_producto',
                                    id_turno: turnoActivo.ID_Turno,
                                    id_producto: response.producto.id,
                                    cod_barra: response.producto.codigo
                                },
                                dataType: 'json',
                                success: function(resp) {
                                    if (resp.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Producto agregado',
                                            text: response.producto.nombre,
                                            timer: 1500,
                                            showConfirmButton: false,
                                            toast: true,
                                            position: 'top-end'
                                        });
                                        CargarProductosTurno(turnoActivo.ID_Turno);
                                        $('#buscar-producto').val('').focus();
                                    } else {
                                        Swal.fire('Error', resp.message, 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Producto no encontrado',
                                text: response.message || 'No se encontró el producto',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#buscar-producto').val('').focus();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al buscar el producto', 'error');
                        $('#buscar-producto').val('').focus();
                    }
                });
            };
            
            // Función para seleccionar producto desde búsqueda (compatibilidad)
            window.seleccionarProductoDesdeBusqueda = function(idProducto, codigo) {
                if (codigo) {
                    buscarYSeleccionarProducto(codigo);
                }
            };
        });
    </script>

    <!-- Footer Start -->
    <?php 
    include "Modales/Modales_Errores.php";
    include "Footer.php";
    ?>
</body>
</html>

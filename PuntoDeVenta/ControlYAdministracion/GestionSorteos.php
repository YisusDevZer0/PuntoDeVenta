<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Sorteos - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    <style>
        .badge-activo { background-color: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
        .badge-inactivo { background-color: #dc3545; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
        .badge-programado { background-color: #ffc107; color: #333; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
        .badge-finalizado { background-color: #6c757d; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
        .card-sorteo { border-left: 4px solid #ef7980; }
        .sorteo-stats { font-size: 24px; font-weight: bold; color: #ef7980; }
        #tablaSorteos th { background-color: #ef7980 !important; color: white; font-size: 13px; }
        #tablaSorteos td { font-size: 13px; }
        #tablaParticipaciones th { background-color: #17a2b8 !important; color: white; font-size: 13px; }
        #tablaParticipaciones td { font-size: 13px; }
        .sucursal-check { margin: 4px 8px; }
    </style>
</head>
<div id="loading-overlay">
    <div class="loader"></div>
    <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    <?php include_once "Menu.php" ?>
    <div class="content">
        <?php include "navbar.php";?>
        
        <div class="container-fluid pt-4 px-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 style="color:#ef7980;"><i class="fa-solid fa-gift"></i> Gestión de Sorteos</h4>
                        <button class="btn btn-primary" onclick="abrirModalCrear()">
                            <i class="fa-solid fa-plus"></i> Nuevo Sorteo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-pills mb-3" id="sorteoTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-sorteos" data-bs-toggle="pill" href="#panel-sorteos" role="tab">
                        <i class="fa-solid fa-list"></i> Sorteos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-participaciones" data-bs-toggle="pill" href="#panel-participaciones" role="tab">
                        <i class="fa-solid fa-users"></i> Participaciones
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Panel Sorteos -->
                <div class="tab-pane fade show active" id="panel-sorteos" role="tabpanel">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="table-responsive">
                            <table id="tablaSorteos" class="table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Participaciones</th>
                                        <th>Sucursales</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Panel Participaciones -->
                <div class="tab-pane fade" id="panel-participaciones" role="tabpanel">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Filtrar por sorteo:</label>
                                <select id="filtroSorteo" class="form-control" onchange="cargarParticipaciones()">
                                    <option value="0">Todos los sorteos</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tablaParticipaciones" class="table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sorteo</th>
                                        <th>Ticket</th>
                                        <th>Folio Rifa</th>
                                        <th>Cliente</th>
                                        <th>Teléfono</th>
                                        <th>F. Nacimiento</th>
                                        <th>Sucursal</th>
                                        <th>Participa</th>
                                        <th>Registrado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear/Editar Sorteo -->
    <div class="modal fade" id="modalSorteo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #ef7980; color: white;">
                    <h5 class="modal-title" id="modalSorteoTitulo">
                        <i class="fa-solid fa-gift"></i> Nuevo Sorteo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSorteo">
                        <input type="hidden" id="sorteo_id" name="id">
                        <input type="hidden" name="creado_por" value="<?php echo $row['Nombre_Apellidos']; ?>">
                        <input type="hidden" name="actualizado_por" value="<?php echo $row['Nombre_Apellidos']; ?>">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nombre del sorteo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sorteo_nombre" name="nombre" required 
                                       placeholder="Ej: Sorteo Día de las Madres 2026">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" id="sorteo_descripcion" name="descripcion" rows="2" 
                                          placeholder="Descripción del sorteo (opcional)"></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="sorteo_fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="sorteo_fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prefijo del folio (personalizable)</label>
                                <input type="text" class="form-control" id="sorteo_prefijo" name="prefijo_folio" 
                                       placeholder="Ej: MAMA, NAVIDAD" maxlength="10">
                                <small class="text-muted">Dejar vacío para usar el prefijo de sucursal por defecto</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Folio inicial</label>
                                <input type="number" class="form-control" id="sorteo_folio_inicio" name="folio_inicio" 
                                       value="1" min="1">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sorteo_aplica_todas" 
                                           name="aplica_todas" value="1" checked onchange="toggleSucursales()">
                                    <label class="form-check-label" for="sorteo_aplica_todas">
                                        Aplica a <strong>todas</strong> las sucursales
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="divSucursales" style="display:none;">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Seleccionar sucursales:</label>
                                <div id="listaSucursales" class="border rounded p-3" style="max-height:200px; overflow-y:auto;">
                                    <?php
                                    $sqlSuc = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Estatus = 'Vigente' ORDER BY Nombre_Sucursal";
                                    $resSuc = mysqli_query($conn, $sqlSuc);
                                    while ($suc = mysqli_fetch_assoc($resSuc)) {
                                        echo '<div class="form-check sucursal-check">';
                                        echo '<input class="form-check-input sucursal-cb" type="checkbox" value="' . $suc['ID_Sucursal'] . '" id="suc_' . $suc['ID_Sucursal'] . '">';
                                        echo '<label class="form-check-label" for="suc_' . $suc['ID_Sucursal'] . '">' . $suc['Nombre_Sucursal'] . '</label>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarSorteo" onclick="guardarSorteo()">
                        <i class="fa-solid fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include "Footer.php"; ?>

    <script>
    var tablaSorteos, tablaParticipaciones;

    $(document).ready(function() {
        tablaSorteos = $('#tablaSorteos').DataTable({
            processing: true,
            ajax: {
                url: "Controladores/SorteosController.php?action=listar",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'ID_Sorteo' },
                { data: 'Nombre_Sorteo' },
                { data: 'Fecha_Inicio' },
                { data: 'Fecha_Fin' },
                { data: null, render: function(data) {
                    var cls = {
                        'Activo': 'badge-activo',
                        'Inactivo': 'badge-inactivo',
                        'Programado': 'badge-programado',
                        'Finalizado': 'badge-finalizado'
                    };
                    return '<span class="' + (cls[data.Estado] || 'badge-finalizado') + '">' + data.Estado + '</span>';
                }},
                { data: 'TotalParticipaciones' },
                { data: null, render: function(data) {
                    if (data.Aplica_Todas_Sucursales == 1) return '<span class="badge bg-info">Todas</span>';
                    if (data.Sucursales && data.Sucursales.length > 0) {
                        return data.Sucursales.map(function(s) { return s.Nombre_Sucursal; }).join(', ');
                    }
                    return '-';
                }},
                { data: null, render: function(data) {
                    var btnToggle = data.Activo == 1
                        ? '<button class="btn btn-warning btn-sm me-1" onclick="toggleSorteo(' + data.ID_Sorteo + ')" title="Desactivar"><i class="fa-solid fa-pause"></i></button>'
                        : '<button class="btn btn-success btn-sm me-1" onclick="toggleSorteo(' + data.ID_Sorteo + ')" title="Activar"><i class="fa-solid fa-play"></i></button>';
                    return btnToggle +
                        '<button class="btn btn-info btn-sm me-1" onclick="editarSorteo(' + data.ID_Sorteo + ')" title="Editar"><i class="fa-solid fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-sm" onclick="eliminarSorteo(' + data.ID_Sorteo + ')" title="Eliminar"><i class="fa-solid fa-trash"></i></button>';
                }}
            ],
            order: [[0, 'desc']],
            language: { url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            responsive: true
        });

        tablaParticipaciones = $('#tablaParticipaciones').DataTable({
            processing: true,
            ajax: {
                url: "Controladores/SorteosController.php?action=listarParticipaciones&sorteo_id=0",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'ID_Participacion' },
                { data: 'Nombre_Sorteo' },
                { data: 'Fk_Venta_Ticket' },
                { data: 'FolioRifa' },
                { data: 'Nombre_Cliente' },
                { data: 'Telefono_Cliente' },
                { data: 'FechaNacimiento_Cliente' },
                { data: 'Nombre_Sucursal' },
                { data: null, render: function(data) {
                    return data.Participa == 1 
                        ? '<span class="badge-activo">Sí</span>' 
                        : '<span class="badge-inactivo">No</span>';
                }},
                { data: 'RegistradoEl' }
            ],
            order: [[0, 'desc']],
            language: { url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            responsive: true,
            dom: '<"d-flex justify-content-between"lBf>rtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                className: 'btn btn-success btn-sm',
                title: 'Participaciones de Sorteo',
                exportOptions: { columns: ':visible' }
            }]
        });

        // Cargar select de sorteos para filtro
        $.getJSON("Controladores/SorteosController.php?action=listar", function(data) {
            if (data.aaData) {
                data.aaData.forEach(function(s) {
                    $('#filtroSorteo').append('<option value="' + s.ID_Sorteo + '">' + s.Nombre_Sorteo + '</option>');
                });
            }
        });
    });

    function toggleSucursales() {
        var checked = document.getElementById('sorteo_aplica_todas').checked;
        document.getElementById('divSucursales').style.display = checked ? 'none' : 'block';
    }

    function abrirModalCrear() {
        document.getElementById('formSorteo').reset();
        document.getElementById('sorteo_id').value = '';
        document.getElementById('modalSorteoTitulo').innerHTML = '<i class="fa-solid fa-gift"></i> Nuevo Sorteo';
        document.getElementById('sorteo_aplica_todas').checked = true;
        document.getElementById('divSucursales').style.display = 'none';
        document.querySelectorAll('.sucursal-cb').forEach(function(cb) { cb.checked = false; });
        var modal = new bootstrap.Modal(document.getElementById('modalSorteo'));
        modal.show();
    }

    function editarSorteo(id) {
        $.getJSON("Controladores/SorteosController.php?action=obtener&id=" + id, function(resp) {
            if (resp.status === 'success') {
                var d = resp.data;
                document.getElementById('sorteo_id').value = d.ID_Sorteo;
                document.getElementById('sorteo_nombre').value = d.Nombre_Sorteo;
                document.getElementById('sorteo_descripcion').value = d.Descripcion || '';
                document.getElementById('sorteo_fecha_inicio').value = d.Fecha_Inicio;
                document.getElementById('sorteo_fecha_fin').value = d.Fecha_Fin;
                document.getElementById('sorteo_prefijo').value = d.Prefijo_Folio || '';
                document.getElementById('sorteo_folio_inicio').value = d.Folio_Inicio || 1;
                
                var aplicaTodas = d.Aplica_Todas_Sucursales == 1;
                document.getElementById('sorteo_aplica_todas').checked = aplicaTodas;
                document.getElementById('divSucursales').style.display = aplicaTodas ? 'none' : 'block';
                
                // Marcar sucursales
                document.querySelectorAll('.sucursal-cb').forEach(function(cb) { cb.checked = false; });
                if (d.sucursales_ids) {
                    d.sucursales_ids.forEach(function(sid) {
                        var cb = document.getElementById('suc_' + sid);
                        if (cb) cb.checked = true;
                    });
                }
                
                document.getElementById('modalSorteoTitulo').innerHTML = '<i class="fa-solid fa-edit"></i> Editar Sorteo';
                var modal = new bootstrap.Modal(document.getElementById('modalSorteo'));
                modal.show();
            }
        });
    }

    function guardarSorteo() {
        var form = document.getElementById('formSorteo');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        var id = document.getElementById('sorteo_id').value;
        formData.append('action', id ? 'actualizar' : 'crear');
        
        // Aplica todas
        formData.set('aplica_todas', document.getElementById('sorteo_aplica_todas').checked ? '1' : '0');
        
        // Sucursales seleccionadas
        var sucursales = [];
        document.querySelectorAll('.sucursal-cb:checked').forEach(function(cb) {
            sucursales.push(cb.value);
        });
        formData.append('sucursales', JSON.stringify(sucursales));

        $.ajax({
            url: "Controladores/SorteosController.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                var resp = typeof data === 'string' ? JSON.parse(data) : data;
                if (resp.status === 'success') {
                    Swal.fire({ icon: 'success', title: resp.message, timer: 2000, showConfirmButton: false });
                    bootstrap.Modal.getInstance(document.getElementById('modalSorteo')).hide();
                    tablaSorteos.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            }
        });
    }

    function toggleSorteo(id) {
        Swal.fire({
            title: '¿Cambiar estado del sorteo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post("Controladores/SorteosController.php", { action: 'toggleActivo', id: id }, function(data) {
                    var resp = typeof data === 'string' ? JSON.parse(data) : data;
                    if (resp.status === 'success') {
                        tablaSorteos.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    }

    function eliminarSorteo(id) {
        Swal.fire({
            title: '¿Eliminar este sorteo?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post("Controladores/SorteosController.php", { action: 'eliminar', id: id }, function(data) {
                    var resp = typeof data === 'string' ? JSON.parse(data) : data;
                    if (resp.status === 'success') {
                        tablaSorteos.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: resp.message });
                    }
                });
            }
        });
    }

    function cargarParticipaciones() {
        var sorteoId = document.getElementById('filtroSorteo').value;
        tablaParticipaciones.ajax.url("Controladores/SorteosController.php?action=listarParticipaciones&sorteo_id=" + sorteoId).load();
    }
    </script>
</body>
</html>

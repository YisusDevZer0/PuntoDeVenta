<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/BitacoraLimpiezaController.php";

$controller = new BitacoraLimpiezaController($conn);
$bitacoras = $controller->obtenerBitacoras();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de limpieza de <?php echo $row["Licencia"]?> <?php echo $row["Nombre_Sucursal"]?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
</head>
<body>
    <?php include_once "Menu.php" ?>
    <div class="content">
        <?php include "navbar.php";?>
        <div class="container-fluid pt-4 px-8">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Bitácora y control de limpieza de <?php echo $row["Licencia"]?> Sucursal <?php echo $row["Nombre_Sucursal"]?></h6>
                    
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalNuevaBitacora">
                            <i class="fas fa-plus"></i> Nueva Bitácora
                        </button>
                        <button type="button" class="btn btn-success" id="btnAgregarElemento" style="display:none;">
                            <i class="fas fa-plus"></i> Agregar Elemento
                        </button>
                        <button type="button" class="btn btn-info" id="btnVerBitacoras">
                            <i class="fas fa-list"></i> Ver Bitácoras
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalRecordatoriosLimpieza">
                            <i class="fas fa-bell"></i> Recordatorios
                        </button>
                    </div>

                    <div class="row mb-4" id="selector-bitacora" style="display:none;">
                        <div class="col-md-6">
                            <label for="selectBitacora" class="form-label">Seleccionar Bitácora:</label>
                            <select class="form-select" id="selectBitacora">
                                <option value="">-- Seleccione una bitácora --</option>
                                <?php foreach($bitacoras as $bitacora): ?>
                                    <option value="<?php echo $bitacora["id_bitacora"]; ?>">
                                        <?php echo $bitacora["area"] . " - " . $bitacora["semana"] . " (" . $bitacora["fecha_inicio"] . " a " . $bitacora["fecha_fin"] . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div id="tabla-limpieza" style="display:none;">
                        <table id="tablaElementos" class="table table-bordered table-striped" style="width:100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>Elemento</th>
                                    <th>Lunes M</th>
                                    <th>Lunes V</th>
                                    <th>Martes M</th>
                                    <th>Martes V</th>
                                    <th>Miércoles M</th>
                                    <th>Miércoles V</th>
                                    <th>Jueves M</th>
                                    <th>Jueves V</th>
                                    <th>Viernes M</th>
                                    <th>Viernes V</th>
                                    <th>Sábado M</th>
                                    <th>Sábado V</th>
                                    <th>Domingo M</th>
                                    <th>Domingo V</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyElementos">
                            </tbody>
                        </table>
                    </div>

                    <div id="tabla-bitacoras">
                        <table id="tablaBitacoras" class="table table-bordered table-striped" style="width:100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Área</th>
                                    <th>Semana</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Responsable</th>
                                    <th>Supervisor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bitacoras as $bitacora): ?>
                                <tr>
                                    <td><?php echo $bitacora["id_bitacora"]; ?></td>
                                    <td><?php echo $bitacora["area"]; ?></td>
                                    <td><?php echo $bitacora["semana"]; ?></td>
                                    <td><?php echo $bitacora["fecha_inicio"]; ?></td>
                                    <td><?php echo $bitacora["fecha_fin"]; ?></td>
                                    <td><?php echo $bitacora["responsable"]; ?></td>
                                    <td><?php echo $bitacora["supervisor"]; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-editar-bitacora" data-id="<?php echo $bitacora["id_bitacora"]; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success btn-ver-elementos" data-id="<?php echo $bitacora["id_bitacora"]; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-eliminar-bitacora" data-id="<?php echo $bitacora["id_bitacora"]; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        include "Modales/NuevaBitacora.php";
        include "Modales/AgregarElemento.php";
        include "Modales/RecordatoriosLimpieza.php";
        include "Footer.php";
        ?>
    </div>
</body>
</html>

<script>
$(document).ready(function() {
    // Inicializar DataTable para bitácoras
    $('#tablaBitacoras').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });

    // Variables globales
    let bitacoraActual = null;

    // Mostrar/ocultar vistas
    $('#btnVerBitacoras').click(function() {
        $('#tabla-bitacoras').show();
        $('#tabla-limpieza').hide();
        $('#selector-bitacora').hide();
        $('#btnAgregarElemento').hide();
    });

    // Crear nueva bitácora
    $('#btnGuardarBitacora').click(function() {
        const formData = {
            area: $('#area').val(),
            semana: $('#semana').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val(),
            responsable: $('#responsable').val(),
            supervisor: $('#supervisor').val(),
            aux_res: $('#aux_res').val()
        };

        $.ajax({
            url: 'api/crear_bitacora.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            }
        });
    });

    // Ver elementos de una bitácora
    $(document).on('click', '.btn-ver-elementos', function() {
        const idBitacora = $(this).data('id');
        bitacoraActual = idBitacora;
        
        $('#selectBitacora').val(idBitacora);
        $('#tabla-bitacoras').hide();
        $('#tabla-limpieza').show();
        $('#selector-bitacora').show();
        $('#btnAgregarElemento').show();
        
        cargarElementosLimpieza(idBitacora);
    });

    // Cambiar bitácora desde el selector
    $('#selectBitacora').change(function() {
        const idBitacora = $(this).val();
        if (idBitacora) {
            bitacoraActual = idBitacora;
            cargarElementosLimpieza(idBitacora);
        }
    });

    // Función para cargar elementos de limpieza
    function cargarElementosLimpieza(idBitacora) {
        $.ajax({
            url: 'api/obtener_detalles_limpieza.php',
            method: 'POST',
            data: { id_bitacora: idBitacora },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarElementosLimpieza(response.data);
                } else {
                    console.error('Error:', response.message);
                }
            },
            error: function() {
                console.error('Error de conexión');
            }
        });
    }

    // Función para mostrar elementos de limpieza
    function mostrarElementosLimpieza(elementos) {
        const tbody = $('#tbodyElementos');
        tbody.empty();

        if (elementos.length === 0) {
            tbody.append('<tr><td colspan="16" class="text-center">No hay elementos registrados</td></tr>');
            return;
        }

        elementos.forEach(function(elemento) {
            const row = `
                <tr>
                    <td>${elemento.elemento}</td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="lunes_mat" ${elemento.lunes_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="lunes_vesp" ${elemento.lunes_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="martes_mat" ${elemento.martes_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="martes_vesp" ${elemento.martes_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="miercoles_mat" ${elemento.miercoles_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="miercoles_vesp" ${elemento.miercoles_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="jueves_mat" ${elemento.jueves_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="jueves_vesp" ${elemento.jueves_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="viernes_mat" ${elemento.viernes_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="viernes_vesp" ${elemento.viernes_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="sabado_mat" ${elemento.sabado_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="sabado_vesp" ${elemento.sabado_vesp ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="domingo_mat" ${elemento.domingo_mat ? 'checked' : ''}></td>
                    <td><input type="checkbox" class="form-check-input checkbox-limpieza" data-id="${elemento.id_detalle}" data-campo="domingo_vesp" ${elemento.domingo_vesp ? 'checked' : ''}></td>
                    <td>
                        <button class="btn btn-sm btn-danger btn-eliminar-elemento" data-id="${elemento.id_detalle}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Inicializar DataTable para elementos
        $('#tablaElementos').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "responsive": true,
            "scrollX": true
        });
    }

    // Actualizar estado de limpieza
    $(document).on('change', '.checkbox-limpieza', function() {
        const idDetalle = $(this).data('id');
        const campo = $(this).data('campo');
        const valor = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'api/actualizar_estado_limpieza.php',
            method: 'POST',
            data: {
                id_detalle: idDetalle,
                campo: campo,
                valor: valor
            },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    console.error('Error:', response.message);
                    $(this).prop('checked', !$(this).is(':checked'));
                }
            },
            error: function() {
                console.error('Error de conexión');
                $(this).prop('checked', !$(this).is(':checked'));
            }
        });
    });

    // Agregar elemento
    $('#btnAgregarElemento').click(function() {
        if (!bitacoraActual) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe seleccionar una bitácora primero'
            });
            return;
        }
        $('#id_bitacora_elemento').val(bitacoraActual);
        $('#ModalAgregarElemento').modal('show');
    });

    // Guardar elemento
    $('#btnGuardarElemento').click(function() {
        const elemento = $('#elemento').val();
        const idBitacora = $('#id_bitacora_elemento').val();

        if (!elemento.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe ingresar el nombre del elemento'
            });
            return;
        }

        $.ajax({
            url: 'api/agregar_elemento.php',
            method: 'POST',
            data: {
                id_bitacora: idBitacora,
                elemento: elemento
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    }).then(() => {
                        $('#ModalAgregarElemento').modal('hide');
                        $('#elemento').val('');
                        cargarElementosLimpieza(idBitacora);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            }
        });
    });

    // Eliminar bitácora
    $(document).on('click', '.btn-eliminar-bitacora', function() {
        const idBitacora = $(this).data('id');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/eliminar_bitacora.php',
                    method: 'POST',
                    data: { id_bitacora: idBitacora },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión'
                        });
                    }
                });
            }
        });
    });

    // Funcionalidad de recordatorios
    $('#btnGuardarRecordatorio').click(function() {
        const formData = {
            titulo: $('#titulo_recordatorio').val(),
            descripcion: $('#descripcion_recordatorio').val(),
            fecha_recordatorio: $('#fecha_recordatorio').val(),
            prioridad: $('#prioridad_recordatorio').val(),
            id_usuario: 1
        };

        if (!formData.titulo.trim() || !formData.descripcion.trim() || !formData.fecha_recordatorio) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Todos los campos son requeridos'
            });
            return;
        }

        $.ajax({
            url: 'api/crear_recordatorio.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    }).then(() => {
                        $('#formRecordatorio')[0].reset();
                        cargarRecordatorios();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            }
        });
    });

    // Cargar recordatorios
    function cargarRecordatorios() {
        $.ajax({
            url: 'api/obtener_recordatorios.php',
            method: 'POST',
            data: { estado: 'activo' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarRecordatorios(response.data);
                }
            },
            error: function() {
                console.error('Error al cargar recordatorios');
            }
        });
    }

    // Mostrar recordatorios
    function mostrarRecordatorios(recordatorios) {
        const container = $('#lista-recordatorios');
        container.empty();

        if (recordatorios.length === 0) {
            container.append('<p class="text-muted">No hay recordatorios activos</p>');
            return;
        }

        recordatorios.forEach(function(recordatorio) {
            const prioridadClass = {
                'baja': 'text-success',
                'media': 'text-warning',
                'alta': 'text-danger'
            };

            const recordatorioHtml = `
                <div class="card mb-2">
                    <div class="card-body p-2">
                        <h6 class="card-title ${prioridadClass[recordatorio.prioridad]}">${recordatorio.titulo}</h6>
                        <p class="card-text small">${recordatorio.descripcion}</p>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> ${new Date(recordatorio.fecha_recordatorio).toLocaleString()}
                        </small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-success btn-completar-recordatorio" data-id="${recordatorio.id_recordatorio}">
                                <i class="fas fa-check"></i> Completar
                            </button>
                            <button class="btn btn-sm btn-danger btn-cancelar-recordatorio" data-id="${recordatorio.id_recordatorio}">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(recordatorioHtml);
        });
    }

    // Completar recordatorio
    $(document).on('click', '.btn-completar-recordatorio', function() {
        const idRecordatorio = $(this).data('id');
        
        $.ajax({
            url: 'api/actualizar_recordatorio.php',
            method: 'POST',
            data: {
                id_recordatorio: idRecordatorio,
                estado: 'completado'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Completado',
                        text: 'Recordatorio marcado como completado'
                    });
                    cargarRecordatorios();
                }
            }
        });
    });

    // Cancelar recordatorio
    $(document).on('click', '.btn-cancelar-recordatorio', function() {
        const idRecordatorio = $(this).data('id');
        
        Swal.fire({
            title: '¿Cancelar recordatorio?',
            text: "Esta acción marcará el recordatorio como cancelado",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/actualizar_recordatorio.php',
                    method: 'POST',
                    data: {
                        id_recordatorio: idRecordatorio,
                        estado: 'cancelado'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelado',
                                text: 'Recordatorio cancelado'
                            });
                            cargarRecordatorios();
                        }
                    }
                });
            }
        });
    });

    // Cargar recordatorios al abrir el modal
    $('#ModalRecordatoriosLimpieza').on('show.bs.modal', function() {
        cargarRecordatorios();
    });
});
</script>

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
    <title>Registro de limpieza de <?php echo $row['Licencia']?> <?php echo $row['Nombre_Sucursal']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
    <?php include "header.php";?>
</head>
<body>
        <!-- Spinner End -->
        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->

            <!-- Table Start -->
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Registro de Limpieza - <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
                    
                    <!-- Instrucciones para el usuario -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Seleccione una bitácora asignada para completar los datos de limpieza. Marque con ✓ las tareas que haya realizado. Los cambios se guardan automáticamente.
                    </div>
                    
                    <!-- Botones de acción simplificados -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-success" id="btnAgregarElemento" style="display:none;">
                            <i class="fas fa-plus"></i> Agregar Elemento
                        </button>
                        <button type="button" class="btn btn-info" id="btnVerBitacoras">
                            <i class="fas fa-list"></i> Ver Bitácoras Asignadas
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalRecordatoriosLimpieza">
                            <i class="fas fa-bell"></i> Recordatorios
                        </button>
                    </div>

                    <!-- Selector de bitácora mejorado -->
                    <div class="row mb-4" id="selector-bitacora" style="display:none;">
                        <div class="col-md-6">
                            <label for="selectBitacora" class="form-label">Seleccionar Bitácora para Completar Datos:</label>
                            <select class="form-select" id="selectBitacora">
                                <option value="">-- Seleccione una bitácora para trabajar --</option>
                                <?php foreach($bitacoras as $bitacora): ?>
                                    <option value="<?php echo $bitacora['id_bitacora']; ?>" 
                                            data-area="<?php echo htmlspecialchars($bitacora['area']); ?>"
                                            data-semana="<?php echo htmlspecialchars($bitacora['semana']); ?>"
                                            data-fecha-inicio="<?php echo $bitacora['fecha_inicio']; ?>"
                                            data-fecha-fin="<?php echo $bitacora['fecha_fin']; ?>"
                                            data-responsable="<?php echo htmlspecialchars($bitacora['responsable']); ?>"
                                            data-supervisor="<?php echo htmlspecialchars($bitacora['supervisor']); ?>">
                                        <?php echo $bitacora['area'] . ' - ' . $bitacora['semana'] . ' (' . $bitacora['fecha_inicio'] . ' a ' . $bitacora['fecha_fin'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Información de la Bitácora:</label>
                            <div id="info-bitacora" class="alert alert-info" style="display:none;">
                                <div class="row">
                                    <div class="col-6"><strong>Área:</strong> <span id="info-area"></span></div>
                                    <div class="col-6"><strong>Semana:</strong> <span id="info-semana"></span></div>
                                    <div class="col-6"><strong>Responsable:</strong> <span id="info-responsable"></span></div>
                                    <div class="col-6"><strong>Supervisor:</strong> <span id="info-supervisor"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de elementos de limpieza mejorada -->
                    <div id="tabla-limpieza" style="display:none;">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Registro de Limpieza:</strong> Marque con ✓ las tareas de limpieza que haya completado. Los cambios se guardan automáticamente.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm" id="btnMarcarTodo">
                                        <i class="fas fa-check-double me-1"></i>Marcar Todo
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" id="btnDesmarcarTodo">
                                        <i class="fas fa-times me-1"></i>Desmarcar Todo
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" id="btnGuardarManual">
                                        <i class="fas fa-save me-1"></i>Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicador de progreso -->
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%">
                                <span id="progressText">0% Completado</span>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="tablaElementos" class="table table-bordered table-striped" style="width:100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Elemento de Limpieza</th>
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
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabla de bitácoras -->
                    <div id="tabla-bitacoras" style="display: block;">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Bitácoras Asignadas:</strong> Estas bitácoras han sido creadas por el administrador. Seleccione una para completar los datos de limpieza.
                        </div>
                        
                        <!-- Filtros de búsqueda -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="buscarBitacora" placeholder="Buscar por área, semana o responsable...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filtroAreaBitacora">
                                    <option value="">Todas las áreas</option>
                                    <?php 
                                    $areasUnicas = array_unique(array_column($bitacoras, 'area'));
                                    foreach($areasUnicas as $area): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($area); ?>"><?php echo htmlspecialchars($area); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filtroEstadoBitacora">
                                    <option value="">Todas las bitácoras</option>
                                    <option value="activa">Activas</option>
                                    <option value="completada">Completadas</option>
                                    <option value="pendiente">Pendientes</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros">
                                    <i class="fas fa-times me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                        
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
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bitacoras as $bitacora): ?>
                                <tr>
                                    <td><?php echo $bitacora['id_bitacora']; ?></td>
                                    <td><?php echo $bitacora['area']; ?></td>
                                    <td><?php echo $bitacora['semana']; ?></td>
                                    <td><?php echo $bitacora['fecha_inicio']; ?></td>
                                    <td><?php echo $bitacora['fecha_fin']; ?></td>
                                    <td><?php echo $bitacora['responsable']; ?></td>
                                    <td><?php echo $bitacora['supervisor']; ?></td>
                                    <td>
                                        <span class="badge bg-success">Activa</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success btn-ver-elementos" data-id="<?php echo $bitacora['id_bitacora']; ?>" title="Completar Datos de Limpieza">
                                            <i class="fas fa-edit me-1"></i>Trabajar
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
        <!-- Footer Start -->
        <?php 
        include "Modales/NuevaBitacora.php";
        include "Modales/AgregarElemento.php";
        include "Modales/RecordatoriosLimpieza.php";
        include "Footer.php";
        ?>
</div>

<script>
   $(document).ready(function() {
        // Inicializar DataTable para bitácoras con filtros personalizados
        let tablaBitacoras;
        
        function inicializarTablaBitacoras() {
            if ($.fn.DataTable.isDataTable('#tablaBitacoras')) {
                $('#tablaBitacoras').DataTable().destroy();
            }
            
            tablaBitacoras = $('#tablaBitacoras').DataTable({
                "paging": true,
                "searching": false, // Deshabilitamos la búsqueda nativa para usar la personalizada
                "info": true,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // Columna de acciones
                ],
                "initComplete": function() {
                    console.log('DataTable de bitácoras inicializado correctamente');
                }
            });
        }
        
        // Inicializar la tabla al cargar la página
        inicializarTablaBitacoras();

        // Filtros personalizados
        $('#buscarBitacora').on('keyup', function() {
            tablaBitacoras.search(this.value).draw();
        });

        $('#filtroAreaBitacora').on('change', function() {
            const area = this.value;
            if (area) {
                tablaBitacoras.column(1).search(area).draw();
            } else {
                tablaBitacoras.column(1).search('').draw();
            }
        });

        $('#filtroEstadoBitacora').on('change', function() {
            const estado = this.value;
            if (estado) {
                tablaBitacoras.column(7).search(estado).draw();
            } else {
                tablaBitacoras.column(7).search('').draw();
            }
        });

        $('#btnLimpiarFiltros').click(function() {
            $('#buscarBitacora').val('');
            $('#filtroAreaBitacora').val('');
            $('#filtroEstadoBitacora').val('');
            tablaBitacoras.search('').columns().search('').draw();
        });

        // Variables globales
        let bitacoraActual = null;
        let elementosLimpieza = [];
        let cambiosPendientes = new Map();
        let autoSaveTimeout = null;

        // Mostrar/ocultar vistas
        $('#btnVerBitacoras').click(function() {
            console.log('Botón Ver Bitácoras presionado');
            
            // Cambiar el texto del botón para indicar que está procesando
            const btnOriginal = $(this).html();
            $(this).html('<i class="fas fa-spinner fa-spin me-1"></i>Cargando...');
            $(this).prop('disabled', true);
            
            // Mostrar tabla de bitácoras
            $('#tabla-bitacoras').show();
            $('#tabla-limpieza').hide();
            $('#selector-bitacora').hide();
            $('#btnAgregarElemento').hide();
            
            // Reinicializar la tabla si es necesario
            setTimeout(function() {
                if (typeof tablaBitacoras !== 'undefined') {
                    tablaBitacoras.columns.adjust().draw();
                } else {
                    inicializarTablaBitacoras();
                }
                
                // Restaurar el botón
                $('#btnVerBitacoras').html(btnOriginal);
                $('#btnVerBitacoras').prop('disabled', false);
                
                // Scroll hacia la tabla
                $('html, body').animate({
                    scrollTop: $('#tabla-bitacoras').offset().top - 100
                }, 500);
                
                // Mostrar mensaje de confirmación
                mostrarMensajeGuardado('success', 'Bitácoras cargadas correctamente');
            }, 100);
        });

        // Mostrar información de la bitácora seleccionada
        $('#selectBitacora').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#info-area').text(selectedOption.data('area'));
                $('#info-semana').text(selectedOption.data('semana'));
                $('#info-responsable').text(selectedOption.data('responsable'));
                $('#info-supervisor').text(selectedOption.data('supervisor'));
                $('#info-bitacora').show();
            } else {
                $('#info-bitacora').hide();
            }
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

        // Función para mostrar elementos de limpieza mejorada
        function mostrarElementosLimpieza(elementos) {
            const tbody = $('#tbodyElementos');
            tbody.empty();
            elementosLimpieza = elementos;

            if (elementos.length === 0) {
                tbody.append('<tr><td colspan="16" class="text-center">No hay elementos registrados</td></tr>');
                actualizarProgreso();
                return;
            }

            elementos.forEach(function(elemento, index) {
                const row = `
                    <tr data-elemento-id="${elemento.id_detalle}">
                        <td class="fw-bold">${elemento.elemento}</td>
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
                            <button class="btn btn-sm btn-danger btn-eliminar-elemento" data-id="${elemento.id_detalle}" title="Eliminar elemento">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            // Inicializar DataTable para elementos
            if ($.fn.DataTable.isDataTable('#tablaElementos')) {
                $('#tablaElementos').DataTable().destroy();
            }
            
            $('#tablaElementos').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "responsive": true,
                "scrollX": true,
                "order": [[0, "asc"]]
            });

            actualizarProgreso();
        }

        // Actualizar estado de limpieza con guardado inteligente
        $(document).on('change', '.checkbox-limpieza', function() {
            const idDetalle = $(this).data('id');
            const campo = $(this).data('campo');
            const valor = $(this).is(':checked') ? 1 : 0;
            const checkbox = $(this);

            // Agregar a cambios pendientes
            const key = `${idDetalle}_${campo}`;
            cambiosPendientes.set(key, { idDetalle, campo, valor });

            // Actualizar progreso inmediatamente
            actualizarProgreso();

            // Guardado automático con debounce (esperar 2 segundos después del último cambio)
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                guardarCambiosPendientes();
            }, 2000);

            // Mostrar indicador visual de guardado
            mostrarIndicadorGuardado();
        });

        // Función para guardar cambios pendientes
        function guardarCambiosPendientes() {
            if (cambiosPendientes.size === 0) return;

            const cambios = Array.from(cambiosPendientes.values());
            let guardados = 0;
            let errores = 0;

            cambios.forEach(cambio => {
                $.ajax({
                    url: 'api/actualizar_estado_limpieza.php',
                    method: 'POST',
                    data: {
                        id_detalle: cambio.idDetalle,
                        campo: cambio.campo,
                        valor: cambio.valor
                    },
                    dataType: 'json',
                    success: function(response) {
                        guardados++;
                        if (response.success) {
                            cambiosPendientes.delete(`${cambio.idDetalle}_${cambio.campo}`);
                        } else {
                            errores++;
                            console.error('Error:', response.message);
                        }
                        
                        if (guardados + errores === cambios.length) {
                            if (errores === 0) {
                                mostrarMensajeGuardado('success', 'Cambios guardados correctamente');
                            } else {
                                mostrarMensajeGuardado('warning', `${guardados} cambios guardados, ${errores} errores`);
                            }
                        }
                    },
                    error: function() {
                        errores++;
                        console.error('Error de conexión');
                        if (guardados + errores === cambios.length) {
                            mostrarMensajeGuardado('error', 'Error al guardar algunos cambios');
                        }
                    }
                });
            });
        }

        // Función para actualizar progreso
        function actualizarProgreso() {
            const totalCheckboxes = $('.checkbox-limpieza').length;
            const checkboxesMarcados = $('.checkbox-limpieza:checked').length;
            const porcentaje = totalCheckboxes > 0 ? Math.round((checkboxesMarcados / totalCheckboxes) * 100) : 0;
            
            $('#progressBar').css('width', porcentaje + '%');
            $('#progressText').text(`${porcentaje}% Completado (${checkboxesMarcados}/${totalCheckboxes})`);
            
            // Cambiar color según progreso
            const progressBar = $('#progressBar');
            progressBar.removeClass('bg-success bg-warning bg-danger');
            if (porcentaje === 100) {
                progressBar.addClass('bg-success');
            } else if (porcentaje >= 50) {
                progressBar.addClass('bg-warning');
            } else {
                progressBar.addClass('bg-danger');
            }
        }

        // Función para mostrar indicador de guardado
        function mostrarIndicadorGuardado() {
            const indicador = $('#btnGuardarManual');
            indicador.html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');
            indicador.prop('disabled', true);
            
            setTimeout(() => {
                indicador.html('<i class="fas fa-save me-1"></i>Guardar');
                indicador.prop('disabled', false);
            }, 1000);
        }

        // Función para mostrar mensaje de guardado
        function mostrarMensajeGuardado(tipo, mensaje) {
            const alertClass = tipo === 'success' ? 'alert-success' : tipo === 'warning' ? 'alert-warning' : 'alert-danger';
            const icon = tipo === 'success' ? 'fa-check-circle' : tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle';
            
            const alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} me-2"></i>${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('#tabla-limpieza .alert-success').after(alert);
            
            setTimeout(() => {
                alert.fadeOut();
            }, 3000);
        }

        // Botones de control masivo
        $('#btnMarcarTodo').click(function() {
            $('.checkbox-limpieza').prop('checked', true).trigger('change');
            mostrarMensajeGuardado('info', 'Todos los elementos marcados');
        });

        $('#btnDesmarcarTodo').click(function() {
            $('.checkbox-limpieza').prop('checked', false).trigger('change');
            mostrarMensajeGuardado('info', 'Todos los elementos desmarcados');
        });

        $('#btnGuardarManual').click(function() {
            if (cambiosPendientes.size > 0) {
                guardarCambiosPendientes();
            } else {
                mostrarMensajeGuardado('info', 'No hay cambios pendientes para guardar');
            }
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
                id_usuario: 1 // Se puede obtener de la sesión
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

</body>
</html>

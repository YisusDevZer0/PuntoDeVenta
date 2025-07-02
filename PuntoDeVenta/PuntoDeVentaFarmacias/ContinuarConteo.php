<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar que $row esté definido
if (!isset($row['Fk_Sucursal']) || !isset($row['Licencia'])) {
    die("Error: No se ha iniciado sesión correctamente");
}

$usuarioActual = $row['Nombre_Apellidos'];
$sucursalActual = $row['Fk_Sucursal'];

// Verificar que existe un conteo en pausa para el usuario
$sql_verificar = "SELECT 
                    MIN(AgregadoEl) as Fecha_Creacion,
                    MAX(AgregadoEl) as Fecha_Pausa,
                    COUNT(*) as Total_Productos,
                    COUNT(CASE WHEN ExistenciaFisica IS NOT NULL THEN 1 END) as Productos_Contados
                  FROM ConteosDiarios 
                  WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1
                  AND AgregadoEl = (
                    SELECT MAX(AgregadoEl) FROM ConteosDiarios 
                    WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1
                  )";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("ssss", $usuarioActual, $sucursalActual, $usuarioActual, $sucursalActual);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows === 0) {
    die("Error: No se encontró un conteo en pausa para continuar");
}

$conteo = $result_verificar->fetch_assoc();

// Obtener la fecha del conteo en pausa más reciente
$sql_fecha = "SELECT MAX(AgregadoEl) as FechaUltimoConteo FROM ConteosDiarios WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1";
$stmt_fecha = $conn->prepare($sql_fecha);
$stmt_fecha->bind_param("ss", $usuarioActual, $sucursalActual);
$stmt_fecha->execute();
$result_fecha = $stmt_fecha->get_result();
$fechaUltimoConteo = null;
if ($row_fecha = $result_fecha->fetch_assoc()) {
    $fechaUltimoConteo = $row_fecha['FechaUltimoConteo'];
}
$stmt_fecha->close();

// Obtener los productos ya contados SOLO del conteo más reciente
$sql_productos_contados = "SELECT Cod_Barra, ExistenciaFisica, Nombre_Producto, Existencias_R
                           FROM ConteosDiarios
                           WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1
                           AND ExistenciaFisica IS NOT NULL
                           AND AgregadoEl = ?
                           ORDER BY AgregadoEl";
$stmt_productos = $conn->prepare($sql_productos_contados);
$stmt_productos->bind_param("sss", $usuarioActual, $sucursalActual, $fechaUltimoConteo);
$stmt_productos->execute();
$productos_contados = $stmt_productos->get_result();

// Obtener productos pendientes (del mismo conteo pausado, ExistenciaFisica IS NULL)
$sql_productos_pendientes = "SELECT Cod_Barra, Nombre_Producto, Existencias_R
                            FROM ConteosDiarios
                            WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1
                            AND ExistenciaFisica IS NULL
                            AND AgregadoEl = ?
                            ORDER BY AgregadoEl";
$stmt_pendientes = $conn->prepare($sql_productos_pendientes);
$stmt_pendientes->bind_param("sss", $usuarioActual, $sucursalActual, $fechaUltimoConteo);
$stmt_pendientes->execute();
$productos_restantes = $stmt_pendientes->get_result();

$stmt_verificar->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Continuar Conteo - <?php echo htmlspecialchars($row['Licencia']); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php"; ?>
    <style>
        .producto-contado {
            background-color: #d4edda !important;
        }
        .producto-pendiente {
            background-color: #fff3cd !important;
        }
    </style>
</head>
<body>
    <?php include_once "Menu.php"; ?>

    <div class="content">
        <?php include "navbar.php"; ?>

        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 style="color:#0172b6;">Continuar Conteo Diario - <?php echo htmlspecialchars($row['Licencia']); ?></h6>
                        <div>
                            <span class="badge bg-warning">Conteo en Pausa</span>
                        </div>
                    </div>
                    
                    <!-- Información del progreso -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Progreso del Conteo</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Productos Contados:</strong> <?php echo $conteo['Productos_Contados']; ?> de <?php echo $conteo['Total_Productos']; ?></p>
                                    <p><strong>Progreso:</strong> 
                                        <?php 
                                        $porcentaje = ($conteo['Total_Productos'] > 0) ? 
                                            round(($conteo['Productos_Contados'] / $conteo['Total_Productos']) * 100, 1) : 0;
                                        echo $porcentaje . '%';
                                        ?>
                                    </p>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?php echo $porcentaje; ?>%" 
                                             aria-valuenow="<?php echo $porcentaje; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $porcentaje; ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-clock"></i> Fechas</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Fecha de Creación:</strong><br>
                                    <?php echo date('d/m/Y H:i', strtotime($conteo['Fecha_Creacion'])); ?></p>
                                    <p><strong>Fecha de Pausa:</strong><br>
                                    <?php echo date('d/m/Y H:i', strtotime($conteo['Fecha_Pausa'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos ya contados -->
                    <?php if ($productos_contados->num_rows > 0): ?>
                    <div class="mb-4">
                        <h6 class="text-success"><i class="fas fa-check-circle"></i> Productos Ya Contados (<?php echo $productos_contados->num_rows; ?>)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Stock Físico</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($producto = $productos_contados->fetch_assoc()): ?>
                                    <tr class="producto-contado">
                                        <td><?php echo htmlspecialchars($producto['Cod_Barra']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($producto['ExistenciaFisica']); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Formulario para productos restantes -->
                    <?php if ($productos_restantes->num_rows > 0): ?>
                    <form id="ContinuarConteoForm" action="javascript:void(0)" method="post">
                        <input type="hidden" name="EnPausa" value="0">
                        
                        <h6 class="text-warning"><i class="fas fa-edit"></i> Productos Pendientes por Contar (<?php echo $productos_restantes->num_rows; ?>)</h6>
                        
                        <div class="text-center mb-3">
                            <button type="button" id="btnPausarContinuacion" class="btn btn-warning me-2">
                                <i class="fas fa-pause"></i> Pausar Nuevamente
                            </button>
                            <button type="submit" id="btnFinalizarContinuacion" class="btn btn-success">
                                <i class="fas fa-save"></i> Finalizar Conteo
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="ProductosRestantes" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Stock Físico</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($producto = $productos_restantes->fetch_assoc()): ?>
                                    <tr class="producto-pendiente">
                                        <td>
                                            <input type="text" class="form-control" name="CodBarra[]" 
                                                   value="<?php echo htmlspecialchars($producto['Cod_Barra']); ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="NombreProd[]" 
                                                   value="<?php echo htmlspecialchars($producto['Nombre_Producto']); ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="StockFisico[]" 
                                                   min="0" step="1">
                                        </td>
                                        <!-- Campo oculto para el ID del registro de ConteosDiarios -->
                                        <?php
                                        // Obtener el ID del registro para este producto
                                        $sql_id = "SELECT id FROM ConteosDiarios WHERE Cod_Barra = ? AND Fk_sucursal = ? AND AgregadoPor = ? AND EnPausa = 1 AND AgregadoEl = ? AND ExistenciaFisica IS NULL LIMIT 1";
                                        $stmt_id = $conn->prepare($sql_id);
                                        $stmt_id->bind_param("ssss", $producto['Cod_Barra'], $sucursalActual, $usuarioActual, $fechaUltimoConteo);
                                        $stmt_id->execute();
                                        $result_id = $stmt_id->get_result();
                                        $id_registro = ($row_id = $result_id->fetch_assoc()) ? $row_id['id'] : '';
                                        $stmt_id->close();
                                        ?>
                                        <input type="hidden" name="IdConteo[]" value="<?php echo htmlspecialchars($id_registro); ?>">
                                        <input type="hidden" name="Existencias_R[]" value="<?php echo htmlspecialchars($producto['Existencias_R']); ?>">
                                        <input type="hidden" name="Agrego[]" value="<?php echo htmlspecialchars($row['Nombre_Apellidos']); ?>">
                                        <input type="hidden" name="Sucursal[]" value="<?php echo htmlspecialchars($row['Fk_Sucursal']); ?>">
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-success text-center">
                        <h5><i class="fas fa-check-circle"></i> ¡Todos los productos han sido contados!</h5>
                        <p>Puedes finalizar el conteo ahora.</p>
                        <button type="button" id="btnFinalizarCompleto" class="btn btn-success">
                            <i class="fas fa-check"></i> Finalizar Conteo Completo
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Inicializar DataTable para productos restantes
        if ($.fn.DataTable.isDataTable('#ProductosRestantes')) {
            $('#ProductosRestantes').DataTable().destroy();
        }

        var table = $('#ProductosRestantes').DataTable({
            "destroy": true,
            "retrieve": true,
            "order": [[0, "desc"]],
            "lengthMenu": [[50], [50]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "processing": "Procesando..."
            },
            "responsive": true
        });

        // Función para validar el formulario
        function validarFormulario() {
            let stockFisicoInputs = $('input[name="StockFisico[]"]');
            let todosLlenos = true;
            
            stockFisicoInputs.each(function() {
                if (!$(this).val()) {
                    todosLlenos = false;
                    return false;
                }
            });

            if (!todosLlenos) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, complete todos los campos de Stock Físico',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#0172b6'
                });
                return false;
            }
            return true;
        }

        // Función para enviar datos
        function enviarDatos(enPausa = 0) {
            // Agregar el estado de pausa al formulario
            if ($('input[name="EnPausa"]').length) {
                $('input[name="EnPausa"]').val(enPausa);
            } else {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'EnPausa',
                    value: enPausa
                }).appendTo('#ContinuarConteoForm');
            }

            // Mostrar mensaje de carga
            Swal.fire({
                title: enPausa ? 'Guardando y Pausando' : 'Finalizando Conteo',
                text: 'Por favor espere...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar datos mediante AJAX
            $.ajax({
                url: 'Controladores/GuardarConteo.php',
                type: 'POST',
                data: $('#ContinuarConteoForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: enPausa ? 'Conteo Pausado' : '¡Conteo Finalizado!',
                            text: enPausa ? 
                                'El conteo se ha guardado y pausado correctamente.' : 
                                'El conteo se ha finalizado correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#0172b6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'ConteoDiario.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Hubo un error al procesar el conteo',
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#0172b6'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un error al comunicarse con el servidor',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#0172b6'
                    });
                }
            });
        }

        // Evento del botón pausar
        $('#btnPausarContinuacion').on('click', function() {
            Swal.fire({
                title: '¿Pausar el conteo?',
                text: '¿Estás seguro de que deseas pausar el conteo nuevamente?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, pausar',
                cancelButtonText: 'No, continuar',
                confirmButtonColor: '#0172b6',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarDatos(1); // 1 = en pausa
                }
            });
        });

        // Evento del formulario (finalizar)
        $('#ContinuarConteoForm').on('submit', function(e) {
            e.preventDefault();
            if (validarFormulario()) {
                Swal.fire({
                    title: '¿Finalizar el conteo?',
                    text: '¿Estás seguro de que deseas finalizar el conteo completo?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'No, revisar',
                    confirmButtonColor: '#0172b6',
                    cancelButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarDatos(0); // 0 = finalizado
                    }
                });
            }
        });

        // Botón para finalizar conteo completo (cuando no hay productos restantes)
        $('#btnFinalizarCompleto').on('click', function() {
            Swal.fire({
                title: '¿Finalizar el conteo completo?',
                text: '¿Estás seguro de que deseas finalizar el conteo?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'No, cancelar',
                confirmButtonColor: '#0172b6',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar solicitud para finalizar el conteo
                    $.ajax({
                        url: 'Controladores/FinalizarConteo.php',
                        type: 'POST',
                        data: {
                            id_conteo: 1, // Valor dummy, el controlador usa usuario y sucursal
                            accion: 'finalizar'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: '¡Conteo Finalizado!',
                                    text: 'El conteo se ha finalizado correctamente.',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#0172b6'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'ConteoDiario.php';
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Hubo un error al finalizar el conteo',
                                    icon: 'error',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#0172b6'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un error al comunicarse con el servidor',
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#0172b6'
                            });
                        }
                    });
                }
            });
        });
    });
    </script>
</body>
</html> 
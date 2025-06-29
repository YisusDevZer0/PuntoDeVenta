<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar que $row esté definido
if (!isset($row['Fk_Sucursal']) || !isset($row['Licencia'])) {
    die("Error: No se ha iniciado sesión correctamente");
}

// Optimizar la consulta SQL usando índices y limitando los campos necesarios
$sql1 = "SELECT Cod_Barra, Nombre_Prod, Existencias_R 
         FROM `Stock_POS` 
         WHERE Fk_sucursal = ? 
         AND Tipo_Servicio = 5
         AND Cod_Barra IS NOT NULL 
         AND Cod_Barra != ''
         ORDER BY RAND()
         LIMIT 50";

// Preparar la consulta para prevenir SQL injection
$stmt = $conn->prepare($sql1);
$stmt->bind_param("s", $row['Fk_Sucursal']);
$stmt->execute();
$query = $stmt->get_result();

// Consulta para verificar si hay un conteo en pausa y obtener información
$usuarioActual = $row['Nombre_Apellidos'];
$sucursalActual = $row['Fk_Sucursal'];
$conteoPausado = false;
$infoConteoPausado = null;

$sqlCheck = "SELECT 
                MIN(AgregadoEl) as Fecha_Creacion,
                MAX(AgregadoEl) as Fecha_Pausa,
                COUNT(*) as Total_Productos,
                COUNT(CASE WHEN ExistenciaFisica IS NOT NULL THEN 1 END) as Productos_Contados
             FROM ConteosDiarios 
             WHERE AgregadoPor = ? AND Fk_sucursal = ? AND EnPausa = 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $usuarioActual, $sucursalActual);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    $infoConteoPausado = $resultCheck->fetch_assoc();
    // Solo mostrar si hay productos en pausa
    if ($infoConteoPausado['Total_Productos'] > 0) {
        $conteoPausado = true;
    } else {
        $conteoPausado = false;
    }
} else {
    $conteoPausado = false;
}
$stmtCheck->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Conteo Diario - <?php echo htmlspecialchars($row['Licencia']); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <style>
        /* Eliminamos los estilos del loader que ya no usaremos */
        #btnFinalizarConteo {
            display: none !important;
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
                    <h6 class="mb-4" style="color:#0172b6;">Conteo Diario - <?php echo htmlspecialchars($row['Licencia']); ?></h6>
                    
                    <?php if ($conteoPausado): ?>
                        <div class="alert alert-warning text-center">
                            <h5><i class="fas fa-exclamation-triangle"></i> Conteo Diario en Pausa</h5>
                            <p class="mb-3">Ya tienes un conteo diario en pausa. Debes finalizarlo antes de iniciar uno nuevo.</p>
                            
                            <?php if ($infoConteoPausado): ?>
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información del Conteo Pausado</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Fecha de Creación:</strong><br>
                                                        <?php echo date('d/m/Y H:i', strtotime($infoConteoPausado['Fecha_Creacion'])); ?></p>
                                                        <p><strong>Fecha de Pausa:</strong><br>
                                                        <?php echo date('d/m/Y H:i', strtotime($infoConteoPausado['Fecha_Pausa'])); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Productos Contados:</strong><br>
                                                        <?php echo $infoConteoPausado['Productos_Contados']; ?> de <?php echo $infoConteoPausado['Total_Productos']; ?></p>
                                                        <p><strong>Progreso:</strong><br>
                                                        <?php 
                                                        $porcentaje = ($infoConteoPausado['Total_Productos'] > 0) ? 
                                                            round(($infoConteoPausado['Productos_Contados'] / $infoConteoPausado['Total_Productos']) * 100, 1) : 0;
                                                        echo $porcentaje . '%';
                                                        ?></p>
                                                    </div>
                                                </div>
                                                <div class="progress mb-3">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: <?php echo $porcentaje; ?>%" 
                                                         aria-valuenow="<?php echo $porcentaje; ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?php echo $porcentaje; ?>%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <button type="button" id="btnContinuarConteo" class="btn btn-primary me-2">
                                    <i class="fas fa-play"></i> Continuar Conteo
                                </button>
                                <button type="button" id="btnFinalizarConteo" class="btn btn-danger me-2">
                                    <i class="fas fa-stop"></i> Finalizar Conteo
                                </button>
                                <button type="button" id="btnVerDetalles" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                            </div>
                        </div>
                        <script>
                        Swal.fire({
                            icon: 'warning',
                            title: '¡Conteo Diario en Pausa!',
                            html: `
                                <div class="text-start">
                                    <p>Ya tienes un conteo diario en pausa que debe ser finalizado antes de iniciar uno nuevo.</p>
                                    <p><strong>Opciones disponibles:</strong></p>
                                    <ul class="text-start">
                                        <li><strong>Continuar:</strong> Reanudar el conteo desde donde lo dejaste</li>
                                        <li><strong>Finalizar:</strong> Terminar el conteo actual y guardar los datos</li>
                                        <li><strong>Ver Detalles:</strong> Revisar la información del conteo pausado</li>
                                    </ul>
                                </div>
                            `,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#0172b6',
                            allowOutsideClick: false
                        });
                        </script>
                    <?php elseif($query->num_rows > 0): ?>
                        <form id="RegistraConteoDelDia" action="javascript:void(0)" method="post">
                            <div class="text-center mb-3">
                                <button type="button" id="btnPausar" class="btn btn-warning me-2">
                                    <i class="fas fa-pause"></i> Pausar
                                </button>
                                <button type="submit" id="EnviarDatos" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="StockSucursalesDistribucion" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Stock Físico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($producto = $query->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" name="CodBarra[]" 
                                                           value="<?php echo htmlspecialchars($producto['Cod_Barra']); ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="NombreProd[]" 
                                                           value="<?php echo htmlspecialchars($producto['Nombre_Prod']); ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="StockFisico[]" 
                                                           min="0" step="1" required>
                                                </td>
                                                <!-- Campos ocultos para mantener los datos -->
                                                <input type="hidden" name="Existencias_R[]" 
                                                       value="<?php echo htmlspecialchars($producto['Existencias_R']); ?>">
                                                <input type="hidden" name="Agrego[]" 
                                                       value="<?php echo htmlspecialchars($row['Nombre_Apellidos']); ?>">
                                                <input type="hidden" name="Sucursal[]" 
                                                       value="<?php echo htmlspecialchars($row['Fk_Sucursal']); ?>">
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">No se encontraron productos para realizar el conteo</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Destruir la tabla si ya existe
        if ($.fn.DataTable.isDataTable('#StockSucursalesDistribucion')) {
            $('#StockSucursalesDistribucion').DataTable().destroy();
        }

        // Inicializar DataTable
        var table = $('#StockSucursalesDistribucion').DataTable({
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

        // Mostrar mensaje de bienvenida
        Swal.fire({
            title: '¡Bienvenido al Conteo Diario!',
            html: `
                <div class="text-start">
                    <p>Si por algún motivo necesitas pausar el conteo, haz clic en el botón "Pausar".</p>
                    <p>De lo contrario, haz clic en "Guardar" para enviar tu conteo diario.</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#0172b6',
            allowOutsideClick: false
        });

        // Función para validar el formulario (solo para guardar, no para pausar)
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
                }).appendTo('#RegistraConteoDelDia');
            }

            // Mostrar mensaje de carga
            Swal.fire({
                title: enPausa ? 'Guardando y Pausando' : 'Guardando Conteo',
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
                data: $('#RegistraConteoDelDia').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: enPausa ? 'Conteo Pausado' : '¡Éxito!',
                            text: enPausa ? 
                                'El conteo se ha guardado y pausado correctamente. Podrás continuarlo más tarde.' : 
                                'El conteo se ha guardado correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#0172b6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (enPausa) {
                                    // Redirigir a la página de conteos pausados
                                    window.location.href = 'ConteosPausados.php';
                                } else {
                                    // Recargar la página para nuevo conteo
                                    window.location.reload();
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Hubo un error al guardar el conteo',
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

        // Evento del botón pausar (NO valida campos vacíos)
        $('#btnPausar').on('click', function() {
            Swal.fire({
                title: '¿Pausar el conteo?',
                text: '¿Estás seguro de que deseas pausar el conteo? Podrás continuarlo más tarde.',
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

        // Evento del formulario (guardar final, SÍ valida campos vacíos)
        $('#RegistraConteoDelDia').on('submit', function(e) {
            e.preventDefault();
            if (validarFormulario()) {
                Swal.fire({
                    title: '¿Finalizar el conteo?',
                    text: '¿Estás seguro de que deseas finalizar y guardar el conteo?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
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

        // Funciones para manejar el conteo pausado
        <?php if ($conteoPausado): ?>
        
        // Botón para continuar el conteo
        $('#btnContinuarConteo').on('click', function() {
            Swal.fire({
                title: '¿Continuar el conteo?',
                text: '¿Deseas reanudar el conteo desde donde lo dejaste?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'No, cancelar',
                confirmButtonColor: '#0172b6',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a la página de continuar conteo
                    window.location.href = 'ContinuarConteo.php';
                }
            });
        });

        // Botón para finalizar el conteo
        $('#btnFinalizarConteo').on('click', function() {
            Swal.fire({
                title: '¿Finalizar el conteo?',
                text: '¿Estás seguro de que deseas finalizar el conteo pausado? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'No, cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar carga
                    Swal.fire({
                        title: 'Finalizando conteo...',
                        text: 'Por favor espere...',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

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
                                        window.location.reload();
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

        // Botón para ver detalles del conteo
        $('#btnVerDetalles').on('click', function() {
            // Mostrar modal con detalles del conteo
            Swal.fire({
                title: 'Detalles del Conteo Pausado',
                html: `
                    <div class="text-start">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Usuario:</strong><br>
                                <?php echo htmlspecialchars($usuarioActual); ?></p>
                                <p><strong>Fecha de Creación:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($infoConteoPausado['Fecha_Creacion'])); ?></p>
                                <p><strong>Fecha de Pausa:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($infoConteoPausado['Fecha_Pausa'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Productos Contados:</strong><br>
                                <?php echo $infoConteoPausado['Productos_Contados']; ?> de <?php echo $infoConteoPausado['Total_Productos']; ?></p>
                                <p><strong>Progreso:</strong><br>
                                <?php 
                                $porcentaje = ($infoConteoPausado['Total_Productos'] > 0) ? 
                                    round(($infoConteoPausado['Productos_Contados'] / $infoConteoPausado['Total_Productos']) * 100, 1) : 0;
                                echo $porcentaje . '%';
                                ?></p>
                                <p><strong>Estado:</strong><br>
                                <span class="badge bg-warning">En Pausa</span></p>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: <?php echo $porcentaje; ?>%" 
                                 aria-valuenow="<?php echo $porcentaje; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?php echo $porcentaje; ?>%
                            </div>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#0172b6',
                width: '600px'
            });
        });

        <?php endif; ?>
    });
    </script>
</body>

</html>
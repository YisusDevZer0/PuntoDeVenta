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
                    
                    <?php if($query->num_rows > 0): ?>
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
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Existencias</th>
                                    </thead>
                                    <?php while ($producto = $query->fetch_assoc()):?>
                                        <tr>
                                            <td><input type="text" class="form-control" name="CodBarra[]" value="<?php echo htmlspecialchars($producto['Cod_Barra']); ?>" readonly></td>
                                            <td><input type="text" class="form-control" name="NombreProd[]" value="<?php echo htmlspecialchars($producto['Nombre_Prod']); ?>" readonly></td>
                                            <td style="display: none;"><input type="text" name="StockEnEseMomento[]"class="form-control" value="<?php echo htmlspecialchars($producto['Existencias_R']); ?>" readonly></td>
                                            <td style="display: none;"><input type="text" name="Agrego[]"class="form-control" value="<?php echo htmlspecialchars($row['Nombre_Apellidos']); ?>" readonly></td>
                                            <td style="display: none;"><input type="text" name="Sucursal[]"class="form-control" value="<?php echo htmlspecialchars($row['Fk_Sucursal']); ?>" readonly></td>
                                            <td><input type="number" class="form-control" name="StockFisico[]" min="0" step="1" required></td>
                                        </tr>
                                    <?php endwhile;?>
                                </table>
                            </div>
                        </form>
                    <?php else:?>
                        <p class="alert alert-warning">No se encontraron coincidencias</p>
                    <?php endif;?>
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
            "lengthMenu": [[30], [30]],
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

        // Evento del botón pausar
        $('#btnPausar').on('click', function() {
            Swal.fire({
                title: 'Conteo Pausado',
                text: 'El conteo ha sido pausado. Puedes continuar más tarde.',
                icon: 'warning',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0172b6'
            });
        });

        // Evento del formulario
        $('#RegistraConteoDelDia').on('submit', function() {
            Swal.fire({
                title: 'Guardando Conteo',
                text: 'Por favor espere mientras se guarda el conteo...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    });
    </script>
</body>

</html>
<?php
include_once "Controladores/ControladorUsuario.php";

$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-01'); // Primer día del mes en curso
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d');   // Hoy
$sucursal_id = isset($_GET['sucursal_id']) ? (int)$_GET['sucursal_id'] : 0;

$sucursales = [];
$sql_suc = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
$res_suc = mysqli_query($conn, $sql_suc);
if ($res_suc) {
    while ($r = mysqli_fetch_assoc($res_suc)) {
        $sucursales[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de ingresos - <?php echo htmlspecialchars($row['Licencia'] ?? 'Doctor Pez'); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    <style>
        #tablaHistorialIngresos th { font-size: 12px; white-space: nowrap; }
        #tablaHistorialIngresos td { font-size: 13px; }
        #tablaHistorialIngresos thead th { background-color: #0172b6 !important; color: #fff; padding: 10px 8px; }
        #tablaHistorialIngresos tbody tr:hover { background-color: #f8f9fa; }
        .swal2-container { z-index: 99999 !important; }
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
                        <div>
                            <h6 class="mb-0" style="color:#0172b6;">
                                <i class="fa-solid fa-history me-2"></i>
                                Historial de ingresos (lotes y caducidades)
                            </h6>
                            <small class="text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Por defecto se muestran los ingresos del mes en curso. Puedes filtrar por rango de fechas y sucursal.
                            </small>
                        </div>
                    </div>

                    <form method="get" class="row g-3 mb-4" id="filtroHistorial">
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Sucursal</label>
                            <select name="sucursal_id" class="form-select form-select-sm">
                                <option value="0">Todas</option>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?php echo (int)$s['ID_Sucursal']; ?>" <?php echo ($sucursal_id === (int)$s['ID_Sucursal']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['Nombre_Sucursal'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="tablaHistorialIngresos" class="table table-hover table-sm table-striped">
                            <thead>
                                <tr>
                                    <th># Factura</th>
                                    <th>Proveedor</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Sucursal</th>
                                    <th>Cant.</th>
                                    <th>Caducidad</th>
                                    <th>Lote</th>
                                    <th>Precio máx.</th>
                                    <th>Registrado por</th>
                                    <th>Fecha registro</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "Footer.php"; ?>

    <script>
    $(document).ready(function() {
        var fechaDesde = "<?php echo addslashes($fecha_desde); ?>";
        var fechaHasta = "<?php echo addslashes($fecha_hasta); ?>";
        var sucursalId = "<?php echo (int)$sucursal_id; ?>";
        var urlAjax = "Controladores/ArrayHistorialIngresosFarmacias.php?fecha_desde=" + encodeURIComponent(fechaDesde) + "&fecha_hasta=" + encodeURIComponent(fechaHasta) + "&sucursal_id=" + sucursalId;

        var tabla = $('#tablaHistorialIngresos').DataTable({
            bProcessing: true,
            bServerSide: false,
            ajax: {
                url: urlAjax,
                dataSrc: 'aaData'
            },
            columns: [
                { data: 'NumFactura' },
                { data: 'Proveedor' },
                { data: 'Cod_Barra' },
                { data: 'Nombre_Prod' },
                { data: 'Sucursal' },
                { data: 'Contabilizado', className: 'text-center' },
                { data: 'Fecha_Caducidad' },
                { data: 'Lote' },
                { data: 'PrecioMaximo', className: 'text-end' },
                { data: 'AgregadoPor' },
                { data: 'AgregadoEl' },
                { data: 'Estatus' }
            ],
            order: [[ 10, 'desc' ]],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_",
                zeroRecords: "No hay ingresos en el rango seleccionado."
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rtip",
            initComplete: function() {
                $('#loading-overlay').hide();
            }
        });
    });
    window.addEventListener('load', function() {
        $('#loading-overlay').hide();
    });
    </script>
</body>
</html>

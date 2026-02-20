<?php
$currentPage = 'ConsultarProductoOtraSucursal';
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Consultar producto en otra sucursal - <?php echo $row['Licencia']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <style>
        .sucursal-mia { background-color: rgba(0, 114, 182, 0.15); font-weight: 600; }
        .menos-que-mia { background-color: rgba(255, 193, 7, 0.2); }
        #resultados-productos table { font-size: 0.9rem; }
        #resultados-productos .badge-sucursal { font-size: 0.75rem; }
    </style>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">
                        <i class="fa-solid fa-warehouse me-2"></i>Consultar producto en otra sucursal
                    </h6>
                    <p class="text-muted small mb-3">Busque por código de barra, nombre o clave. Se mostrará el stock del producto en todas las sucursales. Resaltadas las sucursales con menos existencia que la suya.</p>
                    <div class="row mb-4">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                                <input type="text" id="busqueda-producto" class="form-control" placeholder="Código de barra, nombre o similitud..." autocomplete="off">
                                <button type="button" id="btn-buscar" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 d-flex align-items-center">
                            <label class="form-check-label me-2">
                                <input type="checkbox" id="solo-menor-que-mia" class="form-check-input"> Solo menor que mi sucursal
                            </label>
                        </div>
                    </div>
                    <div id="resultados-productos"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include "Footer.php"; ?>

    <script>
    $(function () {
        var baseUrl = 'api/consulta_producto_otras_sucursales.php';

        function buscar() {
            var q = $('#busqueda-producto').val().trim();
            var soloMenor = $('#solo-menor-que-mia').is(':checked');
            var $out = $('#resultados-productos');
            $out.html('<div class="text-center py-4"><span class="spinner-border text-primary"></span> Buscando...</div>');

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: { query: q },
                dataType: 'json'
            }).done(function (data) {
                if (!data.success) {
                    $out.html('<div class="alert alert-danger">' + (data.message || 'Error al buscar') + '</div>');
                    return;
                }
                var productos = data.productos || [];
                if (soloMenor) {
                    productos = productos.filter(function (p) { return p.menos_que_mi_sucursal; });
                }
                if (productos.length === 0) {
                    $out.html('<div class="alert alert-info">No hay resultados para "<strong>' + (data.query || '') + '</strong>".</div>');
                    return;
                }
                var sucursalActual = data.sucursal_actual || '';
                var urlTraspaso = 'TraspasosEntreSucursales';
                var html = '<div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr>' +
                    '<th>Código</th><th>Producto</th><th>Sucursal</th><th>Existencias</th><th>Acción</th></tr></thead><tbody>';
                productos.forEach(function (p) {
                    var trClass = '';
                    if (p.es_mi_sucursal) trClass = 'sucursal-mia';
                    else if (p.menos_que_mi_sucursal) trClass = 'menos-que-mia';
                    var badges = '';
                    if (p.es_mi_sucursal) badges += ' <span class="badge bg-primary badge-sucursal">Mi sucursal</span>';
                    if (p.menos_que_mi_sucursal) badges += ' <span class="badge bg-warning text-dark badge-sucursal">Menor que mi sucursal</span>';
                    var btnTraspaso = '';
                    if (!p.es_mi_sucursal) {
                        var href = urlTraspaso + '?origen=' + encodeURIComponent(p.Fk_sucursal) + '&destino=' + encodeURIComponent(sucursalActual);
                        btnTraspaso = '<a href="' + href + '" class="btn btn-sm btn-outline-primary" title="Solicitar traspaso desde esta sucursal"><i class="fa-solid fa-truck"></i> Solicitar traspaso</a>';
                    }
                    html += '<tr class="' + trClass + '">' +
                        '<td>' + (p.Cod_Barra || '-') + '</td>' +
                        '<td>' + (p.Nombre_Prod || '-') + '</td>' +
                        '<td>' + (p.Nombre_Sucursal || '-') + badges + '</td>' +
                        '<td>' + p.Existencias_R + '</td>' +
                        '<td>' + btnTraspaso + '</td></tr>';
                });
                html += '</tbody></table></div>';
                $out.html(html);
            }).fail(function () {
                $out.html('<div class="alert alert-danger">Error de conexión al buscar.</div>');
            });
        }

        $('#btn-buscar').on('click', buscar);
        $('#busqueda-producto').on('keypress', function (e) {
            if (e.which === 13) buscar();
        });
        $('#solo-menor-que-mia').on('change', function () {
            var q = $('#busqueda-producto').val().trim();
            if (q) buscar();
        });
    });
    </script>
</body>
</html>

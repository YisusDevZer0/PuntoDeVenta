$(document).ready(function() {
    // Configurar fecha por defecto (hoy)
    $('#fechaInventario').val(new Date().toISOString().split('T')[0]);
    
    // Manejar envío del formulario
    $('#formUploadInventario').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var archivo = $('#archivoInventario')[0].files[0];
        
        if (!archivo) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, seleccione un archivo de inventario.'
            });
            return;
        }
        
        // Validar extensión
        var extension = archivo.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls', 'csv'].includes(extension)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv).'
            });
            return;
        }
        
        // Mostrar loading
        mostrarCargando();
        
        // Enviar archivo
        $.ajax({
            url: 'Controladores/ProcesarComparacionInventario.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                ocultarCargando();
                
                if (response.success) {
                    mostrarResultados(response);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Ocurrió un error al procesar el archivo.'
                    });
                }
            },
            error: function(xhr, status, error) {
                ocultarCargando();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al comunicarse con el servidor: ' + error
                });
            }
        });
    });
    
    function mostrarResultados(data) {
        var resumen = data.resumen;
        var comparacion = data.comparacion;
        
        // Mostrar resumen
        var htmlResumen = `
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>${resumen.productos_en_inventario}</h3>
                            <p class="mb-0">Productos en Inventario</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>${resumen.productos_vendidos}</h3>
                            <p class="mb-0">Productos Vendidos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>${resumen.productos_con_diferencias}</h3>
                            <p class="mb-0">Con Diferencias</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>${resumen.total_diferencia.toFixed(2)}</h3>
                            <p class="mb-0">Total Diferencia</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-muted">
                    <strong>Fecha del Inventario:</strong> ${data.fecha_inventario}
                </p>
            </div>
        `;
        
        $('#resumenComparacion').html(htmlResumen);
        
        // Crear tabla de comparación
        var tablaHtml = `
            <div class="table-responsive">
                <table id="tablaComparacionData" class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código de Barras</th>
                            <th>Nombre del Producto</th>
                            <th>Existencias (Inventario)</th>
                            <th>Cantidad Vendida</th>
                            <th>Diferencia</th>
                            <th>Tickets</th>
                            <th>Total Venta</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        comparacion.forEach(function(item) {
            var claseFila = '';
            var estado = '';
            var diferenciaClass = '';
            
            if (item.tiene_diferencia) {
                if (item.diferencia > 0) {
                    claseFila = 'table-warning';
                    estado = '<span class="badge bg-warning">Sobrante</span>';
                    diferenciaClass = 'text-warning';
                } else {
                    claseFila = 'table-danger';
                    estado = '<span class="badge bg-danger">Faltante</span>';
                    diferenciaClass = 'text-danger';
                }
            } else {
                claseFila = 'table-success';
                estado = '<span class="badge bg-success">Coincide</span>';
                diferenciaClass = 'text-success';
            }
            
            tablaHtml += `
                <tr class="${claseFila}">
                    <td>${item.cod_barra}</td>
                    <td>${item.nombre_prod}</td>
                    <td>${item.existencias_inventario.toFixed(2)}</td>
                    <td>${item.cantidad_vendida.toFixed(2)}</td>
                    <td class="${diferenciaClass}"><strong>${item.diferencia > 0 ? '+' : ''}${item.diferencia.toFixed(2)}</strong></td>
                    <td>${item.total_tickets}</td>
                    <td>$${item.total_venta.toFixed(2)}</td>
                    <td>${estado}</td>
                </tr>
            `;
        });
        
        tablaHtml += `
                    </tbody>
                </table>
            </div>
        `;
        
        $('#tablaComparacion').html(tablaHtml);
        
        // Inicializar DataTable
        if ($.fn.DataTable.isDataTable('#tablaComparacionData')) {
            $('#tablaComparacionData').DataTable().destroy();
        }
        
        $('#tablaComparacionData').DataTable({
            "order": [[4, "desc"]], // Ordenar por diferencia descendente
            "pageLength": 25,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "dom": '<"d-flex justify-content-between"lf>rtip',
            "responsive": true
        });
        
        // Mostrar sección de resultados
        $('#resultadosComparacion').slideDown();
        
        // Scroll a resultados
        $('html, body').animate({
            scrollTop: $('#resultadosComparacion').offset().top - 100
        }, 500);
    }
    
    function mostrarCargando() {
        $('#loading-overlay').fadeIn();
        var mensajes = [
            "Procesando archivo de inventario...",
            "Comparando con ventas...",
            "Analizando diferencias...",
            "Generando reporte..."
        ];
        var mensaje = mensajes[Math.floor(Math.random() * mensajes.length)];
        $('#loading-text').text(mensaje);
    }
    
    function ocultarCargando() {
        $('#loading-overlay').fadeOut();
    }
});

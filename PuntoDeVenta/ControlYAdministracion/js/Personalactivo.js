// Hacer las funciones disponibles globalmente desde el inicio
window.filtrarDatos = function() {
    var tipo = $('#filtro_tipo').val();
    var sucursal = $('#filtro_sucursal').val();
    var estado = $('#filtro_estado').val();
    
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Filtrando datos...');
    
    // Recargar datos con filtros
    if (window.tablaPersonal) {
        window.tablaPersonal.ajax.reload(function() {
            $('#loading-overlay').hide();
            cargarEstadisticas();
        });
    }
};

window.limpiarFiltros = function() {
    $('#filtro_tipo').val('');
    $('#filtro_sucursal').val('');
    $('#filtro_estado').val('');
    filtrarDatos();
};

window.exportarExcel = function() {
    var tipo = $('#filtro_tipo').val();
    var sucursal = $('#filtro_sucursal').val();
    var estado = $('#filtro_estado').val();
    
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Generando archivo Excel...');
    
    var url = 'Controladores/exportar_personal_activo.php?tipo=' + encodeURIComponent(tipo) +
              '&sucursal=' + encodeURIComponent(sucursal) +
              '&estado=' + encodeURIComponent(estado);
    
    // Crear un iframe temporal para la descarga
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Ocultar loading después de un tiempo
    setTimeout(function() {
        $('#loading-overlay').hide();
        document.body.removeChild(iframe);
    }, 3000);
};

function CargaPersonalactivo(){
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Cargando datos del personal...');

    $.post("Controladores/TablaPersonalActivo.php","",function(data){
      $("#DataDeServicios").html(data);
      
      // Inicializar DataTable con configuración mejorada
      if ($.fn.DataTable.isDataTable('#Productos')) {
          $('#Productos').DataTable().destroy();
      }
      
      window.tablaPersonal = $('#Productos').DataTable({
          "bProcessing": true,
          "ordering": true,
          "stateSave": true,
          "bAutoWidth": false,
          "order": [[ 0, "desc" ]],
          "sAjaxSource": "Controladores/ArrayPersonalActivo.php",
          "fnServerData": function(sSource, aoData, fnCallback) {
              // Agregar parámetros de filtro a la petición AJAX
              var tipo = $('#filtro_tipo').val();
              var sucursal = $('#filtro_sucursal').val();
              var estado = $('#filtro_estado').val();
              
              aoData.push({ "name": "tipo", "value": tipo });
              aoData.push({ "name": "sucursal", "value": sucursal });
              aoData.push({ "name": "estado", "value": estado });
              
              $.ajax({
                  "dataType": 'json',
                  "type": "POST",
                  "url": sSource,
                  "data": aoData,
                  "success": fnCallback
              });
          },
          "aoColumns": [
              { mData: 'Idpersonal' },
              { mData: 'NombreApellidos' },
              { mData: 'Foto' },
              { mData: 'Tipousuario' },
              { mData: 'Sucursal' },
              { mData: 'CreadoEl' },
              { mData: 'Estatus' },
              { mData: 'CreadoPor' },
              { mData: 'Acciones' }
          ],
          "lengthMenu": [[10,20,50,100, -1], [10,20,50,100, "Todos"]],
          "language": {
              "lengthMenu": "Mostrar _MENU_ registros",
              "sPaginationType": "extStyle",
              "zeroRecords": "No se encontraron resultados",
              "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "sSearch": "Buscar:",
              "paginate": {
                  "first": '<i class="fas fa-angle-double-left"></i>',
                  "last": '<i class="fas fa-angle-double-right"></i>',
                  "next": '<i class="fas fa-angle-right"></i>',
                  "previous": '<i class="fas fa-angle-left"></i>'
              }
          },
          "initComplete": function() {
              // Al completar la inicialización de la tabla, ocultar el mensaje de carga
              $('#loading-overlay').hide();
              // Cargar estadísticas
              cargarEstadisticas();
          },
          "dom": '<"d-flex justify-content-between"lf>rtip',
          "responsive": true
      });
      
      // Mostrar el mensaje de carga mientras se procesan los datos
      window.tablaPersonal.on('processing.dt', function (e, settings, processing) {
          if (processing) {
              $('#loading-overlay').show();
          } else {
              $('#loading-overlay').hide();
          }
      });
    });
}

// Función para cargar estadísticas
function cargarEstadisticas() {
    var tipo = $('#filtro_tipo').val();
    var sucursal = $('#filtro_sucursal').val();
    var estado = $('#filtro_estado').val();
    
    console.log('Cargando estadísticas con filtros:', {tipo: tipo, sucursal: sucursal, estado: estado});
    
    $.post("Controladores/EstadisticasPersonalActivo.php", {
        tipo: tipo,
        sucursal: sucursal,
        estado: estado
    }, function(data) {
        console.log('Respuesta del servidor:', data);
        try {
            var stats = JSON.parse(data);
            console.log('Estadísticas parseadas:', stats);
            
            $('#totalPersonal').text(stats.totalPersonal || 0);
            $('#totalAdministrativos').text(stats.totalAdministrativos || 0);
            $('#totalSucursales').text(stats.totalSucursales || 0);
            $('#personalReciente').text(stats.personalReciente || 0);
            
            console.log('Estadísticas actualizadas en la interfaz');
        } catch (e) {
            console.error('Error al parsear estadísticas:', e);
            console.log('Respuesta del servidor (raw):', data);
            
            // Mostrar valores por defecto en caso de error
            $('#totalPersonal').text('0');
            $('#totalAdministrativos').text('0');
            $('#totalSucursales').text('0');
            $('#personalReciente').text('0');
        }
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar estadísticas:', error);
        console.log('Status:', status);
        console.log('Response:', xhr.responseText);
        
        // Mostrar valores por defecto en caso de error
        $('#totalPersonal').text('0');
        $('#totalAdministrativos').text('0');
        $('#totalSucursales').text('0');
        $('#personalReciente').text('0');
    });
}

// Función para filtrar datos
function filtrarDatos() {
    var tipo = $('#filtro_tipo').val();
    var sucursal = $('#filtro_sucursal').val();
    var estado = $('#filtro_estado').val();
    
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Filtrando datos...');
    
    // Recargar datos con filtros
    if (window.tablaPersonal) {
        window.tablaPersonal.ajax.reload(function() {
            $('#loading-overlay').hide();
            cargarEstadisticas();
        });
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    $('#filtro_tipo').val('');
    $('#filtro_sucursal').val('');
    $('#filtro_estado').val('');
    filtrarDatos();
}

// Función para exportar a Excel
function exportarExcel() {
    var tipo = $('#filtro_tipo').val();
    var sucursal = $('#filtro_sucursal').val();
    var estado = $('#filtro_estado').val();
    
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Generando archivo Excel...');
    
    var url = 'Controladores/exportar_personal_activo.php?tipo=' + encodeURIComponent(tipo) +
              '&sucursal=' + encodeURIComponent(sucursal) +
              '&estado=' + encodeURIComponent(estado);
    
    // Crear un iframe temporal para la descarga
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Ocultar loading después de un tiempo
    setTimeout(function() {
        $('#loading-overlay').hide();
        document.body.removeChild(iframe);
    }, 3000);
}

CargaPersonalactivo();
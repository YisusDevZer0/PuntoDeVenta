$(document).ready(function() {
    console.log("Inicializando DataTable para personal...");
    
    // Inicializar DataTable
    var table = $('#tablaPersonal').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "Controladores/ArrayUsuariosVigentes.php",
            "type": "GET",
            "dataSrc": function(json) {
                console.log("Respuesta del servidor:", json);
                
                // Ocultar loading
                $('#loading-overlay').hide();
                
                // Verificar si hay error en la respuesta
                if (json.error) {
                    console.error("Error del servidor:", json.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los datos: ' + json.error
                    });
                    return [];
                }
                
                // Actualizar estadísticas
                actualizarEstadisticas(json.aaData);
                
                return json.aaData || [];
            },
            "error": function(xhr, status, error) {
                console.error("Error AJAX:", xhr, status, error);
                console.error("URL llamada:", "Controladores/ArrayUsuariosVigentes.php");
                $('#loading-overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo cargar los datos. Error: ' + error
                });
            }
        },
        "columns": [
            {"data": "Idpersonal"},
            {
                "data": "Foto",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return data; // Ya viene formateado como HTML
                    }
                    return data;
                }
            },
            {"data": "NombreApellidos"},
            {"data": "Tipousuario"},
            {"data": "Sucursal"},
            {"data": "CorreoElectronico"},
            {"data": "Telefono"},
            {"data": "FechaNacimiento"},
            {"data": "CreadoEl"},
            {"data": "Estatus"},
            {"data": "CreadoPor"},
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group" role="group">' +
                           '<button type="button" class="btn btn-success btn-sm btn-edita" data-id="' + row.Idpersonal + '" style="background-color: #0172b6 !important; margin-right: 5px;"><i class="fa-solid fa-pen-to-square"></i></button>' +
                           '<button type="button" class="btn btn-danger btn-sm btn-elimina" data-id="' + row.Idpersonal + '" style="background-color: #ff3131 !important;"><i class="fa-solid fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "order": [[0, "desc"]], // Ordenar por ID descendente
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "error": function(xhr, error, thrown) {
            console.error("Error en DataTables:", error);
            $('#loading-overlay').hide();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los datos. Por favor, verifica la conexión.'
            });
        },
        "initComplete": function() {
            console.log("DataTable inicializado completamente");
            $('#loading-overlay').hide();
        }
    });

    // Función para actualizar estadísticas
    function actualizarEstadisticas(data) {
        if (data && data.length > 0) {
            var totalPersonal = data.length;
            var personalActivo = 0;
            var sucursalesUnicas = new Set();
            var tiposUsuarioUnicos = new Set();
            
            data.forEach(function(item) {
                if (item.Estatus === 'Activo') {
                    personalActivo++;
                }
                if (item.Sucursal) {
                    sucursalesUnicas.add(item.Sucursal);
                }
                if (item.Tipousuario) {
                    tiposUsuarioUnicos.add(item.Tipousuario);
                }
            });
            
            $('#totalPersonal').text(totalPersonal);
            $('#personalActivo').text(personalActivo);
            $('#sucursalesActivas').text(sucursalesUnicas.size);
            $('#tiposUsuario').text(tiposUsuarioUnicos.size);
            
            $('#statsRow').show();
        }
    }

    // Función para filtrar datos con loading
    window.filtrarDatos = function() {
        console.log("Ejecutando filtro...");
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Filtrando datos...');
        
        // Recargar datos
        table.ajax.reload(function() {
            $('#loading-overlay').hide();
            console.log("Filtro completado");
        });
    };

    // Función para exportar a Excel
    window.exportarExcel = function() {
        var tipo_usuario = $('#tipo_usuario').val();
        var sucursal = $('#sucursal').val();
        var estatus = $('#estatus').val();
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Generando archivo Excel...');
        
        var url = 'Controladores/exportar_reporte_personal.php?tipo_usuario=' + tipo_usuario +
                  '&sucursal=' + sucursal +
                  '&estatus=' + estatus;
        
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
});
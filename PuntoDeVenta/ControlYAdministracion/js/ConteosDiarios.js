// Variables globales para paginación
let paginaActual = 1;
let registrosPorPagina = 10;

function CargarConteosDiarios(pagina = 1) {
    paginaActual = pagina;
    
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        estado: $('#filtroEstado').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        pagina: paginaActual,
        registros: registrosPorPagina
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataConteosDiarios.php", 
        filtros, 
        function(data) {
            $("#DataConteosDiarios").html(data);
            // Cargar estadísticas después de cargar los datos
            CargarEstadisticasResumen();
        })
        .fail(function() {
            $("#DataConteosDiarios").html('<div class="alert alert-danger">Error al cargar los datos de conteos diarios</div>');
        });
}

function CargarSucursales() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataSucursales.php", 
        {}, 
        function(data) {
            $("#filtroSucursal").html(data);
        })
        .fail(function() {
            console.error('Error al cargar sucursales');
        });
}

// Cargar datos al inicializar
$(document).ready(function() {
    CargarSucursales();
    CargarConteosDiarios();
});

// Función para exportar datos a Excel
function ExportarConteosDiarios() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        estado: $('#filtroEstado').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        exportar: true
    };

    // Crear URL con parámetros
    const params = new URLSearchParams(filtros);
    const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarConteosDiarios.php?${params.toString()}`;
    
    // Abrir en nueva ventana para descargar
    window.open(url, '_blank');
}

// Función para imprimir reporte
function ImprimirConteosDiarios() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        estado: $('#filtroEstado').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        imprimir: true
    };

    // Crear URL con parámetros
    const params = new URLSearchParams(filtros);
    const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ImprimirConteosDiarios.php?${params.toString()}`;
    
    // Abrir en nueva ventana para imprimir
    window.open(url, '_blank');
}

// Función para limpiar filtros
function LimpiarFiltros() {
    $('#filtroSucursal').val('');
    $('#filtroEstado').val('');
    $('#fechaDesde').val('');
    $('#fechaHasta').val(new Date().toISOString().split('T')[0]);
    paginaActual = 1;
    CargarConteosDiarios();
}

// Funciones de paginación
function CambiarPagina(pagina) {
    CargarConteosDiarios(pagina);
}

function CambiarRegistrosPorPagina(registros) {
    registrosPorPagina = parseInt(registros);
    paginaActual = 1;
    CargarConteosDiarios();
}

// Función para ir a la primera página
function PrimeraPagina() {
    CargarConteosDiarios(1);
}

// Función para ir a la última página
function UltimaPagina() {
    // Esta función se actualizará dinámicamente cuando se carguen los datos
    const ultimaPagina = parseInt($('#totalPaginas').text()) || 1;
    CargarConteosDiarios(ultimaPagina);
}

// Función para mostrar estadísticas
function MostrarEstadisticas() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        estado: $('#filtroEstado').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EstadisticasConteosDiarios.php", 
        filtros, 
        function(data) {
            if (data.success) {
                // Mostrar estadísticas en un modal o alerta
                Swal.fire({
                    title: 'Estadísticas de Conteos Diarios',
                    html: `
                        <div class="row text-center">
                            <div class="col-6">
                                <h4>${data.totalConteos}</h4>
                                <p>Total Conteos</p>
                            </div>
                            <div class="col-6">
                                <h4>${data.conteosCompletados}</h4>
                                <p>Completados</p>
                            </div>
                            <div class="col-6">
                                <h4>${data.conteosPausados}</h4>
                                <p>En Pausa</p>
                            </div>
                            <div class="col-6">
                                <h4>${data.diferenciaPromedio}%</h4>
                                <p>Diferencia Promedio</p>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    showConfirmButton: true
                });
            }
        }, 'json')
        .fail(function() {
            Swal.fire('Error', 'No se pudieron cargar las estadísticas', 'error');
        });
}

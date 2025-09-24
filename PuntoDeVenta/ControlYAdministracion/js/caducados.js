/**
 * JavaScript para el módulo de Control de Caducados
 * Doctor Pez - Sistema de Punto de Venta
 */

// Variables globales
let productosCaducados = [];
let estadisticas = {};

/**
 * Cargar estadísticas del dashboard
 */
function cargarEstadisticas() {
    fetch('api/productos_proximos_caducar.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                estadisticas = data.estadisticas;
                
                // Actualizar contadores
                document.getElementById('contador-3-meses').textContent = data.estadisticas.alerta_3_meses || 0;
                document.getElementById('contador-6-meses').textContent = data.estadisticas.alerta_6_meses || 0;
                document.getElementById('contador-9-meses').textContent = data.estadisticas.alerta_9_meses || 0;
                document.getElementById('contador-total').textContent = data.estadisticas.total || 0;
                
                // Actualizar colores de las tarjetas según la cantidad
                actualizarColoresTarjetas();
            }
        })
        .catch(error => {
            console.error('Error al cargar estadísticas:', error);
            mostrarError('Error al cargar las estadísticas');
        });
}

/**
 * Actualizar colores de las tarjetas según la cantidad
 */
function actualizarColoresTarjetas() {
    const tarjeta3Meses = document.querySelector('#contador-3-meses').closest('.card');
    const tarjeta6Meses = document.querySelector('#contador-6-meses').closest('.card');
    const tarjeta9Meses = document.querySelector('#contador-9-meses').closest('.card');
    
    // 3 meses - amarillo si hay productos
    if (estadisticas.alerta_3_meses > 0) {
        tarjeta3Meses.classList.add('border-warning');
    }
    
    // 6 meses - naranja si hay productos
    if (estadisticas.alerta_6_meses > 0) {
        tarjeta6Meses.classList.add('border-danger');
    }
    
    // 9 meses - azul si hay productos
    if (estadisticas.alerta_9_meses > 0) {
        tarjeta9Meses.classList.add('border-info');
    }
}

/**
 * Cargar sucursales en el filtro
 */
function cargarSucursales() {
    fetch('api/obtener_sucursales.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('filtro-sucursal');
            select.innerHTML = '<option value="">Todas las sucursales</option>';
            
            data.sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.id;
                option.textContent = sucursal.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
}

/**
 * Cargar productos próximos a caducar
 */
function cargarProductosCaducados() {
    const filtros = obtenerFiltros();
    
    let url = 'api/productos_proximos_caducar.php?';
    const params = new URLSearchParams();
    
    if (filtros.sucursal) params.append('sucursal', filtros.sucursal);
    if (filtros.estado) params.append('estado', filtros.estado);
    if (filtros.tipo_alerta) params.append('tipo_alerta', filtros.tipo_alerta);
    
    url += params.toString();
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productosCaducados = data.productos;
                mostrarProductosEnTabla(data.productos);
            } else {
                mostrarError('Error al cargar los productos');
            }
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
            mostrarError('Error al cargar los productos');
        });
}

/**
 * Obtener filtros aplicados
 */
function obtenerFiltros() {
    return {
        sucursal: document.getElementById('filtro-sucursal').value,
        estado: document.getElementById('filtro-estado').value,
        tipo_alerta: document.getElementById('filtro-alerta').value
    };
}

/**
 * Aplicar filtros
 */
function aplicarFiltros() {
    cargarProductosCaducados();
}

/**
 * Mostrar productos en la tabla
 */
function mostrarProductosEnTabla(productos) {
    const tbody = document.getElementById('tbody-caducados');
    tbody.innerHTML = '';
    
    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                    No hay productos que coincidan con los filtros
                </td>
            </tr>
        `;
        return;
    }
    
    productos.forEach(producto => {
        const row = document.createElement('tr');
        row.innerHTML = generarFilaProducto(producto);
        tbody.appendChild(row);
    });
}

/**
 * Generar fila de producto para la tabla
 */
function generarFilaProducto(producto) {
    const estadoBadge = generarBadgeEstado(producto.estado);
    const alertaBadge = generarBadgeAlerta(producto.tipo_alerta, producto.dias_restantes);
    const acciones = generarBotonesAccion(producto);
    
    return `
        <td><code>${producto.cod_barra}</code></td>
        <td>
            <div class="fw-bold">${producto.nombre_producto}</div>
            <small class="text-muted">${producto.proveedor || 'Sin proveedor'}</small>
        </td>
        <td><span class="badge bg-secondary">${producto.lote}</span></td>
        <td>
            <div class="fw-bold ${producto.dias_restantes < 0 ? 'text-danger' : ''}">${producto.fecha_caducidad}</div>
            <small class="text-muted">${producto.dias_restantes} días</small>
        </td>
        <td>
            <span class="badge bg-primary">${producto.cantidad_actual}</span>
        </td>
        <td>${producto.sucursal}</td>
        <td>${estadoBadge}</td>
        <td>${alertaBadge}</td>
        <td>${acciones}</td>
    `;
}

/**
 * Generar badge de estado
 */
function generarBadgeEstado(estado) {
    const estados = {
        'activo': { class: 'bg-success', text: 'Activo' },
        'agotado': { class: 'bg-warning', text: 'Agotado' },
        'vencido': { class: 'bg-danger', text: 'Vencido' },
        'retirado': { class: 'bg-secondary', text: 'Retirado' }
    };
    
    const estadoInfo = estados[estado] || { class: 'bg-secondary', text: estado };
    return `<span class="badge ${estadoInfo.class}">${estadoInfo.text}</span>`;
}

/**
 * Generar badge de alerta
 */
function generarBadgeAlerta(tipoAlerta, diasRestantes) {
    if (diasRestantes < 0) {
        return '<span class="badge bg-danger">VENCIDO</span>';
    }
    
    const alertas = {
        '3_meses': { class: 'bg-warning', text: '3 MESES' },
        '6_meses': { class: 'bg-danger', text: '6 MESES' },
        '9_meses': { class: 'bg-info', text: '9 MESES' },
        'normal': { class: 'bg-success', text: 'NORMAL' }
    };
    
    const alertaInfo = alertas[tipoAlerta] || { class: 'bg-secondary', text: tipoAlerta };
    return `<span class="badge ${alertaInfo.class}">${alertaInfo.text}</span>`;
}

/**
 * Generar botones de acción
 */
function generarBotonesAccion(producto) {
    return `
        <div class="btn-group" role="group">
            <button class="btn btn-sm btn-outline-info" onclick="abrirModalDetallesLote(${producto.id_lote})" title="Ver detalles">
                <i class="fa-solid fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning" onclick="abrirModalActualizarCaducidad(${producto.id_lote}, ${JSON.stringify(producto).replace(/"/g, '&quot;')})" title="Actualizar fecha">
                <i class="fa-solid fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="abrirModalTransferirLote(${producto.id_lote}, ${JSON.stringify(producto).replace(/"/g, '&quot;')})" title="Transferir">
                <i class="fa-solid fa-truck"></i>
            </button>
        </div>
    `;
}

/**
 * Mostrar error con SweetAlert
 */
function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Aceptar'
    });
}

/**
 * Mostrar éxito con SweetAlert
 */
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        confirmButtonText: 'Aceptar'
    });
}

/**
 * Formatear fecha para mostrar
 */
function formatearFecha(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

/**
 * Formatear número con separadores de miles
 */
function formatearNumero(numero) {
    return new Intl.NumberFormat('es-ES').format(numero);
}

/**
 * Validar formulario
 */
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    const requiredFields = form.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
        }
    }
    
    return true;
}

/**
 * Limpiar formulario
 */
function limpiarFormulario(formId) {
    const form = document.getElementById(formId);
    form.reset();
    
    // Limpiar clases de validación
    const fields = form.querySelectorAll('.is-invalid, .is-valid');
    fields.forEach(field => {
        field.classList.remove('is-invalid', 'is-valid');
    });
}

/**
 * Mostrar loading
 */
function mostrarLoading(titulo = 'Cargando...') {
    Swal.fire({
        title: titulo,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Cerrar loading
 */
function cerrarLoading() {
    Swal.close();
}

/**
 * Confirmar acción
 */
function confirmarAccion(titulo, texto, callback) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

/**
 * Exportar datos a Excel
 */
function exportarExcel() {
    mostrarLoading('Generando reporte...');
    
    const filtros = obtenerFiltros();
    let url = 'api/reporte_caducados.php?formato=excel';
    
    if (filtros.sucursal) url += `&sucursal=${filtros.sucursal}`;
    if (filtros.estado) url += `&estado=${filtros.estado}`;
    if (filtros.tipo_alerta) url += `&tipo_alerta=${filtros.tipo_alerta}`;
    
    // Crear enlace de descarga
    const link = document.createElement('a');
    link.href = url;
    link.download = `reporte_caducados_${new Date().toISOString().split('T')[0]}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    cerrarLoading();
    mostrarExito('Reporte generado exitosamente');
}

/**
 * Actualizar página
 */
function actualizarPagina() {
    cargarEstadisticas();
    cargarProductosCaducados();
}

/**
 * Inicializar eventos
 */
function inicializarEventos() {
    // Event listeners para filtros
    document.getElementById('filtro-sucursal').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-estado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-alerta').addEventListener('change', aplicarFiltros);
    
    // Event listener para actualizar automáticamente cada 5 minutos
    setInterval(actualizarPagina, 300000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    inicializarEventos();
    actualizarPagina();
});

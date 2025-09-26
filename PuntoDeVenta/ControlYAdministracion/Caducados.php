<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Control de Caducados - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Control de Caducados - <?php echo $row['Licencia']?></h6>
            
            <div id="DataDeCaducados"></div>
            </div></div></div></div>
            
          
<script>
// Funciones globales para los botones de la tabla
function abrirModalDetallesLote(idLote) {
    console.log('Abriendo modal detalles para lote:', idLote);
    
    // Mostrar modal directamente
    var myModal = new bootstrap.Modal(document.getElementById('modalDetallesLote'));
    myModal.show();
    
    // Cargar datos después de abrir el modal
    setTimeout(() => {
        $.get(`api/obtener_detalles_lote.php?id=${idLote}`, function(data) {
            if (data.success) {
                mostrarDetallesLote(data.lote);
                mostrarHistorialLote(data.historial);
            } else {
                console.error('Error al cargar detalles:', data.error);
            }
        }).fail(function() {
            console.error('Error de conexión al cargar detalles');
        });
    }, 300);
}

function abrirModalActualizarCaducidad(idLote, datosLote) {
    console.log('Abriendo modal actualizar para lote:', idLote, datosLote);
    
    try {
        const datos = JSON.parse(datosLote);
        
        // Llenar información del lote
        document.getElementById('idLoteActualizar').value = idLote;
        document.getElementById('infoLoteActual').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Producto:</strong> ${datos.nombre_producto}<br>
                    <strong>Código:</strong> ${datos.cod_barra}<br>
                    <strong>Lote:</strong> ${datos.lote}
                </div>
                <div class="col-md-6">
                    <strong>Fecha Actual:</strong> ${datos.fecha_caducidad}<br>
                    <strong>Cantidad:</strong> ${datos.cantidad_actual}<br>
                    <strong>Sucursal:</strong> ${datos.sucursal}
                </div>
            </div>
        `;
        
        // Establecer fecha actual como valor por defecto
        document.getElementById('fechaCaducidadNueva').value = datos.fecha_caducidad;
        
        // Limpiar otros campos
        document.getElementById('motivoActualizacion').value = '';
        document.getElementById('observacionesActualizacion').value = '';
        
    } catch (error) {
        console.error('Error al procesar datos:', error);
    }
    
    // Mostrar modal
    var myModal = new bootstrap.Modal(document.getElementById('modalActualizarCaducidad'));
    myModal.show();
}

function abrirModalTransferirLote(idLote, datosLote) {
    console.log('Abriendo modal transferir para lote:', idLote, datosLote);
    
    try {
        const datos = JSON.parse(datosLote);
        
        // Llenar información del lote origen
        document.getElementById('idLoteTransferir').value = idLote;
        document.getElementById('infoLoteOrigen').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Producto:</strong> ${datos.nombre_producto}<br>
                    <strong>Código:</strong> ${datos.cod_barra}<br>
                    <strong>Lote:</strong> ${datos.lote}
                </div>
                <div class="col-md-6">
                    <strong>Fecha Caducidad:</strong> ${datos.fecha_caducidad}<br>
                    <strong>Cantidad Disponible:</strong> ${datos.cantidad_actual}<br>
                    <strong>Sucursal Actual:</strong> ${datos.sucursal}
                </div>
            </div>
        `;
        
        // Establecer cantidad máxima
        document.getElementById('cantidadDisponible').textContent = datos.cantidad_actual;
        document.getElementById('cantidadTransferir').max = datos.cantidad_actual;
        document.getElementById('cantidadTransferir').value = 1;
        
        // Cargar sucursales destino (excluyendo la actual)
        cargarSucursalesDestino(datos.sucursal_id || 1);
        
        // Limpiar otros campos
        document.getElementById('motivoTransferencia').value = '';
        document.getElementById('observacionesTransferencia').value = '';
        
    } catch (error) {
        console.error('Error al procesar datos:', error);
    }
    
    // Mostrar modal
    var myModal = new bootstrap.Modal(document.getElementById('modalTransferirLote'));
    myModal.show();
}

function mostrarDetallesLote(lote) {
    console.log('Mostrando detalles del lote:', lote);
    
    // Llenar información del lote
    const detallesHtml = `
        <div class="col-md-6">
            <strong>Código de Barra:</strong> ${lote.cod_barra}<br>
            <strong>Producto:</strong> ${lote.nombre_producto}<br>
            <strong>Lote:</strong> ${lote.lote}<br>
            <strong>Fecha de Caducidad:</strong> ${lote.fecha_caducidad}<br>
            <strong>Fecha de Ingreso:</strong> ${lote.fecha_registro}
        </div>
        <div class="col-md-6">
            <strong>Cantidad Inicial:</strong> ${lote.cantidad_inicial}<br>
            <strong>Cantidad Actual:</strong> ${lote.cantidad_actual}<br>
            <strong>Sucursal:</strong> ${lote.sucursal}<br>
            <strong>Proveedor:</strong> ${lote.proveedor || 'Sin proveedor'}<br>
            <strong>Precio Compra:</strong> ${lote.precio_compra ? '$' + lote.precio_compra : 'No especificado'}<br>
            <strong>Precio Venta:</strong> $${lote.precio_venta}
        </div>
    `;
    
    document.getElementById('detallesLoteInfo').innerHTML = detallesHtml;
    
    // Mostrar estado
    const estadoHtml = `
        <div class="mb-2">
            <strong>Estado:</strong> <span class="badge bg-success">${lote.estado}</span>
        </div>
        <div class="mb-2">
            <strong>Días Restantes:</strong> ${lote.dias_restantes} días
        </div>
        <div class="mb-2">
            <strong>Observaciones:</strong> ${lote.observaciones || 'Sin observaciones'}
        </div>
    `;
    
    document.getElementById('estadoLote').innerHTML = estadoHtml;
}

function mostrarHistorialLote(historial) {
    console.log('Mostrando historial:', historial);
    
    const tbody = document.getElementById('historialLote');
    tbody.innerHTML = '';
    
    if (historial.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay movimientos registrados</td></tr>';
        return;
    }
    
    historial.forEach(movimiento => {
        const row = `
            <tr>
                <td>${movimiento.fecha_movimiento}</td>
                <td><span class="badge bg-info">${movimiento.tipo_movimiento}</span></td>
                <td>
                    ${movimiento.cantidad_anterior !== null ? `Cantidad: ${movimiento.cantidad_anterior} → ${movimiento.cantidad_nueva}` : ''}
                    ${movimiento.fecha_caducidad_anterior ? `Fecha: ${movimiento.fecha_caducidad_anterior} → ${movimiento.fecha_caducidad_nueva}` : ''}
                </td>
                <td>${movimiento.usuario_movimiento}</td>
                <td>${movimiento.observaciones || '-'}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function cargarSucursalesDestino(sucursalOrigen) {
    $.get("api/obtener_sucursales.php", function(data) {
        if (data.success) {
            const select = document.getElementById('sucursalDestino');
            select.innerHTML = '<option value="">Seleccionar sucursal destino</option>';
            
            data.sucursales.forEach(sucursal => {
                if (sucursal.id != sucursalOrigen) {
                    const option = document.createElement('option');
                    option.value = sucursal.id;
                    option.textContent = sucursal.nombre;
                    select.appendChild(option);
                }
            });
        }
    });
}

// Función para guardar actualización de caducidad
function guardarActualizacionCaducidad() {
    const idLote = document.getElementById('idLoteActualizar').value;
    const fechaNueva = document.getElementById('fechaCaducidadNueva').value;
    const motivo = document.getElementById('motivoActualizacion').value;
    const observaciones = document.getElementById('observacionesActualizacion').value;
    
    if (!fechaNueva || !motivo) {
        Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    const data = {
        id_lote: idLote,
        fecha_caducidad_nueva: fechaNueva,
        motivo: motivo,
        observaciones: observaciones,
        usuario_movimiento: 1 // ID del usuario actual
    };
    
    fetch('api/actualizar_caducidad.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', data.message, 'success');
            // Cerrar modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarCaducidad'));
            modal.hide();
            // Recargar tabla
            if (typeof tabla !== 'undefined') {
                tabla.ajax.reload();
            }
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}

// Función para guardar transferencia de lote
function guardarTransferencia() {
    const idLote = document.getElementById('idLoteTransferir').value;
    const sucursalDestino = document.getElementById('sucursalDestino').value;
    const cantidad = document.getElementById('cantidadTransferir').value;
    const motivo = document.getElementById('motivoTransferencia').value;
    const observaciones = document.getElementById('observacionesTransferencia').value;
    
    if (!sucursalDestino || !cantidad || !motivo) {
        Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    const data = {
        id_lote: idLote,
        sucursal_destino: sucursalDestino,
        cantidad_transferir: parseInt(cantidad),
        motivo: motivo,
        observaciones: observaciones,
        usuario_movimiento: 1 // ID del usuario actual
    };
    
    fetch('api/transferir_lote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', data.message, 'success');
            // Cerrar modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalTransferirLote'));
            modal.hide();
            // Recargar tabla
            if (typeof tabla !== 'undefined') {
                tabla.ajax.reload();
            }
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}
</script>
<script src="js/ControlDeCaducados.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/RegistrarLote.php";
            include "Modales/ActualizarCaducidad.php";
            include "Modales/TransferirLote.php";
            include "Modales/DetallesLote.php";
            include "Modales/ConfiguracionCaducados.php";
            include "Modales/Modales_Errores.php";
            include "footer.php";?>

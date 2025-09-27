function CargaCaducados(){
    $.get("Controladores/DataCaducados","",function(data){
      $("#DataDeCaducados").html(data);
    })
}

// Función para abrir modal de registrar lote
function abrirModalRegistrarLote() {
    // Limpiar formulario
    document.getElementById('formRegistrarLote').reset();
    document.getElementById('infoProducto').style.display = 'none';
    
    // Cargar sucursales
    cargarSucursalesModal();
    
    // Mostrar modal usando jQuery
    $('#modalRegistrarLote').modal('show');
}

// Función para cargar sucursales en el modal
function cargarSucursalesModal() {
    fetch('api/obtener_sucursales.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sucursal');
            select.innerHTML = '<option value="">Seleccionar sucursal</option>';
            
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

// Función para buscar producto
function buscarProducto() {
    const codigoBarra = document.getElementById('codigoBarra').value;
    const sucursal = document.getElementById('sucursal').value;
    
    if (!codigoBarra || !sucursal) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor ingresa el código de barras y selecciona la sucursal'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Buscando producto...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`api/buscar_producto.php?codigo=${codigoBarra}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Mostrar información del producto
                document.getElementById('detallesProducto').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre:</strong> ${data.producto.nombre_producto}<br>
                            <strong>Código:</strong> ${data.producto.cod_barra}
                        </div>
                        <div class="col-md-6">
                            <strong>Precio Venta:</strong> $${data.producto.precio_venta}<br>
                            <strong>Precio Compra:</strong> $${data.producto.precio_compra}
                        </div>
                    </div>
                `;
                document.getElementById('infoProducto').style.display = 'block';
                document.getElementById('precioVenta').value = data.producto.precio_venta;
                document.getElementById('precioCompra').value = data.producto.precio_compra;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto no encontrado',
                    text: data.error
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al buscar el producto: ' + error.message
            });
        });
}

// Función para guardar lote
function guardarLote() {
    const form = document.getElementById('formRegistrarLote');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const requiredFields = ['codigoBarra', 'sucursal', 'lote', 'fechaCaducidad', 'cantidad'];
    for (let field of requiredFields) {
        if (!formData.get(field)) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: `Por favor completa el campo: ${field}`
            });
            return;
        }
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Registrando lote...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const datos = {
        codigoBarra: formData.get('codigoBarra'),
        sucursal: formData.get('sucursal'),
        lote: formData.get('lote'),
        fechaCaducidad: formData.get('fechaCaducidad'),
        cantidad: formData.get('cantidad'),
        proveedor: formData.get('proveedor'),
        precioCompra: formData.get('precioCompra'),
        observaciones: formData.get('observaciones'),
        usuarioRegistro: 1
    };
    
    // Crear FormData para envío
    const formDataToSend = new FormData();
    formDataToSend.append('codigoBarra', datos.codigoBarra);
    formDataToSend.append('sucursal', datos.sucursal);
    formDataToSend.append('lote', datos.lote);
    formDataToSend.append('fechaCaducidad', datos.fechaCaducidad);
    formDataToSend.append('cantidad', datos.cantidad);
    formDataToSend.append('proveedor', datos.proveedor);
    formDataToSend.append('precioCompra', datos.precioCompra);
    formDataToSend.append('observaciones', datos.observaciones);
    formDataToSend.append('usuarioRegistro', datos.usuarioRegistro);
    
    fetch('api/registrar_lote.php', {
        method: 'POST',
        body: formDataToSend
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error de red: ' + response.status);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', text);
                throw new Error('Error al procesar la respuesta del servidor');
            }
        });
    })
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Lote registrado',
                text: data.message
            }).then(() => {
                // Cerrar modal y recargar datos
                $('#modalRegistrarLote').modal('hide');
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
                cargarEstadisticas();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al registrar el lote: ' + error.message
        });
    });
}

// Función para cargar estadísticas
function cargarEstadisticas() {
    $.get("api/productos_proximos_caducar.php", function(data) {
        if (data.success) {
            $("#contador-3-meses").text(data.estadisticas.alerta_3_meses || 0);
            $("#contador-6-meses").text(data.estadisticas.alerta_6_meses || 0);
            $("#contador-9-meses").text(data.estadisticas.alerta_9_meses || 0);
            $("#contador-total").text(data.estadisticas.total || 0);
        }
    });
}

CargaCaducados();
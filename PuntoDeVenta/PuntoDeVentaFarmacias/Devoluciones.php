<?php
include_once "Controladores/ControladorUsuario.php";

// Obtener datos del usuario
$usuario_id = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1;
$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;
$tipo_usuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar permisos
$isAdmin = ($tipo_usuario == 'Administrador' || $tipo_usuario == 'MKT');

// Función para obtener tipos de devolución
function getTiposDevolucion() {
    return [
        'no_facturado' => 'Producto no facturado',
        'danado_recibir' => 'Producto dañado al recibir',
        'proximo_caducar' => 'Próximo a caducar',
        'caducado' => 'Producto caducado',
        'danado_roto' => 'Producto dañado/roto',
        'solicitado_admin' => 'Solicitado por administración',
        'error_etiquetado' => 'Error en etiquetado',
        'defectuoso' => 'Producto defectuoso',
        'sobrante' => 'Sobrante de inventario',
        'otro' => 'Otro motivo'
    ];
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'procesar_devolucion':
            $productos_json = $_POST['productos'] ?? '';
            if (empty($productos_json)) {
                $response['message'] = 'No hay productos para procesar';
                echo json_encode($response);
                exit;
            }
            
            $productos = json_decode($productos_json, true);
            if (!$productos || count($productos) === 0) {
                $response['message'] = 'No hay productos válidos para procesar';
                echo json_encode($response);
                exit;
            }
            
            // Generar folio único
            $folio_devolucion = 'DEV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Crear registro de devolución
                $sql = "INSERT INTO Devoluciones (folio, sucursal_id, usuario_id, fecha, estatus, observaciones_generales) 
                        VALUES (?, ?, ?, NOW(), 'procesada', ?)";
                $stmt = $conn->prepare($sql);
                $observaciones_generales = $_POST['observaciones_generales'] ?? '';
                $stmt->bind_param("siis", $folio_devolucion, $sucursal_id, $usuario_id, $observaciones_generales);
                $stmt->execute();
                $devolucion_id = $conn->insert_id;
                
                // Procesar cada producto
                foreach ($productos as $item) {
                    $producto = $item['producto'];
                    
                    // Insertar detalle de devolución
                    $sql = "INSERT INTO Devoluciones_Detalle 
                            (devolucion_id, producto_id, codigo_barras, nombre_producto, cantidad, 
                             tipo_devolucion, observaciones, lote, fecha_caducidad, precio_venta) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iississssd", 
                        $devolucion_id, 
                        $producto['id'], 
                        $item['codigo_barras'], 
                        $producto['nombre'], 
                        $item['cantidad'], 
                        $item['tipo_devolucion'], 
                        $item['observaciones'], 
                        $producto['lote'], 
                        $producto['fecha_caducidad'], 
                        $producto['precio_venta']
                    );
                    $stmt->execute();
                    
                    // Actualizar stock (reducir existencias)
                    $sql = "UPDATE Stock_POS 
                            SET Existencias_R = Existencias_R - ? 
                            WHERE ID_Prod_POS = ? AND Fk_sucursal = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $item['cantidad'], $producto['id'], $sucursal_id);
                    $stmt->execute();
                    
                    // Registrar en log de stock
                    $sql = "INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Mensaje) 
                            VALUES (?, ?, ?, 'Devolucion', ?)";
                    $stmt = $conn->prepare($sql);
                    $mensaje = "Devolución: {$item['tipo_devolucion']} - {$item['observaciones']}";
                    $stmt->bind_param("siis", $item['codigo_barras'], $sucursal_id, $item['cantidad'], $mensaje);
                    $stmt->execute();
                }
                
                $conn->commit();
                $response['success'] = true;
                $response['message'] = 'Devolución procesada correctamente. Folio: ' . $folio_devolucion;
                $response['folio'] = $folio_devolucion;
                
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = 'Error al procesar devolución: ' . $e->getMessage();
            }
            break;
            
        case 'obtener_devoluciones':
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $estatus = $_POST['estatus'] ?? null;
            
            $devoluciones = getDevoluciones($sucursal_id, $fecha_inicio, $fecha_fin, $estatus);
            $response['success'] = true;
            $response['devoluciones'] = $devoluciones;
            break;
            
        case 'obtener_detalles':
            $devolucion_id = intval($_POST['devolucion_id']);
            $devolucion = getDevolucionById($devolucion_id);
            $detalles = getDevolucionDetalle($devolucion_id);
            
            if ($devolucion) {
                $response['success'] = true;
                $response['devolucion'] = $devolucion;
                $response['detalles'] = $detalles;
            } else {
                $response['message'] = 'Devolución no encontrada';
            }
            break;
    }
    
    echo json_encode($response);
    exit;
}

// Obtener devoluciones para listado
function getDevoluciones($sucursal_id, $fecha_inicio = null, $fecha_fin = null, $estatus = null) {
    global $conn;
    
    $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($sucursal_id) {
        $sql .= " AND d.sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    if ($fecha_inicio) {
        $sql .= " AND DATE(d.fecha) >= ?";
        $params[] = $fecha_inicio;
        $types .= 's';
    }
    
    if ($fecha_fin) {
        $sql .= " AND DATE(d.fecha) <= ?";
        $params[] = $fecha_fin;
        $types .= 's';
    }
    
    if ($estatus) {
        $sql .= " AND d.estatus = ?";
        $params[] = $estatus;
        $types .= 's';
    }
    
    $sql .= " ORDER BY d.fecha DESC LIMIT 100";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener detalles de una devolución
function getDevolucionDetalle($devolucion_id) {
    global $conn;
    
    $sql = "SELECT * FROM Devoluciones_Detalle WHERE devolucion_id = ? ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $devolucion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener devolución por ID
function getDevolucionById($devolucion_id) {
    global $conn;
    
    $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            WHERE d.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $devolucion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Devoluciones - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once "Menu.php" ?>
    <div class="content">
        <?php include "navbar.php";?>
        
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Sistema de Devoluciones - <?php echo $row['Licencia']?></h6>
                    
                    <!-- Header con botones -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fa-solid fa-undo me-2"></i>Sistema de Devoluciones</h2>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="mostrarNuevaDevolucion()">
                                <i class="fa-solid fa-plus me-1"></i>Nueva Devolución
                            </button>
                            <button class="btn btn-secondary" onclick="mostrarProcesarDevoluciones()">
                                <i class="fa-solid fa-cogs me-1"></i>Procesar Devoluciones
                            </button>
                        </div>
                    </div>
                    
                    <!-- Búsqueda de Productos -->
                    <div id="busqueda-section" class="card" style="display: none;">
                        <div class="card-body">
                            <h5><i class="fa-solid fa-search me-2"></i>Buscar Productos</h5>
                            <p class="mb-3">Escanea o ingresa el código de barras del producto a devolver</p>
                            
                            <!-- Información del Usuario y Sucursal -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Usuario:</label>
                                    <input type="text" class="form-control" value="<?php echo $row['Nombre_Apellidos']; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sucursal:</label>
                                    <input type="text" class="form-control" value="<?php echo $row['Nombre_Sucursal']; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha:</label>
                                    <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="codigoEscaneado" class="form-label">
                                            <i class="fas fa-barcode me-1"></i>
                                            <span>Producto</span>
                                        </label>
                                        <input type="text" class="form-control" name="codigoEscaneado" id="codigoEscaneado" 
                                               placeholder="Código de barras o nombre del producto..." autofocus>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo-devolucion-default" class="form-label">
                                            <i class="fa-solid fa-tag me-1"></i>
                                            <span>Tipo de Devolución por Defecto</span>
                                        </label>
                                        <select id="tipo-devolucion-default" class="form-select">
                                            <option value="no_facturado">Producto no facturado</option>
                                            <option value="danado_recibir">Dañado al recibir</option>
                                            <option value="proximo_caducar">Próximo a caducar</option>
                                            <option value="caducado">Caducado</option>
                                            <option value="danado_roto">Dañado/Roto</option>
                                            <option value="solicitado_admin">Solicitado por administración</option>
                                            <option value="error_etiquetado">Error en etiquetado</option>
                                            <option value="defectuoso">Defectuoso</option>
                                            <option value="sobrante">Sobrante</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Productos en Devolución -->
                    <div id="productos-lista" class="row" style="display: none;">
                        <div class="col-12">
                            <h5><i class="fa-solid fa-list me-2"></i>Productos a Devolver</h5>
                            <div id="productos-container">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </div>
                            
                            <!-- Resumen y Acciones -->
                            <div class="total-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Observaciones Generales:</h6>
                                        <textarea id="observaciones-generales" class="form-control" rows="3" 
                                                  placeholder="Observaciones adicionales sobre la devolución..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-end">
                                            <h6>Total de Productos: <span id="total-productos">0</span></h6>
                                            <h6>Total de Unidades: <span id="total-unidades">0</span></h6>
                                            <div class="mt-3">
                                                <button class="btn btn-success me-2" onclick="procesarDevolucion()">
                                                    <i class="fa-solid fa-check me-1"></i>Procesar Devolución
                                                </button>
                                                <button class="btn btn-secondary" onclick="cancelarDevolucion()">
                                                    <i class="fa-solid fa-times me-1"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Procesar Devoluciones -->
                    <div id="procesar-devoluciones" class="card" style="display: none;">
                        <div class="card-header">
                            <h5><i class="fa-solid fa-cogs me-2"></i>Procesar Devoluciones</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Filtrar por Estatus:</label>
                                    <select id="filtro-procesar" class="form-select">
                                        <option value="procesada">Procesadas</option>
                                        <option value="pendiente">Pendientes</option>
                                        <option value="cancelada">Canceladas</option>
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Inicio:</label>
                                    <input type="date" id="fecha-procesar-inicio" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Fin:</label>
                                    <input type="date" id="fecha-procesar-fin" class="form-control">
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Productos</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-procesar-devoluciones">
                                        <!-- Los datos se cargarán aquí -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Detalles de Devolución -->
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-eye me-2"></i>Detalles de Devolución</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalles-contenido">
                        <!-- Los detalles se cargarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Variables globales
        let productosDevolucion = [];
        var isScannerInput = false;

        $(document).ready(function() {
            // Enter en búsqueda de productos
            $('#codigoEscaneado').keyup(function (event) {
                if (event.which === 13) {
                    if (!isScannerInput) {
                        var codigoEscaneado = $('#codigoEscaneado').val();
                        buscarArticulo(codigoEscaneado);
                        event.preventDefault();
                    }
                    isScannerInput = false;
                }
            });
            
            // Autocompletado
            $('#codigoEscaneado').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: 'Controladores/autocompletadoAdministrativo.php',
                        type: 'GET',
                        dataType: 'json',
                        data: { term: request.term },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 3,
                select: function (event, ui) {
                    var codigoEscaneado = ui.item.value;
                    isScannerInput = true;
                    $('#codigoEscaneado').val(codigoEscaneado);
                    buscarArticulo(codigoEscaneado);
                }
            });
        });
        
        // Mostrar nueva devolución
        function mostrarNuevaDevolucion() {
            $('#busqueda-section').show();
            $('#productos-lista').show();
            $('#procesar-devoluciones').hide();
            $('#codigoEscaneado').focus();
            
            // Limpiar productos anteriores
            window.productosDevolucion = [];
            mostrarProductos([]);
            actualizarTotales();
        }
        
        // Mostrar procesar devoluciones
        function mostrarProcesarDevoluciones() {
            $('#busqueda-section').hide();
            $('#productos-lista').hide();
            $('#procesar-devoluciones').show();
            cargarDevolucionesProcesar();
        }
        
        // Buscar artículo
        function buscarArticulo(codigoEscaneado) {
            var formData = new FormData();
            formData.append('codigoEscaneado', codigoEscaneado);

            $.ajax({
                url: "Controladores/escaner_articulo_administrativo.php",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if ($.isEmptyObject(data)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Producto no encontrado',
                            text: 'El producto no existe en el inventario',
                            timer: 2000
                        });
                    } else if (data.codigo || data.descripcion) {
                        agregarProductoDevolucion(data);
                    }
                    limpiarCampo();
                },
                error: function (data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la búsqueda',
                        text: 'No se pudo buscar el producto'
                    });
                }
            });
        }
        
        // Agregar producto a devoluciones
        function agregarProductoDevolucion(producto) {
            if (!producto || (!producto.id && !producto.descripcion)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto inválido',
                    text: 'El producto no es válido'
                });
                return;
            }
            
            // Verificar si ya existe en la lista
            const existingIndex = window.productosDevolucion ? 
                window.productosDevolucion.findIndex(p => p.codigo_barras === producto.codigo) : -1;
            
            if (existingIndex >= 0) {
                // Si ya existe, incrementar cantidad
                window.productosDevolucion[existingIndex].cantidad += 1;
                mostrarProductos(window.productosDevolucion);
                actualizarTotales();
                return;
            }
            
            // Obtener el tipo de devolución seleccionado
            const tipoDevolucionSeleccionado = $('#tipo-devolucion-default').val() || 'no_facturado';
            
            // Agregar nuevo producto
            const nuevoProducto = {
                codigo_barras: producto.codigo,
                producto: {
                    id: producto.id,
                    codigo: producto.codigo,
                    nombre: producto.descripcion,
                    precio_venta: producto.precio,
                    precio_compra: producto.precio_compra || producto.precio,
                    stock: producto.stock || 0,
                    lote: producto.lote || null,
                    fecha_caducidad: producto.fecha_caducidad || null
                },
                cantidad: 1,
                tipo_devolucion: tipoDevolucionSeleccionado,
                observaciones: ''
            };
            
            if (!window.productosDevolucion) {
                window.productosDevolucion = [];
            }
            
            window.productosDevolucion.push(nuevoProducto);
            mostrarProductos(window.productosDevolucion);
            actualizarTotales();
        }
        
        // Limpiar campo de búsqueda
        function limpiarCampo() {
            $('#codigoEscaneado').val('');
            $('#codigoEscaneado').focus();
        }
        
        // Mostrar productos
        function mostrarProductos(productos) {
            const container = $('#productos-container');
            container.empty();
            
            if (!productos || productos.length === 0) {
                container.html('<div class="alert alert-info text-center">No hay productos agregados</div>');
                return;
            }
            
            productos.forEach(function(item, index) {
                const producto = item.producto;
                const itemKey = item.codigo_barras + '_' + (producto.lote || 'default');
                
                const html = `
                    <div class="product-item" data-key="${itemKey}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-1">${producto.nombre}</h6>
                                <small class="text-muted">Código: ${item.codigo_barras} | Lote: ${producto.lote || 'N/A'}</small>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" onchange="actualizarTipoDevolucionLocal('${itemKey}', this.value)">
                                    <option value="no_facturado" ${item.tipo_devolucion === 'no_facturado' ? 'selected' : ''}>No Facturado</option>
                                    <option value="danado_recibir" ${item.tipo_devolucion === 'danado_recibir' ? 'selected' : ''}>Dañado al Recibir</option>
                                    <option value="proximo_caducar" ${item.tipo_devolucion === 'proximo_caducar' ? 'selected' : ''}>Próximo a Caducar</option>
                                    <option value="caducado" ${item.tipo_devolucion === 'caducado' ? 'selected' : ''}>Caducado</option>
                                    <option value="danado_roto" ${item.tipo_devolucion === 'danado_roto' ? 'selected' : ''}>Dañado/Roto</option>
                                    <option value="solicitado_admin" ${item.tipo_devolucion === 'solicitado_admin' ? 'selected' : ''}>Solicitado por Admin</option>
                                    <option value="error_etiquetado" ${item.tipo_devolucion === 'error_etiquetado' ? 'selected' : ''}>Error en Etiquetado</option>
                                    <option value="defectuoso" ${item.tipo_devolucion === 'defectuoso' ? 'selected' : ''}>Defectuoso</option>
                                    <option value="sobrante" ${item.tipo_devolucion === 'sobrante' ? 'selected' : ''}>Sobrante</option>
                                    <option value="otro" ${item.tipo_devolucion === 'otro' ? 'selected' : ''}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control cantidad-input" 
                                           value="${item.cantidad}" min="1" max="${producto.stock}"
                                           onchange="actualizarCantidadLocal('${itemKey}', this.value)">
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarProductoLocal('${itemKey}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        ${item.observaciones ? `<div class="row mt-2"><div class="col-12"><small class="text-muted"><strong>Observaciones:</strong> ${item.observaciones}</small></div></div>` : ''}
                    </div>
                `;
                container.append(html);
            });
        }
        
        // Actualizar cantidad localmente
        function actualizarCantidadLocal(itemKey, nuevaCantidad) {
            if (!window.productosDevolucion) return;
            
            const item = window.productosDevolucion.find(p => {
                const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                return key === itemKey;
            });
            
            if (item) {
                item.cantidad = parseInt(nuevaCantidad);
                actualizarTotales();
            }
        }
        
        // Actualizar tipo de devolución localmente
        function actualizarTipoDevolucionLocal(itemKey, nuevoTipo) {
            if (!window.productosDevolucion) return;
            
            const item = window.productosDevolucion.find(p => {
                const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                return key === itemKey;
            });
            
            if (item) {
                item.tipo_devolucion = nuevoTipo;
            }
        }
        
        // Eliminar producto localmente
        function eliminarProductoLocal(itemKey) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Se eliminará el producto de la devolución',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (!window.productosDevolucion) return;
                    
                    window.productosDevolucion = window.productosDevolucion.filter(p => {
                        const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                        return key !== itemKey;
                    });
                    
                    mostrarProductos(window.productosDevolucion);
                    actualizarTotales();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Actualizar totales
        function actualizarTotales() {
            if (!window.productosDevolucion) {
                $('#total-productos').text('0');
                $('#total-unidades').text('0');
                return;
            }
            
            const totalProductos = window.productosDevolucion.length;
            const totalUnidades = window.productosDevolucion.reduce((sum, item) => sum + item.cantidad, 0);
            
            $('#total-productos').text(totalProductos);
            $('#total-unidades').text(totalUnidades);
        }
        
        // Procesar devolución
        function procesarDevolucion() {
            if (!window.productosDevolucion || window.productosDevolucion.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin productos',
                    text: 'No hay productos para procesar'
                });
                return;
            }
            
            const observaciones = $('#observaciones-generales').val();
            
            Swal.fire({
                title: '¿Procesar devolución?',
                text: 'Se procesará la devolución con ' + window.productosDevolucion.length + ' productos',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'Devoluciones.php',
                        method: 'POST',
                        data: {
                            action: 'procesar_devolucion',
                            observaciones_generales: observaciones,
                            productos: JSON.stringify(window.productosDevolucion)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Devolución procesada!',
                                    text: 'Folio: ' + response.folio,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    cancelarDevolucion();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar devolución'
                            });
                        }
                    });
                }
            });
        }
        
        // Cancelar devolución
        function cancelarDevolucion() {
            Swal.fire({
                title: '¿Cancelar devolución?',
                text: 'Se perderán todos los productos agregados',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.productosDevolucion = [];
                    $('#busqueda-section').hide();
                    $('#productos-lista').hide();
                    $('#codigoEscaneado').val('');
                    $('#observaciones-generales').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Devolución cancelada',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Cargar devoluciones para procesar
        function cargarDevolucionesProcesar() {
            const fechaInicio = $('#fecha-procesar-inicio').val();
            const fechaFin = $('#fecha-procesar-fin').val();
            const estatus = $('#filtro-procesar').val();
            
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_devoluciones',
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    estatus: estatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDevolucionesProcesar(response.devoluciones);
                    }
                }
            });
        }
        
        // Mostrar devoluciones para procesar
        function mostrarDevolucionesProcesar(devoluciones) {
            const tbody = $('#tabla-procesar-devoluciones');
            tbody.empty();
            
            if (devoluciones.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center">No hay devoluciones para procesar</td></tr>');
                return;
            }
            
            devoluciones.forEach(function(devolucion) {
                const estatusClass = `status-${devolucion.estatus}`;
                const html = `
                    <tr>
                        <td><strong>${devolucion.folio}</strong></td>
                        <td>${formatearFecha(devolucion.fecha)}</td>
                        <td>${devolucion.usuario_nombre || 'N/A'}</td>
                        <td>${devolucion.total_productos || 0}</td>
                        <td><span class="badge status-badge ${estatusClass}">${devolucion.estatus}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="verDetallesProcesar(${devolucion.id})">
                                <i class="fa-solid fa-eye"></i> Ver Detalles
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(html);
            });
        }
        
        // Ver detalles para procesar
        function verDetallesProcesar(devolucionId) {
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_detalles',
                    devolucion_id: devolucionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDetalles(response.devolucion, response.detalles);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                }
            });
        }
        
        // Mostrar detalles
        function mostrarDetalles(devolucion, detalles) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Folio:</strong> ${devolucion.folio}</h6>
                        <h6><strong>Fecha:</strong> ${formatearFecha(devolucion.fecha)}</h6>
                        <h6><strong>Usuario:</strong> ${devolucion.usuario_nombre || 'N/A'}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Sucursal:</strong> ${devolucion.Nombre_Sucursal || 'N/A'}</h6>
                        <h6><strong>Estatus:</strong> <span class="badge status-${devolucion.estatus}">${devolucion.estatus}</span></h6>
                    </div>
                </div>
            `;
            
            if (devolucion.observaciones_generales) {
                html += `<div class="alert alert-info"><strong>Observaciones:</strong> ${devolucion.observaciones_generales}</div>`;
            }
            
            html += `
                <h6>Productos Devoluidos:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Lote</th>
                                <th>Cantidad</th>
                                <th>Tipo</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            detalles.forEach(function(detalle) {
                html += `
                    <tr>
                        <td>${detalle.nombre_producto}</td>
                        <td>${detalle.codigo_barras}</td>
                        <td>${detalle.lote}</td>
                        <td>${detalle.cantidad}</td>
                        <td><span class="badge bg-${getTipoColor(detalle.tipo_devolucion)}">${getTipoDevolucionTexto(detalle.tipo_devolucion)}</span></td>
                        <td>${detalle.observaciones || '-'}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            $('#detalles-contenido').html(html);
            $('#modalDetalles').modal('show');
        }
        
        // Obtener texto del tipo de devolución
        function getTipoDevolucionTexto(tipo) {
            const tipos = {
                'no_facturado': 'No Facturado',
                'danado_recibir': 'Dañado al Recibir',
                'proximo_caducar': 'Próximo a Caducar',
                'caducado': 'Caducado',
                'danado_roto': 'Dañado/Roto',
                'solicitado_admin': 'Solicitado por Admin',
                'error_etiquetado': 'Error en Etiquetado',
                'defectuoso': 'Defectuoso',
                'sobrante': 'Sobrante',
                'otro': 'Otro'
            };
            return tipos[tipo] || tipo;
        }
        
        // Obtener color del tipo de devolución
        function getTipoColor(tipo) {
            const colores = {
                'no_facturado': 'primary',
                'danado_recibir': 'danger',
                'proximo_caducar': 'warning',
                'caducado': 'dark',
                'danado_roto': 'danger',
                'solicitado_admin': 'info',
                'error_etiquetado': 'secondary',
                'defectuoso': 'danger',
                'sobrante': 'success',
                'otro': 'light'
            };
            return colores[tipo] || 'secondary';
        }
        
        // Formatear fecha
        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES');
        }
    </script>

    <style>
        .product-item {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        .cantidad-input {
            width: 80px;
        }
        .total-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
        }
        .status-pendiente { background-color: #ffc107; color: #000; }
        .status-procesada { background-color: #28a745; color: #fff; }
        .status-cancelada { background-color: #dc3545; color: #fff; }
    </style>

    <?php include "footer.php";?>
</body>
</html>

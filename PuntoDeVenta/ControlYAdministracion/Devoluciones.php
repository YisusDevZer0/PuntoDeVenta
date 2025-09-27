<?php
include_once "Controladores/ControladorUsuario.php";

// Obtener datos del usuario
$usuario_id = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1;
$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;
$tipo_usuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar permisos
$isAdmin = ($tipo_usuario == 'Administrador' || $tipo_usuario == 'MKT');

// Definir variable para identificar la página actual
$currentPage = 'devoluciones';
$showDashboard = false;
$disabledAttr = $isAdmin ? '' : 'disabled';

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

// Función para buscar producto
function buscarProducto($codigo_barras, $sucursal_id) {
    global $conn;
    
    $sql = "SELECT s.*, p.Precio_Venta, p.Precio_C, p.Fecha_Caducidad, p.Lote
            FROM Stock_POS s 
            LEFT JOIN productos_lotes_caducidad p ON s.ID_Prod_POS = p.ID_Prod_POS AND s.Fk_sucursal = p.Fk_sucursal
            WHERE s.Cod_Barra = ? AND s.Fk_sucursal = ? AND s.Existencias_R > 0
            ORDER BY p.Fecha_Caducidad ASC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $codigo_barras, $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'agregar_producto':
            $codigo_barras = trim($_POST['codigo_barras']);
            $cantidad = intval($_POST['cantidad']);
            $tipo_devolucion = $_POST['tipo_devolucion'];
            $observaciones = trim($_POST['observaciones']);
            
            if (empty($codigo_barras) || $cantidad <= 0) {
                $response['message'] = 'Código de barras y cantidad son requeridos';
                echo json_encode($response);
                exit;
            }
            
            $producto = buscarProducto($codigo_barras, $sucursal_id);
            if (!$producto) {
                $response['message'] = 'Producto no encontrado o sin existencias';
                echo json_encode($response);
                exit;
            }
            
            // Agregar a la sesión temporal
            if (!isset($_SESSION['devolucion_temp'])) {
                $_SESSION['devolucion_temp'] = [];
            }
            
            $item_key = $codigo_barras . '_' . $producto['Lote'];
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                $_SESSION['devolucion_temp'][$item_key]['cantidad'] += $cantidad;
            } else {
                $_SESSION['devolucion_temp'][$item_key] = [
                    'codigo_barras' => $codigo_barras,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'tipo_devolucion' => $tipo_devolucion,
                    'observaciones' => $observaciones
                ];
            }
            
            $response['success'] = true;
            $response['message'] = 'Producto agregado correctamente';
            break;
            
        case 'eliminar_producto':
            $item_key = $_POST['item_key'];
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                unset($_SESSION['devolucion_temp'][$item_key]);
                $response['success'] = true;
                $response['message'] = 'Producto eliminado correctamente';
            } else {
                $response['message'] = 'Producto no encontrado';
            }
            break;
            
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

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Sistema de Devoluciones - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>

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
                    <h6 class="mb-4" style="color:#0172b6;">Sistema de Devoluciones - <?php echo $row['Licencia']?></h6>
                    
                    <!-- Header con botones -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fa-solid fa-undo me-2"></i>Sistema de Devoluciones</h2>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="mostrarNuevaDevolucion()">
                                <i class="fa-solid fa-plus me-1"></i>Nueva Devolución
                            </button>
                            <button class="btn btn-outline-primary" onclick="mostrarReportes()">
                                <i class="fa-solid fa-chart-bar me-1"></i>Reportes
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
                    
                    <!-- Lista de Devoluciones -->
                    <div id="lista-devoluciones">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fa-solid fa-history me-2"></i>Historial de Devoluciones</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filtros -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha Inicio:</label>
                                        <input type="date" id="fecha-inicio" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha Fin:</label>
                                        <input type="date" id="fecha-fin" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estatus:</label>
                                        <select id="filtro-estatus" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="pendiente">Pendiente</option>
                                            <option value="procesada">Procesada</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary d-block w-100" onclick="filtrarDevoluciones()">
                                            <i class="fa-solid fa-filter me-1"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Tabla de Devoluciones -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Folio</th>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Sucursal</th>
                                                <th>Productos</th>
                                                <th>Estatus</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-devoluciones">
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
                    <button type="button" class="btn btn-primary" onclick="imprimirDevolucion()">
                        <i class="fa-solid fa-print me-1"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Agregar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Agregar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-producto">
                        <div class="mb-3">
                            <label class="form-label">Código de Barras:</label>
                            <input type="text" id="modal-codigo-barras" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad:</label>
                            <input type="number" id="modal-cantidad" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Devolución:</label>
                            <select id="modal-tipo-devolucion" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach (getTiposDevolucion() as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones:</label>
                            <textarea id="modal-observaciones" class="form-control" rows="3" 
                                      placeholder="Motivo específico de la devolución..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarProductoModal()">
                        <i class="fa-solid fa-plus me-1"></i>Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Variables globales
        let productosDevolucion = [];
        let scannerActivo = false;
        
        // Inicializar página
        var isScannerInput = false;

        $(document).ready(function() {
            cargarDevoluciones();
            
            // Enter en búsqueda de productos (igual que RealizarVentas.php)
            $('#codigoEscaneado').keyup(function (event) {
                if (event.which === 13) { // Verifica si la tecla presionada es "Enter"
                    if (!isScannerInput) { // Verifica si el evento no viene del escáner
                        var codigoEscaneado = $('#codigoEscaneado').val();
                        buscarArticulo(codigoEscaneado);
                        event.preventDefault(); // Evita que el formulario se envíe al presionar "Enter"
                    }
                    isScannerInput = false; // Restablece la bandera del escáner
                }
            });
            
            // Autocompletado (igual que RealizarVentas.php)
            $('#codigoEscaneado').autocomplete({
                source: function (request, response) {
                    // Realiza una solicitud AJAX para obtener los resultados de autocompletado
                    $.ajax({
                        url: 'Controladores/autocompletadoAdministrativo.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 3, // Especifica la cantidad mínima de caracteres para activar el autocompletado
                select: function (event, ui) {
                    // Cuando se selecciona un resultado del autocompletado, llamar a la función buscarArticulo() con el código seleccionado
                    var codigoEscaneado = ui.item.value;
                    isScannerInput = true; // Establece la bandera del escáner
                    $('#codigoEscaneado').val(codigoEscaneado);
                    buscarArticulo(codigoEscaneado);
                }
            });
        });
        
        // Mostrar nueva devolución
        function mostrarNuevaDevolucion() {
            $('#busqueda-section').show();
            $('#productos-lista').show();
            $('#lista-devoluciones').hide();
            $('#codigoEscaneado').focus();
        }
        
        // Buscar artículo (igual que en RealizarVentas.php y RealizarTraspasos.php)
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
                        alert('Producto no encontrado');
                    } else if (data.codigo || data.descripcion) {
                        // Agregar directamente a la lista de devoluciones (sin modal)
                        agregarProductoDevolucion(data);
                    }
                    limpiarCampo();
                },
                error: function (data) {
                    alert('Error en la búsqueda');
                }
            });
        }
        
        // Agregar producto a devoluciones (sin modal, directo como en RealizarVentas.php)
        function agregarProductoDevolucion(producto) {
            if (!producto || (!producto.id && !producto.descripcion)) {
                alert('El producto no es válido');
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
        
        // Mostrar modal de producto
        function mostrarModalProducto(producto) {
            $('#modal-codigo-barras').val(producto.codigo);
            $('#modal-cantidad').val(1);
            $('#modal-tipo-devolucion').val('');
            $('#modal-observaciones').val('');
            
            // Limpiar información anterior
            $('#modalProducto .modal-body .alert').remove();
            
            // Mostrar información del producto
            const info = `
                <div class="alert alert-info">
                    <h6>${producto.nombre}</h6>
                    <p class="mb-1"><strong>Código:</strong> ${producto.codigo}</p>
                    <p class="mb-1"><strong>Stock:</strong> ${producto.stock}</p>
                    <p class="mb-1"><strong>Precio:</strong> $${producto.precio_venta}</p>
                    ${producto.lote ? `<p class="mb-1"><strong>Lote:</strong> ${producto.lote}</p>` : ''}
                    ${producto.fecha_caducidad ? `<p class="mb-0"><strong>Caducidad:</strong> ${producto.fecha_caducidad}</p>` : ''}
                </div>
            `;
            $('#modalProducto .modal-body').prepend(info);
            
            $('#modalProducto').modal('show');
        }
        
        // Agregar producto desde modal
        function agregarProductoModal() {
            const codigoBarras = $('#modal-codigo-barras').val();
            const cantidad = parseInt($('#modal-cantidad').val());
            const tipoDevolucion = $('#modal-tipo-devolucion').val();
            const observaciones = $('#modal-observaciones').val();
            
            if (!codigoBarras || !cantidad || !tipoDevolucion) {
                alert('Complete todos los campos requeridos');
                return;
            }
            
            // Obtener datos del producto desde la sesión temporal
            const productoData = window.productoSeleccionado;
            if (!productoData) {
                alert('Error: No se encontraron los datos del producto');
                return;
            }
            
            // Agregar a la lista local
            const itemKey = codigoBarras + '_' + (productoData.lote || 'default');
            const item = {
                codigo_barras: codigoBarras,
                producto: productoData,
                cantidad: cantidad,
                tipo_devolucion: tipoDevolucion,
                observaciones: observaciones
            };
            
            if (!window.productosDevolucion) {
                window.productosDevolucion = [];
            }
            
            // Verificar si ya existe
            const existingIndex = window.productosDevolucion.findIndex(p => p.codigo_barras === codigoBarras && p.producto.lote === productoData.lote);
            if (existingIndex >= 0) {
                window.productosDevolucion[existingIndex].cantidad += cantidad;
            } else {
                window.productosDevolucion.push(item);
            }
            
            $('#modalProducto').modal('hide');
            $('#codigoEscaneado').val('');
            mostrarProductos(window.productosDevolucion);
            actualizarTotales();
        }
        
        // Cargar productos en devolución
        function cargarProductosDevolucion() {
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_productos_temp'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarProductos(response.productos);
                    }
                }
            });
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
                const tipoDevolucion = getTipoDevolucionTexto(item.tipo_devolucion);
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
            if (confirm('¿Está seguro de eliminar este producto?')) {
                if (!window.productosDevolucion) return;
                
                window.productosDevolucion = window.productosDevolucion.filter(p => {
                    const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                    return key !== itemKey;
                });
                
                mostrarProductos(window.productosDevolucion);
                actualizarTotales();
            }
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
                alert('No hay productos para procesar');
                return;
            }
            
            const observaciones = $('#observaciones-generales').val();
            
            if (confirm('¿Está seguro de procesar esta devolución?')) {
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
                            alert('Devolución procesada correctamente. Folio: ' + response.folio);
                            cancelarDevolucion();
                            cargarDevoluciones();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error al procesar devolución');
                    }
                });
            }
        }
        
        // Cancelar devolución
        function cancelarDevolucion() {
            if (confirm('¿Está seguro de cancelar la devolución actual?')) {
                window.productosDevolucion = [];
                $('#busqueda-section').hide();
                $('#productos-lista').hide();
                $('#lista-devoluciones').show();
                $('#codigoEscaneado').val('');
                $('#observaciones-generales').val('');
            }
        }
        
        // Cargar devoluciones
        function cargarDevoluciones() {
            const fechaInicio = $('#fecha-inicio').val();
            const fechaFin = $('#fecha-fin').val();
            const estatus = $('#filtro-estatus').val();
            
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
                        mostrarDevoluciones(response.devoluciones);
                    }
                }
            });
        }
        
        // Mostrar devoluciones en tabla
        function mostrarDevoluciones(devoluciones) {
            const tbody = $('#tabla-devoluciones');
            tbody.empty();
            
            if (devoluciones.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No hay devoluciones registradas</td></tr>');
                return;
            }
            
            devoluciones.forEach(function(devolucion) {
                const estatusClass = `status-${devolucion.estatus}`;
                const html = `
                    <tr>
                        <td><strong>${devolucion.folio}</strong></td>
                        <td>${formatearFecha(devolucion.fecha)}</td>
                        <td>${devolucion.usuario_nombre || 'N/A'}</td>
                        <td>${devolucion.Nombre_Sucursal || 'N/A'}</td>
                        <td>${devolucion.total_productos || 0}</td>
                        <td><span class="badge status-badge ${estatusClass}">${devolucion.estatus}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalles(${devolucion.id})">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(html);
            });
        }
        
        // Ver detalles de devolución
        function verDetalles(devolucionId) {
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
                    }
                }
            });
        }
        
        // Mostrar detalles en modal
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
        
        // Filtrar devoluciones
        function filtrarDevoluciones() {
            cargarDevoluciones();
        }
        
        // Mostrar reportes
        function mostrarReportes() {
            // Implementar funcionalidad de reportes
            alert('Funcionalidad de reportes en desarrollo');
        }
        
        // Activar cámara (placeholder)
        function activarCamara() {
            alert('Funcionalidad de cámara en desarrollo');
        }
        
        // Imprimir devolución
        function imprimirDevolucion() {
            window.print();
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
        .tipo-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
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

    <!-- Footer Start -->
    <?php include "footer.php";?>

</body>
</html>
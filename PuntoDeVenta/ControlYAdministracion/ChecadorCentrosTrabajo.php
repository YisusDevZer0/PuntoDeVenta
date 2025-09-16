<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión usando las variables correctas del sistema
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    include_once "Controladores/ControladorUsuario.php";
}

// Verificar si es administrador
$isAdmin = ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT');

if (!$isAdmin) {
    header("Location: ChecadorIndex.php");
    exit();
}

$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Centros de Trabajo - Administración Checador</title>
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <script src="https://kit.fontawesome.com/a337b4cc32.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Notifications Stylesheet -->
    <link href="css/notifications.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="js/validation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .centros-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .centros-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .content-section {
            padding: 40px;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .search-box {
            flex: 1;
            max-width: 300px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 14px;
        }
        
        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .centros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .centro-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .centro-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .centro-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .centro-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .centro-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .centro-info {
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .info-item i {
            width: 20px;
            margin-right: 10px;
            color: #667eea;
        }
        
        .centro-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-edit:hover {
            background: #e0a800;
            transform: translateY(-1px);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .btn-toggle {
            background: #28a745;
            color: white;
        }
        
        .btn-toggle:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: none;
            }
            
            .centros-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="centros-container">
        <div class="centros-card">
            <!-- Header Section -->
            <div class="header-section">
                <button class="back-button" onclick="window.location.href='ChecadorAdmin.php'">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <h1><i class="fas fa-building"></i> Centros de Trabajo</h1>
                <p>Gestiona los centros de trabajo y sus ubicaciones</p>
            </div>

            <!-- Content Section -->
            <div class="content-section">
                <!-- Action Bar -->
                <div class="action-bar">
                    <button class="btn-primary" onclick="mostrarModalNuevoCentro()">
                        <i class="fas fa-plus"></i> Nuevo Centro de Trabajo
                    </button>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Buscar centros de trabajo..." onkeyup="filtrarCentros()">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <!-- Centros Grid -->
                <div class="centros-grid" id="centrosGrid">
                    <div class="empty-state">
                        <i class="fas fa-building"></i>
                        <h3>Cargando centros de trabajo...</h3>
                        <p>Por favor espera mientras cargamos la información</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo centro -->
    <div class="modal fade" id="modalNuevoCentro" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Centro de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoCentro">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Centro</label>
                                    <input type="text" class="form-control" id="nombreCentro" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionCentro">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcionCentro" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Latitud</label>
                                    <input type="number" class="form-control" id="latitudCentro" step="0.0000001" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Longitud</label>
                                    <input type="number" class="form-control" id="longitudCentro" step="0.0000001" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Radio (metros)</label>
                                    <input type="number" class="form-control" id="radioCentro" value="100" min="10" max="1000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-control" id="estadoCentro">
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" onclick="obtenerUbicacionActual()">
                                <i class="fas fa-map-marker-alt"></i> Usar Ubicación Actual
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCentro()">Guardar Centro</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let centros = [];
        
        // Cargar centros al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarCentros();
        });
        
        // Cargar centros de trabajo
        async function cargarCentros() {
            try {
                const response = await makeRequest('obtener_centros_trabajo', {});
                
                if (response.success) {
                    centros = response.data;
                    mostrarCentros(centros);
                } else {
                    mostrarError('Error cargando centros de trabajo');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            }
        }
        
        // Mostrar centros en la grilla
        function mostrarCentros(centrosFiltrados) {
            const container = document.getElementById('centrosGrid');
            
            if (centrosFiltrados.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-building"></i>
                        <h3>No hay centros de trabajo</h3>
                        <p>Comienza creando tu primer centro de trabajo</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = centrosFiltrados.map(centro => `
                <div class="centro-card">
                    <div class="centro-header">
                        <div>
                            <div class="centro-title">${centro.nombre}</div>
                            <div class="centro-status ${centro.estado === 'active' ? 'status-active' : 'status-inactive'}">
                                ${centro.estado === 'active' ? 'Activo' : 'Inactivo'}
                            </div>
                        </div>
                    </div>
                    <div class="centro-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${centro.latitud}, ${centro.longitud}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-ruler"></i>
                            <span>Radio: ${centro.radio} metros</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map"></i>
                            <span>${centro.direccion || 'Sin dirección'}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>Creado: ${new Date(centro.created_at).toLocaleDateString('es-MX')}</span>
                        </div>
                    </div>
                    <div class="centro-actions">
                        <button class="btn-action btn-edit" onclick="editarCentro(${centro.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-action btn-toggle" onclick="toggleCentro(${centro.id}, '${centro.estado}')">
                            <i class="fas fa-${centro.estado === 'active' ? 'pause' : 'play'}"></i> 
                            ${centro.estado === 'active' ? 'Desactivar' : 'Activar'}
                        </button>
                        <button class="btn-action btn-delete" onclick="eliminarCentro(${centro.id})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        // Filtrar centros
        function filtrarCentros() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const centrosFiltrados = centros.filter(centro => 
                centro.nombre.toLowerCase().includes(searchTerm) ||
                (centro.direccion && centro.direccion.toLowerCase().includes(searchTerm))
            );
            mostrarCentros(centrosFiltrados);
        }
        
        // Mostrar modal de nuevo centro
        function mostrarModalNuevoCentro() {
            const modal = new bootstrap.Modal(document.getElementById('modalNuevoCentro'));
            modal.show();
        }
        
        // Obtener ubicación actual
        async function obtenerUbicacionActual() {
            if (!navigator.geolocation) {
                mostrarError('Geolocalización no soportada');
                return;
            }
            
            try {
                const position = await getCurrentPosition();
                document.getElementById('latitudCentro').value = position.coords.latitude;
                document.getElementById('longitudCentro').value = position.coords.longitude;
                mostrarExito('Ubicación obtenida correctamente');
            } catch (error) {
                mostrarError('No se pudo obtener la ubicación');
            }
        }
        
        function getCurrentPosition() {
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000
                });
            });
        }
        
        // Guardar centro
        async function guardarCentro() {
            const data = {
                nombre: document.getElementById('nombreCentro').value,
                direccion: document.getElementById('direccionCentro').value,
                descripcion: document.getElementById('descripcionCentro').value,
                latitud: parseFloat(document.getElementById('latitudCentro').value),
                longitud: parseFloat(document.getElementById('longitudCentro').value),
                radio: parseInt(document.getElementById('radioCentro').value),
                estado: document.getElementById('estadoCentro').value
            };
            
            try {
                const response = await makeRequest('guardar_centro_trabajo', data);
                
                if (response.success) {
                    mostrarExito('Centro de trabajo guardado correctamente');
                    document.getElementById('formNuevoCentro').reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCentro'));
                    modal.hide();
                    cargarCentros();
                } else {
                    mostrarError(response.message);
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            }
        }
        
        // Editar centro
        function editarCentro(id) {
            const centro = centros.find(c => c.id === id);
            if (centro) {
                document.getElementById('nombreCentro').value = centro.nombre;
                document.getElementById('direccionCentro').value = centro.direccion || '';
                document.getElementById('descripcionCentro').value = centro.descripcion || '';
                document.getElementById('latitudCentro').value = centro.latitud;
                document.getElementById('longitudCentro').value = centro.longitud;
                document.getElementById('radioCentro').value = centro.radio;
                document.getElementById('estadoCentro').value = centro.estado;
                
                // Cambiar el título del modal
                document.querySelector('#modalNuevoCentro .modal-title').textContent = 'Editar Centro de Trabajo';
                
                const modal = new bootstrap.Modal(document.getElementById('modalNuevoCentro'));
                modal.show();
            }
        }
        
        // Toggle estado del centro
        async function toggleCentro(id, estadoActual) {
            const nuevoEstado = estadoActual === 'active' ? 'inactive' : 'active';
            
            try {
                const response = await makeRequest('actualizar_centro_trabajo', {
                    id: id,
                    estado: nuevoEstado
                });
                
                if (response.success) {
                    mostrarExito(`Centro ${nuevoEstado === 'active' ? 'activado' : 'desactivado'} correctamente`);
                    cargarCentros();
                } else {
                    mostrarError(response.message);
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            }
        }
        
        // Eliminar centro
        async function eliminarCentro(id) {
            const confirmed = await Swal.fire({
                title: '¿Eliminar centro de trabajo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (confirmed.isConfirmed) {
                try {
                    const response = await makeRequest('eliminar_centro_trabajo', { id: id });
                    
                    if (response.success) {
                        mostrarExito('Centro de trabajo eliminado correctamente');
                        cargarCentros();
                    } else {
                        mostrarError(response.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarError('Error de conexión');
                }
            }
        }
        
        // Realizar petición al servidor
        async function makeRequest(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }

            const response = await fetch('Controladores/ChecadorController.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }
        
        // Mostrar mensajes
        function mostrarExito(mensaje) {
            Swal.fire('Éxito', mensaje, 'success');
        }
        
        function mostrarError(mensaje) {
            Swal.fire('Error', mensaje, 'error');
        }
    </script>
</body>
</html>

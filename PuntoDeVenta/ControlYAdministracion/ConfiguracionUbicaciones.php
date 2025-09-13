<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración de Ubicaciones - Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    <style>
        .location-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        .location-active {
            border-color: #28a745;
            background: #d4edda;
        }
        .btn-location {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Configuración de Ubicaciones de Trabajo</h1>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Configurar Nueva Ubicación</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="configurarUbicacionActual()">
                            <i class="fas fa-map-marker-alt"></i> Usar Ubicación Actual
                        </button>
                        <button class="btn btn-secondary" onclick="mostrarFormularioManual()">
                            <i class="fas fa-edit"></i> Configurar Manualmente
                        </button>
                    </div>
                </div>
                
                <div id="ubicacionesList" class="row">
                    <!-- Las ubicaciones se cargarán aquí -->
                </div>
                
                <div class="mt-4">
                    <a href="Checador.php" class="btn btn-success">
                        <i class="fas fa-arrow-left"></i> Volver al Checador
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para configuración manual -->
    <div class="modal fade" id="modalManual" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar Ubicación Manualmente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formManual">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Ubicación</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Latitud</label>
                                    <input type="number" class="form-control" id="latitud" step="0.0000001" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Longitud</label>
                                    <input type="number" class="form-control" id="longitud" step="0.0000001" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Radio (metros)</label>
                            <input type="number" class="form-control" id="radio" value="100" min="10" max="1000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarUbicacionManual()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let userLocation = null;
        
        // Cargar ubicaciones al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarUbicaciones();
        });
        
        // Obtener ubicación actual
        async function configurarUbicacionActual() {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Geolocalización no soportada', 'error');
                return;
            }
            
            try {
                const position = await getCurrentPosition();
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                const nombre = prompt('Nombre para esta ubicación:', 'Mi ubicación de trabajo');
                if (nombre) {
                    await guardarUbicacion({
                        nombre: nombre,
                        descripcion: 'Ubicación configurada automáticamente',
                        latitud: userLocation.lat,
                        longitud: userLocation.lng,
                        radio: 100,
                        direccion: '',
                        estado: 'active'
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo obtener la ubicación', 'error');
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
        
        function mostrarFormularioManual() {
            const modal = new bootstrap.Modal(document.getElementById('modalManual'));
            modal.show();
        }
        
        async function guardarUbicacionManual() {
            const form = document.getElementById('formManual');
            const formData = new FormData(form);
            
            const data = {
                nombre: document.getElementById('nombre').value,
                descripcion: document.getElementById('descripcion').value,
                latitud: parseFloat(document.getElementById('latitud').value),
                longitud: parseFloat(document.getElementById('longitud').value),
                radio: parseInt(document.getElementById('radio').value),
                direccion: document.getElementById('direccion').value,
                estado: 'active'
            };
            
            await guardarUbicacion(data);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalManual'));
            modal.hide();
            
            // Limpiar formulario
            form.reset();
        }
        
        async function guardarUbicacion(data) {
            try {
                const formData = new FormData();
                formData.append('action', 'guardar_ubicacion');
                
                for (const [key, value] of Object.entries(data)) {
                    formData.append(key, value);
                }
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Éxito', 'Ubicación guardada correctamente', 'success');
                    cargarUbicaciones();
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }
        
        async function cargarUbicaciones() {
            try {
                const formData = new FormData();
                formData.append('action', 'obtener_ubicaciones');
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    mostrarUbicaciones(result.data);
                } else {
                    console.error('Error cargando ubicaciones:', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        function mostrarUbicaciones(ubicaciones) {
            const container = document.getElementById('ubicacionesList');
            
            if (ubicaciones.length === 0) {
                container.innerHTML = '<div class="col-12"><div class="alert alert-info">No hay ubicaciones configuradas</div></div>';
                return;
            }
            
            container.innerHTML = ubicaciones.map(ubicacion => `
                <div class="col-md-6">
                    <div class="location-card ${ubicacion.estado === 'active' ? 'location-active' : ''}">
                        <h5>${ubicacion.nombre}</h5>
                        <p><strong>Descripción:</strong> ${ubicacion.descripcion || 'Sin descripción'}</p>
                        <p><strong>Coordenadas:</strong> ${ubicacion.latitud}, ${ubicacion.longitud}</p>
                        <p><strong>Radio:</strong> ${ubicacion.radio} metros</p>
                        <p><strong>Dirección:</strong> ${ubicacion.direccion || 'No especificada'}</p>
                        <p><strong>Estado:</strong> ${ubicacion.estado === 'active' ? 'Activa' : 'Inactiva'}</p>
                        <div>
                            <button class="btn btn-sm btn-warning btn-location" onclick="editarUbicacion(${ubicacion.id})">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-location" onclick="eliminarUbicacion(${ubicacion.id})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        async function eliminarUbicacion(id) {
            const confirmed = await Swal.fire({
                title: '¿Eliminar ubicación?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (confirmed.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'eliminar_ubicacion');
                    formData.append('ubicacion_id', id);
                    
                    const response = await fetch('Controladores/ChecadorController.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire('Éxito', 'Ubicación eliminada', 'success');
                        cargarUbicaciones();
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        }
        
        function editarUbicacion(id) {
            Swal.fire('Info', 'Función de edición en desarrollo', 'info');
        }
    </script>
</body>
</html>
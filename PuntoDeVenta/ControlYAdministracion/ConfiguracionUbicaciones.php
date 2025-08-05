<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Configuración de Ubicaciones - Sistema de Control</title>
    
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
        .config-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .config-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .config-header {
            background: #f8f9fa;
            padding: 30px;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
        }
        
        .config-title {
            font-size: 28px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .config-subtitle {
            font-size: 16px;
            color: #6c757d;
        }
        
        .config-content {
            padding: 30px;
        }
        
        .location-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .location-preview {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }
        
        .preview-title {
            font-weight: 700;
            color: #495057;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .preview-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .preview-label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .preview-value {
            color: #495057;
        }
        
        .location-list {
            background: white;
            border-radius: 15px;
            padding: 30px;
        }
        
        .list-title {
            font-size: 24px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 20px;
        }
        
        .location-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .location-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .location-name {
            font-weight: 700;
            color: #495057;
            font-size: 18px;
        }
        
        .location-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 20px;
        }
        
        .btn-edit {
            background: #007bff;
            color: white;
            border: none;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
        }
        
        .location-details {
            color: #6c757d;
            font-size: 14px;
        }
        
        .coordinate-display {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
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
        
        .map-container {
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
            border: 2px solid #e9ecef;
        }
        
        @media (max-width: 768px) {
            .config-content {
                padding: 20px;
            }
            
            .location-form {
                padding: 20px;
            }
            
            .location-item-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .location-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="config-container">
        <div class="config-card">
            <!-- Header -->
            <div class="config-header">
                <div class="config-title">
                    <i class="fas fa-map-marker-alt"></i> Configuración de Ubicaciones
                </div>
                <div class="config-subtitle">
                    Vincula tu ubicación de trabajo para el sistema de checador
                </div>
            </div>

            <!-- Contenido -->
            <div class="config-content">
                <!-- Formulario de Nueva Ubicación -->
                <div class="location-form">
                    <h4><i class="fas fa-plus-circle"></i> Nueva Ubicación de Trabajo</h4>
                    
                    <form id="locationForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre de la Ubicación</label>
                                    <input type="text" class="form-control" id="locationName" 
                                           placeholder="Ej: Casa Eduardo Mutul" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" class="form-control" id="locationDescription" 
                                           placeholder="Descripción opcional">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Latitud</label>
                                    <input type="number" class="form-control" id="latitude" 
                                           step="any" placeholder="20.9674" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Longitud</label>
                                    <input type="number" class="form-control" id="longitude" 
                                           step="any" placeholder="-89.5926" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Radio (metros)</label>
                                    <input type="number" class="form-control" id="radius" 
                                           value="100" min="10" max="1000" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="address" 
                                           placeholder="Dirección completa">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Estado</label>
                                    <select class="form-control" id="status">
                                        <option value="active">Activa</option>
                                        <option value="inactive">Inactiva</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-secondary" onclick="getCurrentLocation()">
                                    <i class="fas fa-crosshairs"></i> Obtener Ubicación Actual
                                </button>
                                <button type="button" class="btn btn-primary" onclick="saveLocation()">
                                    <i class="fas fa-save"></i> Guardar Ubicación
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Vista Previa -->
                    <div class="location-preview" id="locationPreview">
                        <div class="preview-title">
                            <i class="fas fa-eye"></i> Vista Previa
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Nombre:</span>
                            <span class="preview-value" id="previewName">--</span>
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Descripción:</span>
                            <span class="preview-value" id="previewDescription">--</span>
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Coordenadas:</span>
                            <span class="preview-value" id="previewCoordinates">--</span>
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Radio:</span>
                            <span class="preview-value" id="previewRadius">--</span>
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Dirección:</span>
                            <span class="preview-value" id="previewAddress">--</span>
                        </div>
                        <div class="preview-item">
                            <span class="preview-label">Estado:</span>
                            <span class="preview-value" id="previewStatus">--</span>
                        </div>
                    </div>
                </div>

                <!-- Lista de Ubicaciones -->
                <div class="location-list">
                    <div class="list-title">
                        <i class="fas fa-list"></i> Ubicaciones Configuradas
                    </div>
                    
                    <div id="locationsList">
                        <!-- Las ubicaciones se cargarán dinámicamente -->
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin"></i> Cargando ubicaciones...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentLocations = [];
        let editingLocationId = null;

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            loadLocations();
            setupFormListeners();
        });

        // Configurar listeners del formulario
        function setupFormListeners() {
            const formInputs = ['locationName', 'locationDescription', 'latitude', 'longitude', 'radius', 'address', 'status'];
            
            formInputs.forEach(inputId => {
                document.getElementById(inputId).addEventListener('input', updatePreview);
            });
        }

        // Obtener ubicación actual
        function getCurrentLocation() {
            if (navigator.geolocation) {
                Swal.fire({
                    title: 'Obteniendo ubicación...',
                    text: 'Por favor espera mientras obtenemos tu ubicación actual.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        document.getElementById('latitude').value = lat.toFixed(6);
                        document.getElementById('longitude').value = lng.toFixed(6);
                        
                        updatePreview();
                        
                        Swal.fire({
                            title: '¡Ubicación obtenida!',
                            text: 'Tu ubicación actual ha sido capturada.',
                            icon: 'success',
                            timer: 2000
                        });
                    },
                    function(error) {
                        console.error('Error obteniendo ubicación:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo obtener tu ubicación. Verifica que tengas el GPS activado.',
                            icon: 'error'
                        });
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Tu navegador no soporta geolocalización.',
                    icon: 'error'
                });
            }
        }

        // Actualizar vista previa
        function updatePreview() {
            const name = document.getElementById('locationName').value;
            const description = document.getElementById('locationDescription').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const radius = document.getElementById('radius').value;
            const address = document.getElementById('address').value;
            const status = document.getElementById('status').value;

            if (name || latitude || longitude) {
                document.getElementById('previewName').textContent = name || '--';
                document.getElementById('previewDescription').textContent = description || '--';
                document.getElementById('previewCoordinates').textContent = 
                    (latitude && longitude) ? `${latitude}, ${longitude}` : '--';
                document.getElementById('previewRadius').textContent = radius ? `${radius} metros` : '--';
                document.getElementById('previewAddress').textContent = address || '--';
                document.getElementById('previewStatus').textContent = 
                    status === 'active' ? 'Activa' : 'Inactiva';

                document.getElementById('locationPreview').style.display = 'block';
            } else {
                document.getElementById('locationPreview').style.display = 'none';
            }
        }

        // Guardar ubicación
        function saveLocation() {
            const formData = {
                name: document.getElementById('locationName').value,
                description: document.getElementById('locationDescription').value,
                latitude: parseFloat(document.getElementById('latitude').value),
                longitude: parseFloat(document.getElementById('longitude').value),
                radius: parseInt(document.getElementById('radius').value),
                address: document.getElementById('address').value,
                status: document.getElementById('status').value
            };

            // Validaciones
            if (!formData.name) {
                Swal.fire('Error', 'El nombre de la ubicación es requerido.', 'error');
                return;
            }

            if (!formData.latitude || !formData.longitude) {
                Swal.fire('Error', 'Las coordenadas son requeridas.', 'error');
                return;
            }

            if (formData.radius < 10 || formData.radius > 1000) {
                Swal.fire('Error', 'El radio debe estar entre 10 y 1000 metros.', 'error');
                return;
            }

            // Simular guardado en base de datos
            if (editingLocationId) {
                // Actualizar ubicación existente
                const index = currentLocations.findIndex(loc => loc.id === editingLocationId);
                if (index !== -1) {
                    currentLocations[index] = { ...currentLocations[index], ...formData };
                }
            } else {
                // Nueva ubicación
                const newLocation = {
                    id: Date.now(),
                    ...formData,
                    created_at: new Date().toISOString()
                };
                currentLocations.push(newLocation);
            }

            // Guardar en localStorage (simulando base de datos)
            localStorage.setItem('workLocations', JSON.stringify(currentLocations));

            Swal.fire({
                title: editingLocationId ? '¡Actualizado!' : '¡Guardado!',
                text: editingLocationId ? 'La ubicación ha sido actualizada.' : 'La ubicación ha sido guardada.',
                icon: 'success',
                timer: 2000
            });

            clearForm();
            loadLocations();
        }

        // Limpiar formulario
        function clearForm() {
            document.getElementById('locationForm').reset();
            document.getElementById('locationPreview').style.display = 'none';
            editingLocationId = null;
            
            // Cambiar texto del botón
            const saveButton = document.querySelector('button[onclick="saveLocation()"]');
            saveButton.innerHTML = '<i class="fas fa-save"></i> Guardar Ubicación';
        }

        // Cargar ubicaciones
        function loadLocations() {
            const savedLocations = localStorage.getItem('workLocations');
            currentLocations = savedLocations ? JSON.parse(savedLocations) : [];

            const locationsList = document.getElementById('locationsList');
            
            if (currentLocations.length === 0) {
                locationsList.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-map-marker-alt" style="font-size: 48px; margin-bottom: 20px;"></i>
                        <p>No hay ubicaciones configuradas</p>
                        <p>Agrega tu primera ubicación de trabajo usando el formulario de arriba.</p>
                    </div>
                `;
                return;
            }

            locationsList.innerHTML = currentLocations.map(location => `
                <div class="location-item">
                    <div class="location-item-header">
                        <div class="location-name">${location.name}</div>
                        <div class="location-actions">
                            <span class="status-badge ${location.status === 'active' ? 'status-active' : 'status-inactive'}">
                                ${location.status === 'active' ? 'Activa' : 'Inactiva'}
                            </span>
                            <button class="btn btn-sm btn-edit" onclick="editLocation(${location.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-delete" onclick="deleteLocation(${location.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="location-details">
                        ${location.description ? `<div><strong>Descripción:</strong> ${location.description}</div>` : ''}
                        ${location.address ? `<div><strong>Dirección:</strong> ${location.address}</div>` : ''}
                        <div class="coordinate-display">
                            <strong>Coordenadas:</strong> ${location.latitude}, ${location.longitude}
                        </div>
                        <div><strong>Radio:</strong> ${location.radius} metros</div>
                        <div><strong>Creado:</strong> ${new Date(location.created_at).toLocaleDateString('es-MX')}</div>
                    </div>
                </div>
            `).join('');
        }

        // Editar ubicación
        function editLocation(locationId) {
            const location = currentLocations.find(loc => loc.id === locationId);
            if (!location) return;

            editingLocationId = locationId;
            
            // Llenar formulario
            document.getElementById('locationName').value = location.name;
            document.getElementById('locationDescription').value = location.description || '';
            document.getElementById('latitude').value = location.latitude;
            document.getElementById('longitude').value = location.longitude;
            document.getElementById('radius').value = location.radius;
            document.getElementById('address').value = location.address || '';
            document.getElementById('status').value = location.status;

            // Cambiar texto del botón
            const saveButton = document.querySelector('button[onclick="saveLocation()"]');
            saveButton.innerHTML = '<i class="fas fa-save"></i> Actualizar Ubicación';

            updatePreview();

            // Scroll al formulario
            document.querySelector('.location-form').scrollIntoView({ behavior: 'smooth' });
        }

        // Eliminar ubicación
        function deleteLocation(locationId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    currentLocations = currentLocations.filter(loc => loc.id !== locationId);
                    localStorage.setItem('workLocations', JSON.stringify(currentLocations));
                    
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'La ubicación ha sido eliminada.',
                        icon: 'success',
                        timer: 2000
                    });

                    loadLocations();
                }
            });
        }
    </script>
</body>
</html> 
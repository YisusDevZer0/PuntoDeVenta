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
    <title>Administración del Checador - Sistema de Control</title>
    
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
        .admin-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .admin-card {
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .admin-card-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }
        
        .admin-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .admin-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        
        .admin-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .admin-description {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .recent-activity {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .activity-icon.entry {
            background: #d4edda;
            color: #155724;
        }
        
        .activity-icon.exit {
            background: #f8d7da;
            color: #721c24;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-user {
            font-weight: 600;
            color: #495057;
        }
        
        .activity-time {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-grid {
                grid-template-columns: 1fr;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="admin-card">
            <!-- Header Section -->
            <div class="header-section">
                <button class="back-button" onclick="window.location.href='ChecadorIndex.php'">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <h1><i class="fas fa-cog"></i> Administración del Checador</h1>
                <p>Gestiona usuarios, ubicaciones y configuraciones del sistema</p>
            </div>

            <!-- Content Section -->
            <div class="content-section">
                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number" id="totalUsers">--</div>
                        <div class="stat-label">Usuarios Activos</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-number" id="totalLocations">--</div>
                        <div class="stat-label">Ubicaciones Configuradas</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number" id="todayCheckins">--</div>
                        <div class="stat-label">Registros Hoy</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-number" id="monthlyCheckins">--</div>
                        <div class="stat-label">Registros del Mes</div>
                    </div>
                </div>

                <!-- Opciones de Administración -->
                <div class="admin-grid">
                    <div class="admin-card-item" onclick="window.location.href='ChecadorUsuarios.php'">
                        <div class="admin-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="admin-title">Gestionar Usuarios</div>
                        <div class="admin-description">
                            Administra los usuarios del sistema, asigna ubicaciones y configura permisos
                        </div>
                    </div>
                    
                    <div class="admin-card-item" onclick="window.location.href='ChecadorCentrosTrabajo.php'">
                        <div class="admin-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="admin-title">Centros de Trabajo</div>
                        <div class="admin-description">
                            Configura y gestiona los centros de trabajo y sus ubicaciones
                        </div>
                    </div>
                    
                    <div class="admin-card-item" onclick="window.location.href='ChecadorReportes.php'">
                        <div class="admin-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="admin-title">Reportes Avanzados</div>
                        <div class="admin-description">
                            Genera reportes detallados de asistencia y estadísticas
                        </div>
                    </div>
                    
                    <div class="admin-card-item" onclick="window.location.href='ChecadorConfiguracion.php'">
                        <div class="admin-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="admin-title">Configuración</div>
                        <div class="admin-description">
                            Configura parámetros del sistema y ajustes generales
                        </div>
                    </div>
                    
                    <div class="admin-card-item" onclick="window.location.href='ChecadorLogs.php'">
                        <div class="admin-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <div class="admin-title">Logs del Sistema</div>
                        <div class="admin-description">
                            Revisa los logs y actividad del sistema de checador
                        </div>
                    </div>
                    
                    <div class="admin-card-item" onclick="window.location.href='ChecadorBackup.php'">
                        <div class="admin-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="admin-title">Respaldo de Datos</div>
                        <div class="admin-description">
                            Gestiona respaldos y restauraciones de datos del checador
                        </div>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="recent-activity">
                    <h4><i class="fas fa-history"></i> Actividad Reciente</h4>
                    <div id="recentActivity">
                        <div class="activity-item">
                            <div class="activity-icon entry">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-user">Cargando actividad...</div>
                                <div class="activity-time">Por favor espera</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cargar estadísticas al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadRecentActivity();
        });
        
        // Cargar estadísticas
        async function loadStatistics() {
            try {
                const response = await makeRequest('obtener_estadisticas_admin', {});
                
                if (response.success) {
                    document.getElementById('totalUsers').textContent = response.data.total_usuarios || 0;
                    document.getElementById('totalLocations').textContent = response.data.total_ubicaciones || 0;
                    document.getElementById('todayCheckins').textContent = response.data.registros_hoy || 0;
                    document.getElementById('monthlyCheckins').textContent = response.data.registros_mes || 0;
                }
            } catch (error) {
                console.error('Error cargando estadísticas:', error);
            }
        }
        
        // Cargar actividad reciente
        async function loadRecentActivity() {
            try {
                const response = await makeRequest('obtener_actividad_reciente', {});
                
                if (response.success) {
                    const container = document.getElementById('recentActivity');
                    
                    if (response.data.length === 0) {
                        container.innerHTML = '<div class="activity-item"><div class="activity-content"><div class="activity-user">No hay actividad reciente</div></div></div>';
                        return;
                    }
                    
                    container.innerHTML = response.data.map(activity => `
                        <div class="activity-item">
                            <div class="activity-icon ${activity.tipo}">
                                <i class="fas fa-${activity.tipo === 'entrada' ? 'sign-in-alt' : 'sign-out-alt'}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-user">${activity.usuario} - ${activity.tipo}</div>
                                <div class="activity-time">${activity.fecha_hora}</div>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error cargando actividad:', error);
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
    </script>
</body>
</html>

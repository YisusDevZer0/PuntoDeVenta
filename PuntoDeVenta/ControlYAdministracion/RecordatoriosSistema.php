<?php
// Verificación básica de sesión
session_start();

// Verificar sesión de manera simple
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Variables básicas
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

// Incluir conexión directamente si no existe
if (!isset($con) || !$con) {
    include_once "Controladores/db_connect.php";
    
    // El archivo db_connect.php usa $conn, así que lo asignamos a $con
    if (isset($conn) && $conn) {
        $con = $conn;
    } else {
        // Si aún no hay conexión, crear una manualmente
        $con = new mysqli("localhost", "u858848268_devpezer0", "F9+nIIOuCh8yI6wu4!08", "u858848268_doctorpez");
        if ($con->connect_error) {
            die("Error de conexión: " . $con->connect_error);
        }
    }
}

$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;
$disabledAttr = '';
$currentPage = 'recordatorios';
$showDashboard = false;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar tablas
$tablas_existen = false;
$tablas_creadas = [];

try {
    if (isset($con) && $con) {
        $tablas_requeridas = [
            'recordatorios_sistema',
            'recordatorios_destinatarios', 
            'recordatorios_grupos',
            'recordatorios_logs'
        ];
        
        foreach ($tablas_requeridas as $tabla) {
            $resultado = $con->query("SHOW TABLES LIKE '$tabla'");
            if ($resultado && $resultado->num_rows > 0) {
                $tablas_creadas[] = $tabla;
            }
        }
        
        $tablas_existen = in_array('recordatorios_sistema', $tablas_creadas);
    }
} catch (Exception $e) {
    $tablas_existen = false;
}

// Obtener recordatorios existentes
$recordatorios = [];
if ($tablas_existen) {
    try {
        $query = "SELECT * FROM recordatorios_sistema ORDER BY fecha_programada DESC LIMIT 20";
        $result = $con->query($query);
        if ($result) {
            while ($row_recordatorio = $result->fetch_assoc()) {
                $recordatorios[] = $row_recordatorio;
            }
        }
    } catch (Exception $e) {
        // Error al obtener recordatorios
    }
}

// Obtener sucursales
$sucursales = [];
try {
    $sucursales_query = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Activo = 1 ORDER BY Nombre_Sucursal";
    $sucursales_result = $con->query($sucursales_query);
    if ($sucursales_result) {
        while ($row_sucursal = $sucursales_result->fetch_assoc()) {
            $sucursales[] = $row_sucursal;
        }
    }
} catch (Exception $e) {
    // Error al obtener sucursales
}

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                try {
                    $titulo = $_POST['titulo'] ?? '';
                    $descripcion = $_POST['descripcion'] ?? '';
                    $fecha_programada = $_POST['fecha_programada'] ?? '';
                    $prioridad = $_POST['prioridad'] ?? 'media';
                    $tipo_envio = $_POST['tipo_envio'] ?? 'ambos';
                    $destinatarios = $_POST['destinatarios'] ?? 'todos';
                    $sucursal_id = $_POST['sucursal_id'] ?? null;
                    $mensaje_whatsapp = $_POST['mensaje_whatsapp'] ?? '';
                    $mensaje_notificacion = $_POST['mensaje_notificacion'] ?? '';
                    
                    $sql = "INSERT INTO recordatorios_sistema 
                            (titulo, descripcion, fecha_programada, prioridad, tipo_envio, destinatarios, 
                             sucursal_id, mensaje_whatsapp, mensaje_notificacion, usuario_creador) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ssssssisss", $titulo, $descripcion, $fecha_programada, $prioridad, 
                                    $tipo_envio, $destinatarios, $sucursal_id, $mensaje_whatsapp, 
                                    $mensaje_notificacion, $usuario_id);
                    
                    if ($stmt->execute()) {
                        $mensaje = "Recordatorio creado exitosamente";
                        $tipo_mensaje = 'success';
                    } else {
                        $mensaje = "Error al crear recordatorio: " . $stmt->error;
                        $tipo_mensaje = 'danger';
                    }
                } catch (Exception $e) {
                    $mensaje = "Error: " . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
                break;
                
            case 'eliminar':
                try {
                    $id = $_POST['id'] ?? 0;
                    $sql = "DELETE FROM recordatorios_sistema WHERE id_recordatorio = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $id);
                    
                    if ($stmt->execute()) {
                        $mensaje = "Recordatorio eliminado exitosamente";
                        $tipo_mensaje = 'success';
                    } else {
                        $mensaje = "Error al eliminar recordatorio";
                        $tipo_mensaje = 'danger';
                    }
                } catch (Exception $e) {
                    $mensaje = "Error: " . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
                break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios - <?php echo isset($row['Licencia']) ? $row['Licencia'] : 'Doctor Pez'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php include "header.php";?>
</head>
<body>
    <!-- Navbar Start -->
    <?php include "navbar.php";?>
    <!-- Navbar End -->

    <!-- Sidebar Start -->
    <?php include "Menu.php";?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-bell me-2"></i>
                                Sistema de Recordatorios
                            </h6>
                            <?php if ($tablas_existen): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRecordatorio">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    Nuevo Recordatorio
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($mensaje) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$tablas_existen): ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Instalación Requerida</h4>
                                <p class="text-muted">Las tablas de la base de datos para el sistema de recordatorios no están instaladas.</p>
                                <div class="mt-4">
                                    <a href="instalar_recordatorios.php" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-download me-2"></i>
                                        Instalar Sistema
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Lista de Recordatorios -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recordatorios del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recordatorios)): ?>
                                        <div class="text-center py-4">
                                            <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay recordatorios creados</h5>
                                            <p class="text-muted">Crea tu primer recordatorio para comenzar</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Título</th>
                                                        <th>Prioridad</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Programada</th>
                                                        <th>Destinatarios</th>
                                                        <th>Tipo Envío</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recordatorios as $recordatorio): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?= htmlspecialchars($recordatorio['titulo']) ?></strong>
                                                                <?php if (!empty($recordatorio['descripcion'])): ?>
                                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($recordatorio['descripcion'], 0, 100)) ?>...</small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?= $recordatorio['prioridad'] === 'urgente' ? 'danger' : ($recordatorio['prioridad'] === 'alta' ? 'warning' : ($recordatorio['prioridad'] === 'media' ? 'info' : 'secondary')) ?>">
                                                                    <?= ucfirst($recordatorio['prioridad']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?= $recordatorio['estado'] === 'enviado' ? 'success' : ($recordatorio['estado'] === 'error' ? 'danger' : ($recordatorio['estado'] === 'enviando' ? 'warning' : 'primary')) ?>">
                                                                    <?= ucfirst($recordatorio['estado']) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= date('d/m/Y H:i', strtotime($recordatorio['fecha_programada'])) ?></td>
                                                            <td><?= ucfirst($recordatorio['destinatarios']) ?></td>
                                                            <td><?= ucfirst($recordatorio['tipo_envio']) ?></td>
                                                            <td>
                                                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este recordatorio?')">
                                                                    <input type="hidden" name="action" value="eliminar">
                                                                    <input type="hidden" name="id" value="<?= $recordatorio['id_recordatorio'] ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Modal para crear recordatorio -->
    <?php if ($tablas_existen): ?>
    <div class="modal fade" id="modalRecordatorio" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Recordatorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="crear">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_programada" class="form-label">Fecha y Hora Programada *</label>
                                <input type="datetime-local" class="form-control" id="fecha_programada" name="fecha_programada" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="prioridad" class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad" name="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="destinatarios" class="form-label">Destinatarios</label>
                                <select class="form-select" id="destinatarios" name="destinatarios">
                                    <option value="todos">Todos los usuarios</option>
                                    <option value="sucursal">Sucursal específica</option>
                                    <option value="grupo">Grupo específico</option>
                                    <option value="individual">Usuarios individuales</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tipo_envio" class="form-label">Tipo de Envío</label>
                                <select class="form-select" id="tipo_envio" name="tipo_envio">
                                    <option value="ambos">WhatsApp y Notificación</option>
                                    <option value="whatsapp">Solo WhatsApp</option>
                                    <option value="notificacion">Solo Notificación</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="sucursal-container" style="display: none;">
                                <label for="sucursal_id" class="form-label">Sucursal</label>
                                <select class="form-select" id="sucursal_id" name="sucursal_id">
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <option value="<?= $sucursal['ID_Sucursal'] ?>"><?= $sucursal['Nombre_Sucursal'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="mensaje_whatsapp" class="form-label">Mensaje para WhatsApp</label>
                                <textarea class="form-control" id="mensaje_whatsapp" name="mensaje_whatsapp" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="mensaje_notificacion" class="form-label">Mensaje para Notificación</label>
                                <textarea class="form-control" id="mensaje_notificacion" name="mensaje_notificacion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Recordatorio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer Start -->
    <?php include "footer.php";?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <?php include "scripts.php";?>
    
    <script>
        // Ocultar spinner si existe
        if (document.getElementById('spinner')) {
            document.getElementById('spinner').style.display = 'none';
        }
        
        // Mostrar/ocultar campo de sucursal según destinatarios
        document.getElementById('destinatarios').addEventListener('change', function() {
            const sucursalContainer = document.getElementById('sucursal-container');
            if (this.value === 'sucursal') {
                sucursalContainer.style.display = 'block';
            } else {
                sucursalContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
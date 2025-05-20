<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Gestion de Notificaciones <?php echo $row['Licencia']?></title>
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
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestión de Notificaciones</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Notificaciones del Sistema</h6>
            <div class="dropdown no-arrow">
                <button id="marcarTodasLeidas" class="btn btn-sm btn-primary">
                    Marcar todas como leídas
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaNotificaciones" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán las notificaciones mediante AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarTodasNotificaciones();
    
    // Marcar todas como leídas
    document.getElementById('marcarTodasLeidas').addEventListener('click', function() {
        if (confirm('¿Estás seguro de marcar todas las notificaciones como leídas?')) {
            fetch('api/marcar_todas_leidas.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Todas las notificaciones han sido marcadas como leídas');
                    cargarTodasNotificaciones();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});

function cargarTodasNotificaciones() {
    fetch('api/get_todas_notificaciones.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tablaNotificaciones tbody');
            tbody.innerHTML = '';
            
            data.forEach(notif => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${notif.ID_Notificacion}</td>
                    <td>${notif.Tipo}</td>
                    <td>${notif.Mensaje}</td>
                    <td>${notif.FechaFormateada}</td>
                    <td>${notif.NombreSucursal || 'Todas'}</td>
                    <td>${notif.Leido == 1 ? '<span class="badge badge-success">Leída</span>' : '<span class="badge badge-warning">No leída</span>'}</td>
                    <td>
                        ${notif.Leido == 0 ? 
                            `<button class="btn btn-sm btn-primary btnMarcarLeida" data-id="${notif.ID_Notificacion}">Marcar como leída</button>` : 
                            `<button class="btn btn-sm btn-info btnMarcarNoLeida" data-id="${notif.ID_Notificacion}">Marcar como no leída</button>`
                        }
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            // Agregar eventos a los botones
            document.querySelectorAll('.btnMarcarLeida').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    marcarNotificacion(id, 1);
                });
            });
            
            document.querySelectorAll('.btnMarcarNoLeida').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    marcarNotificacion(id, 0);
                });
            });
        })
        .catch(error => console.error('Error:', error));
}

function marcarNotificacion(id, estado) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('estado', estado);
    
    fetch('api/cambiar_estado_notificacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarTodasNotificaciones();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php include "Footer.php"; ?> 
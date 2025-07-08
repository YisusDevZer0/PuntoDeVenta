<?php
// include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/db_connection_Huellas.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Asistencias</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    <style>
        #tablaAsistencias th {
            font-size: 14px;
            background-color: #ef7980 !important;
            color: white;
            padding: 8px;
        }
        #tablaAsistencias td {
            font-size: 13px;
            padding: 6px;
            color: #000;
        }
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            margin-top: 10px !important;
        }
        .dataTables_paginate .paginate_button {
            padding: 5px 10px !important;
            border: 1px solid #ef7980 !important;
            margin: 2px !important;
            cursor: pointer !important;
            font-size: 16px !important;
            color: #ef7980 !important;
            background-color: #fff !important;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #ef7980 !important;
            color: #fff !important;
            border-color: #ef7980 !important;
        }
        .dataTables_paginate .paginate_button:hover {
            background-color: #C80096 !important;
            color: #fff !important;
            border-color: #C80096 !important;
        }
    </style>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Gestión de Asistencias</h1>
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Notificaciones de Asistencia</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaAsistencias" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID Notificación</th>
                                    <th>ID Asistencia</th>
                                    <th>ID Personal</th>
                                    <th>Nombre Completo</th>
                                    <th>Domicilio</th>
                                    <th>Día</th>
                                    <th>Hora Registro</th>
                                    <th>Tipo Evento</th>
                                    <th>Fecha Notificación</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "Footer.php"; ?>
    <script>
    $(document).ready(function() {
        var mensajesCarga = [
            "Consultando asistencias...",
            "Cargando datos de asistencias...",
            "Procesando la información...",
            "Espere un momento...",
            "Cargando... ten paciencia, incluso los planetas tardaron millones de años en formarse.",
            "¿Sabías que los pingüinos también tienen que esperar mientras cargan su comida?",
            "Cargando... ¿quieres un chiste para hacer más amena la espera? ¿Por qué los pájaros no usan Facebook? Porque ya tienen Twitter.",
            "¡Alerta! Un koala está jugando con los cables de carga. Espera un momento mientras lo persuadimos.",
            "¿Sabías que las tortugas cargan a una velocidad épica? Bueno, estamos intentando superarlas.",
            "¡Espera un instante! Estamos pidiendo ayuda a los unicornios para acelerar el proceso.",
            "Cargando... mientras nuestros programadores disfrutan de una buena taza de café.",
            "Cargando... No estamos seguros de cómo llegamos aquí, pero estamos trabajando en ello."
        ];
        function mostrarCargando() {
            var randomIndex = Math.floor(Math.random() * mensajesCarga.length);
            var mensaje = mensajesCarga[randomIndex];
            document.getElementById('loading-text').innerText = mensaje;
            document.getElementById('loading-overlay').style.display = 'flex';
        }
        function ocultarCargando() {
            document.getElementById('loading-overlay').style.display = 'none';
        }
        var tabla = $('#tablaAsistencias').DataTable({
            "processing": true,
            "serverSide": false, // Si quieres paginación real en SQL, cambia a true y adapta el PHP
            "ajax": {
                "url": "Controladores/ArrayAsistencias.php",
                "type": "GET",
                "dataSrc": function(json) {
                    ocultarCargando();
                    return json.aaData;
                },
                "beforeSend": function() {
                    mostrarCargando();
                },
                "error": function() {
                    ocultarCargando();
                }
            },
            "columns": [
                { "data": "id_notificacion" },
                { "data": "id_asistencia" },
                { "data": "id_personal" },
                { "data": "nombre_completo" },
                { "data": "domicilio" },
                { "data": "nombre_dia" },
                { "data": "hora_registro" },
                { "data": "tipo_evento" },
                { "data": "fecha_notificacion" },
                { "data": "estado_notificacion" }
            ],
            "order": [[ 0, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
            },
            "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
            "initComplete": function() {
                ocultarCargando();
            }
        });
    });
    </script>
</body>
</html> 

    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text"></div>
    </div>

    <div class="table-responsive">
        <table id="Productos" class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Empleado</th>
                    <th>Nombre</th>
                    <th>Fotografía</th>
                    <th>Tipo de Usuario</th>
                    <th>Sucursal</th>
                    <th>Fecha de Creación</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // La inicialización de DataTable se maneja en PersonalActivo.js
            // para permitir filtros dinámicos
        });
    </script>

<!-- Modal para registrar la bitácora de limpieza -->
<div class="modal fade" id="RegistroLimpiezaModal" tabindex="-1" aria-labelledby="RegistroLimpiezaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="RegistroLimpiezaModalLabel">Registrar Bitácora de Limpieza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistroLimpieza">
                    <!-- Campos principales -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="area" class="form-label">Área</label>
                            <input type="text" class="form-control" id="area" name="area" required>
                        </div>
                        <div class="col-md-6">
                            <label for="semana" class="form-label">Semana</label>
                            <input type="text" class="form-control" id="semana" name="semana" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="responsable" class="form-label">Responsable</label>
                            <input type="text" class="form-control" id="responsable" name="responsable" required>
                        </div>
                        <div class="col-md-4">
                            <label for="supervisor" class="form-label">Supervisor</label>
                            <input type="text" class="form-control" id="supervisor" name="supervisor" required>
                        </div>
                        <div class="col-md-4">
                            <label for="aux_res" class="form-label">Auxiliar de Residuos</label>
                            <input type="text" class="form-control" id="aux_res" name="aux_res" required>
                        </div>
                    </div>

                    <!-- Tabla de elementos a limpiar -->
                    <h6 class="mb-3">Elementos a Limpiar</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Elemento</th>
                                <th>Lunes</th>
                                <th>Martes</th>
                                <th>Miércoles</th>
                                <th>Jueves</th>
                                <th>Viernes</th>
                                <th>Sábado</th>
                                <th>Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="tablaElementos">
                            <!-- Filas dinámicas para los elementos -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="agregarElemento()">Agregar Elemento</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarBitacora()">Guardar</button>
            </div>
        </div>
    </div>
</div>
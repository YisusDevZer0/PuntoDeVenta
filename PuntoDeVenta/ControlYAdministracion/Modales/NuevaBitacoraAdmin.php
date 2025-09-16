<!-- Modal Nueva Bitácora -->
<div class="modal fade" id="ModalNuevaBitacoraAdmin" tabindex="-1" aria-labelledby="ModalNuevaBitacoraAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ModalNuevaBitacoraAdminLabel">
                    <i class="fa fa-plus-circle me-2"></i>Nueva Bitácora de Limpieza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaBitacoraAdmin">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Sucursal -->
                        <div class="col-md-6">
                            <label for="nuevaSucursal" class="form-label">Sucursal <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevaSucursal" name="sucursal_id" required>
                                <option value="">Seleccionar sucursal</option>
                                <?php
                                $sucursales = [];
                                try {
                                    $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal";
                                    $result_sucursales = mysqli_query($conn, $sql_sucursales);
                                    if ($result_sucursales) {
                                        while($row_sucursal = mysqli_fetch_assoc($result_sucursales)) {
                                            $sucursales[] = $row_sucursal;
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Manejar error silenciosamente
                                }
                                
                                foreach($sucursales as $sucursal): ?>
                                    <option value="<?php echo $sucursal['ID_Sucursal']; ?>">
                                        <?php echo htmlspecialchars($sucursal['Nombre_Sucursal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Área -->
                        <div class="col-md-6">
                            <label for="nuevaArea" class="form-label">Área <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevaArea" name="area" required 
                                   placeholder="Ej: Cocina, Recepción, Sala de espera">
                        </div>

                        <!-- Semana -->
                        <div class="col-md-6">
                            <label for="nuevaSemana" class="form-label">Semana <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevaSemana" name="semana" required 
                                   placeholder="Ej: Semana 1, Semana 2">
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label for="nuevaEstado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevaEstado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="Activa">Activa</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Completada">Completada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>

                        <!-- Fecha Inicio -->
                        <div class="col-md-6">
                            <label for="nuevaFechaInicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="nuevaFechaInicio" name="fecha_inicio" required>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-6">
                            <label for="nuevaFechaFin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="nuevaFechaFin" name="fecha_fin" required>
                        </div>

                        <!-- Responsable -->
                        <div class="col-md-6">
                            <label for="nuevaResponsable" class="form-label">Responsable <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevaResponsable" name="responsable" required 
                                   placeholder="Nombre del responsable">
                        </div>

                        <!-- Supervisor -->
                        <div class="col-md-6">
                            <label for="nuevaSupervisor" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevaSupervisor" name="supervisor" required 
                                   placeholder="Nombre del supervisor">
                        </div>

                        <!-- Auxiliar -->
                        <div class="col-md-6">
                            <label for="nuevaAuxRes" class="form-label">Auxiliar de Respaldo</label>
                            <input type="text" class="form-control" id="nuevaAuxRes" name="aux_res" 
                                   placeholder="Nombre del auxiliar (opcional)">
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <label for="nuevaObservaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="nuevaObservaciones" name="observaciones" rows="3" 
                                      placeholder="Observaciones adicionales (opcional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-2"></i>Crear Bitácora
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
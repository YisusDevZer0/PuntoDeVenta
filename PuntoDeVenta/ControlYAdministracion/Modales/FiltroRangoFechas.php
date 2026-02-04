<div class="modal fade" id="FiltroRangoFechas" tabindex="-1" role="dialog" aria-labelledby="FiltroRangoFechasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-success">
        <div class="modal-content">
            <div class="text-center">
                <div class="modal-header" style="background-color: #ef7980 !important;">
                    <h5 class="modal-title" style="color:white;" id="FiltroRangoFechasLabel">
                        <i class="fas fa-calendar me-2"></i>Rango de Fechas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color:white; opacity: 1;"></button>
                </div>
                <div class="modal-body">
                    <form id="formFiltroRangoFechas">
                        <div class="row">
                            <!-- Fecha de inicio -->
                            <div class="col">
                                <label for="fechaInicio">Fecha de inicio</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    </div>
                                    <input type="date" id="fechaInicio" class="form-control" required>
                                </div>
                            </div>

                            <!-- Fecha de fin -->
                            <div class="col">
                                <label for="fechaFin">Fecha de fin</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    </div>
                                    <input type="date" id="fechaFin" class="form-control" required>
                                </div>
                            </div>

                            <!-- Sucursal a elegir (opcional) -->
                            <div class="col">
                                <label for="sucursalRango">Sucursal (opcional)</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-clinic-medical"></i></span>
                                    </div>
                                    <select id="sucursalRango" class="form-control">
                                        <option value="">Todas las sucursales</option>
                                        <?php 
                                        $query = $conn->query("SELECT ID_Sucursal, Nombre_Sucursal, Licencia FROM Sucursales WHERE Licencia='".$row['Licencia']."'");
                                        while ($valores = mysqli_fetch_array($query)) {
                                            echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" onclick="aplicarFiltroRangoFechas()">
                                <i class="fas fa-filter me-1"></i>Aplicar Filtro
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function aplicarFiltroRangoFechas() {
    var fechaInicio = $('#fechaInicio').val();
    var fechaFin = $('#fechaFin').val();
    var sucursalId = $('#sucursalRango').val();
    
    if (!fechaInicio || !fechaFin) {
        alert('Por favor seleccione las fechas de inicio y fin');
        return;
    }
    
    if (fechaInicio > fechaFin) {
        alert('La fecha de inicio no puede ser mayor a la fecha de fin');
        return;
    }
    
    if (typeof filtrarPorRangoFechas === 'function') {
        filtrarPorRangoFechas(fechaInicio, fechaFin, sucursalId);
    } else {
        console.error('La función filtrarPorRangoFechas no está definida');
    }
}
</script>

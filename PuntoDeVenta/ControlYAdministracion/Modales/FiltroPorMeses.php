<div class="modal fade" id="FiltroEspecificoMes" tabindex="-1" role="dialog" aria-labelledby="FiltroEspecificoMesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-success">
        <div class="modal-content">
            <div class="text-center">
                <div class="modal-header" style="background-color: #ef7980 !important;">
                    <h5 class="modal-title" style="color:white;" id="FiltroEspecificoMesLabel">
                        <i class="fas fa-calendar-week me-2"></i>Buscar por Mes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color:white; opacity: 1;"></button>
                </div>
                <div class="modal-body">
                    <form id="formFiltroMes">
                        <div class="form-row">
                            <div class="col">
                                <label for="mesesSelect">Seleccione un mes</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="calendario"><i class="far fa-calendar"></i></span>
                                    </div>
                                    <select id="mesesSelect" class="form-control" required>
                                        <option value="">Seleccione un mes:</option>
                                        <option value="01">Enero</option>
                                        <option value="02">Febrero</option>
                                        <option value="03">Marzo</option>
                                        <option value="04">Abril</option>
                                        <option value="05">Mayo</option>
                                        <option value="06">Junio</option>
                                        <option value="07">Julio</option>
                                        <option value="08">Agosto</option>
                                        <option value="09">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <label for="añosSelect">Seleccione un año</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="calendario"><i class="far fa-calendar"></i></span>
                                    </div>
                                    <select id="añosSelect" class="form-control" required>
                                        <option value="">Seleccione un año:</option>
                                        <?php
                                        $añoActual = date('Y');
                                        $añosAtras = 5;
                                        for ($i = $añoActual; $i >= ($añoActual - $añosAtras); $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" onclick="aplicarFiltroMes()">
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
function aplicarFiltroMes() {
    var mes = $('#mesesSelect').val();
    var anio = $('#añosSelect').val();
    
    if (!mes || !anio) {
        alert('Por favor seleccione un mes y un año');
        return;
    }
    
    // Las funciones están definidas en DesgloseDeTickets.php
    if (typeof filtrarPorMes === 'function') {
        filtrarPorMes(mes, anio);
    } else {
        console.log('Aplicando filtro de mes:', mes, anio);
        location.reload();
    }
}
</script>

<div class="modal fade bd-example-modal-xl" id="FiltroEspecificoMesxd" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-success">
        <div class="modal-content">
            <div class="text-center">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrado de ventas por sucursal <i class="fas fa-credit-card"></i></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="FiltroPorMesVentas" method="POST">
                        <div class="form-row">
                            <div class="col">
                                <label for="mesesSelect">Seleccione un mes</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="calendario"><i class="far fa-calendar"></i></span>
                                    </div>
                                    <select id="mesesSelect" class="form-control" name="Mes" required>
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
                                    <select id="añosSelect" class="form-control" name="anual" required>
                                        <option value="">Seleccione un año:</option>
                                        <?php
                                        $añoActual = date('Y');
                                        $añosAtras = 5; // Puedes ajustar este valor para mostrar más años pasados
                                        for ($i = $añoActual; $i >= ($añoActual - $añosAtras); $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Realizar Busqueda <i class="fas fa-exchange-alt"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



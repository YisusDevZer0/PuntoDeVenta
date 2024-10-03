<div class="modal fade bd-example-modal-xl" id="FiltroPorrangoDeFechas" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-notify modal-success">
    <div class="modal-content">
    
      <div class="text-center">
        <div class="modal-header" style="background-color: #ef7980 !important;">
          <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Búsqueda por sucursal</h5>
        </div>
       
        <div class="modal-body">
          <form method="POST" action="FiltroPorFechasVentasTotales">
            <div class="row">
              <!-- Fecha de inicio -->
              <div class="col">
                <label for="fechaInicio">Fecha de inicio</label>
                <div class="input-group mb-3">
                  <input type="date" id="fechaInicio" class="form-control" name="FechaInicio" required>
                </div>
              </div>

              <!-- Fecha de fin -->
              <div class="col">
                <label for="fechaFin">Fecha de fin</label>
                <div class="input-group mb-3">
                  <input type="date" id="fechaFin" class="form-control" name="FechaFin" required>
                </div>
              </div>

              <!-- Sucursal a elegir -->
              <div class="col">
                <label for="sucursal">Sucursal a elegir</label>
                <div class="input-group mb-3">
                  <select id="sucursal" class="form-control" name="Sucursal" required>
                    <option value="">Seleccione una Sucursal:</option>
                    <?php 
                      $query = $conn -> query ("SELECT ID_Sucursal,Nombre_Sucursal,Licencia FROM Sucursales WHERE Licencia='".$row['Licencia']."'");
                      while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <button type="submit" id="submit_registroarea" value="Guardar" class="btn btn-success">
              Realizar búsqueda <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-xl" id="FiltroPorSucursalesVentasEnTotal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-success">
    <div class="modal-content">
    
    <div class="text-center">
      <div class="modal-header" style="background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Búsqueda por sucursal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">&times;</span>
        </button>
      </div>
     
      <div class="modal-body">
        <form action="javascript:void(0)" method="post" id="CambiaDeSucursal">
          <div class="row">
            <!-- Mes dinámico -->
            <div class="col">
              <label for="mesSelect">Mes actual</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="mesIcon"><i class="far fa-calendar-alt"></i></span>
                </div>
                <select id="mesSelect" class="form-control" name="Mes" required>
                  <option value="">Seleccione un mes:</option>
                  <option value="1">Enero</option>
                  <option value="2">Febrero</option>
                  <option value="3">Marzo</option>
                  <option value="4">Abril</option>
                  <option value="5">Mayo</option>
                  <option value="6">Junio</option>
                  <option value="7">Julio</option>
                  <option value="8">Agosto</option>
                  <option value="9">Septiembre</option>
                  <option value="10">Octubre</option>
                  <option value="11">Noviembre</option>
                  <option value="12">Diciembre</option>
                </select>
              </div>
            </div>

            <!-- Año dinámico -->
            <div class="col">
              <label for="anioSelect">Año a elegir</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="anioIcon"><i class="far fa-calendar"></i></span>
                </div>
                <select id="anioSelect" class="form-control" name="Anio" required></select>
              </div>
            </div>

            <!-- Sucursal a elegir -->
            <div class="col">
              <label for="sucursal">Sucursal a elegir</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="sucursalIcon"><i class="far fa-hospital"></i></span>
                </div>
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

          <input type="text" name="user" hidden value="<?php echo $row['Id_PvUser']?>">

          <button type="submit" id="submit_registroarea" value="Guardar" class="btn btn-success">
            Aplicar cambio <i class="fas fa-exchange-alt"></i>
          </button>
        </form>
      </div>
    </div>
    </div>
  </div>
</div>

<script>
// Llenar el select con los años a partir de 2024 dinámicamente
document.addEventListener('DOMContentLoaded', function() {
  const anioSelect = document.getElementById('anioSelect');
  const currentYear = new Date().getFullYear();
  const startYear = 2024;
  
  for (let year = startYear; year <= currentYear + 5; year++) {
    let option = document.createElement('option');
    option.value = year;
    option.text = year;
    anioSelect.appendChild(option);
  }
});
</script>

<!-- Modal -->
<div class="modal fade" id="DescargaReporteInventarios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header" style=" background-color: #ef7980 !important;">
    <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Generar reporte de inventarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
            <form id="NewTypeUser" target="_blank" method="GET" action="GeneraReporteInventarios" >
                  <!-- Agrega un campo oculto para el token CSRF -->
    
                  <div class="mb-3">
  <label for="tipouser" class="form-label">Seleccione fecha</label>
  <select class="form-control" name="fecha" id="nombreservicio">
    <!-- Opciones se llenarán dinámicamente con JavaScript -->
  </select>
</div>

                
               
                
                          <!-- Agrega los otros campos del formulario de manera similar -->
                <!-- ... -->

                <button type="submit" class="btn btn-primary">Generar reporte</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
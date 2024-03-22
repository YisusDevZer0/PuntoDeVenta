<!-- Central Modal Medium Info -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Nueva sucursal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
               <form enctype="multipart/form-data" id="AgregaProductos">
         <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Codigo de barras<span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-barcode"></i></span>
  </div>
  <input type="text" class="form-control " id="codbarrap" name="CodBarraP" placeholder="Escanee o ingrese código" >
    </div>
    </div>
    
    <div class="col">
      
    <label for="exampleFormControlInput1">Clave adicional <span class="text-info">Opcional</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" ><i class="fas fa-barcode"></i></span>
  </div>
  <input type="text" class="form-control " name="Clav" id="clav"  placeholder="Ingrese código" aria-describedby="basic-addon1" maxlength="60">            
</div><label for="clav" class="error"></div>
<div class="col">
    <label for="exampleFormControlInput1">Nombre / Descripcion <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-pencil-alt"></i></span>
  </div>
  <textarea class="form-control" id="nombreprod" name="NombreProd" rows="3"></textarea>
          
    </div><label for="nombreprod" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Componente activo<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <!-- <select id = "componente" class = "form-control" name = "ComponenteActivo">
                                               <option value="">Seleccione una presentacion:</option>
        </select> -->
    </div><label for="presentacion" class="error">
    </div>
</div>

                <button type="submit" class="btn btn-primary">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
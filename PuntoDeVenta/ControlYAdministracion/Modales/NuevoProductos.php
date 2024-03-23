<!-- Central Modal Medium Info -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center">
             <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
               <form enctype="multipart/form-data" id="AgregaProductos">
         <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1" style="color: black;" style="color: black;">Codigo de barras<span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <input type="text" class="form-control " id="codbarrap" name="CodBarraP" placeholder="Escanee o ingrese código" >
    </div>
    </div>
    
    <div class="col">
      
    <label for="exampleFormControlInput1" style="color: black;" style="color: black;">Clave interna <span class="text-info">Opcional</span></label>
     <div class="input-group mb-3">
 
  <input type="text" class="form-control " name="Clav" id="clav"  placeholder="Ingrese código" aria-describedby="basic-addon1" maxlength="60">            
</div><label for="clav" class="error"></div>
<div class="col">
    <label for="exampleFormControlInput1" style="color: black;" style="color: black;">Nombre / Descripcion <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-pencil-alt"></i></span>
  </div>
  <textarea class="form-control" id="nombreprod" name="NombreProd" rows="3"></textarea>
          
    </div><label for="nombreprod" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1" style="color: black;">Componente activo<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  
   <select id = "componente" class = "form-control" name = "ComponenteActivo">
                                               <option value="">Seleccione una presentacion:</option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Componentes");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Com"].'">'.$valores["Nom_Com"].'</option>';
          }
        ?> 
                                              </select>
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
</div></div>
 
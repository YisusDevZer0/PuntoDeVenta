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

<!-- SEGUNDA SECCION -->
<script>
    function validarPrecios() {
      var precioVenta = document.getElementById('pv').value;
      var precioCompra = document.getElementById('pc').value;

      if (precioVenta <= precioCompra) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El precio de venta debe ser mayor al precio de compra.',
          showCancelButton: false,
          confirmButtonText: 'Aceptar',
        });

        document.getElementById('EnviarDatos').disabled = true;
      } else {
        document.getElementById('EnviarDatos').disabled = false;
      }
    }
  </script> 
    <div class="row">
    <div class="col">
      
    <label for="exampleFormControlInput1">Precio venta <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" ><i class="fas fa-tag"></i></span>
  </div>
  <input type="number" class="form-control " id="pv" name="PV" placeholder="Ingrese precio de venta" onchange="validarPrecios()" >
</div><label for="pv" class="error"></div>


    <div class="col">
    <label for="exampleFormControlInput1">Precio compra <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-tags"></i></span>
  </div>
  <input type="number" class="form-control " id="pc" name="PC" placeholder="Ingrese precio de compra" >
    </div><label for="pc" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Minimo existencia <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-minus"></i></span>
  </div>
  <input type="text" class="form-control " name="MinE" id="mine" placeholder="Ingrese minimo de existencia" aria-describedby="basic-addon1" onchange="validarPrecios()" >           
    </div><label for="mine" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Maximo existencia<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-plus"></i></span>
  </div>
  <input type="text" class="form-control " name="MaxE" id="maxe"  placeholder="Ingrese maximo de existencia" aria-describedby="basic-addon1" >           
    </div><label for="maxe" class="error">
    </div>  </div>

<!-- SEGUNDA SECCION FIN -->

                <button type="submit" class="btn btn-primary">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div></div>
 
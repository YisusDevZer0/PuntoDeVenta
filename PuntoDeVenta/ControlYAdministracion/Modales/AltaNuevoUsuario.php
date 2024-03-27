<!-- Central Modal Medium Info -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Agregar Nuevo Personal</h5>
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
    <label for="exampleFormControlInput1" style="color: black;" style="color: black;">Codigo de Empleado<span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <input type="text" class="form-control "  placeholder="Escanee o ingrese código" >
    </div>
    </div>
    
    <div class="col">
      
    <label for="exampleFormControlInput1" style="color: black;" style="color: black;">Nombre y apellidos </label>
     <div class="input-group mb-3">
 
  <input type="text" class="form-control " name="Clav" id="clav"  placeholder="Ingrese código" aria-describedby="basic-addon1" maxlength="60">            
</div><label for="clav" class="error"></div>
    
</div>

<!-- SEGUNDA SECCION -->

    <div class="row">
    <div class="col">
      
    <label for="exampleFormControlInput1">Correo <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control " id="pv" name="PV" placeholder="Ingrese precio de venta" onchange="validarPrecios()" >
</div><label for="pv" class="error"></div>

<div class="col">
    <label for="exampleFormControlInput1">Contraseña <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-tags"></i></span>
  </div>
  <input type="text" class="form-control " id="pc" name="PC" placeholder="Ingrese precio de compra" >
    </div><label for="pc" class="error">
    </div>
    
   
      </div>

<!-- SEGUNDA SECCION FIN -->


<!-- Tercera SECCION -->
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Tipo de usuario <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
 
   <select id = "tip" class = "form-control" name = "Tip">
                                               <option value="">Selecciona un tipo de usuario:</option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM `Tipos_Usuarios`");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_User"].'">'.$valores["TipoUsuario"].'</option>';
          }
        ?>  </select> 
    </div><label for="tip" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal<span class="text-danger">*</span></label>
    <div class="input-group mb-3">

   <select id = "categoria" class = "form-control" name = "Categoria">
                                               <option value="">Seleccione una sucursal:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM Sucursales");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
          }
        ?>  </select>
    </div><label for="categoria" class="error">
    </div>
   
  
  

   </div>

<!-- TERCERA SECCION FIN -->


<!-- CUARTA SECCION -->



<!-- Tercera SECCION -->
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Fecha de nacimiento <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
 
 <input type="date" class="form-control" name="fechanac" id="fechanac">
    </div><label for="tip" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Telefono<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
<input type="number" name="Tel" id="tel" class="form-control">
    </div><label for="categoria" class="error">
    </div>
   
    
   </div>

    <input type="text" class="form-control " name="Licencia" id="empresa" hidden value="<?php echo $row['Licencia']?>"aria-describedby="basic-addon1" >       
    <input type="text" class="form-control " name="Estatus" id="estatus" hidden value="Activo"aria-describedby="basic-addon1" >       
      
    <input type="text" class="form-control"  name="AgregaProductosBy" id="agrega" hidden readonly value=" <?php echo $row['Nombre_Apellidos']?>">
    <input type="text" class="form-control"  name="SistemaProductos" id="sistema" hidden readonly value="Administrador">
    
<!-- CUARTA SECCION FIN -->
                <button type="submit" class="btn btn-primary">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div></div>
 
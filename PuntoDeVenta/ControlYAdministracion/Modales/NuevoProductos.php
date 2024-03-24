<!-- Central Modal Medium Info -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Agregar Nuevo Producto</h5>
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
    <label for="exampleFormControlInput1">Tipo de servicio <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
   <select id = "tiposervicio" class = "form-control" name = "TipoServicio">
                                               <option value="">Seleccione una servicio:</option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Servicios_POS WHERE  Licencia='".$row['Licencia']."'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Servicio_ID"].'">'.$valores["Nom_Serv"].'</option>';
          }
        ?>  </select>        
    </div><label for="pc" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">¿Requiere receta? <span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  
  <select name="Receta" id="receta" class="form-control">
  <option value="">Elige una opcion</option>
  <option value="Si">Si</option>
  <option value="No">No</option>
  </select>
    </div>
    </div>
      </div>

<!-- SEGUNDA SECCION FIN -->


<!-- Tercera SECCION -->
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Tipo <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
 
   <select id = "tip" class = "form-control" name = "Tip">
                                               <option value="">Selecciona un tipo:</option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM TipProd_POS");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Tipo_Prod"].'">'.$valores["Nom_Tipo_Prod"].'</option>';
          }
        ?>  </select> 
    </div><label for="tip" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Categoria<span class="text-danger">*</span></label>
    <div class="input-group mb-3">

   <select id = "categoria" class = "form-control" name = "Categoria">
                                               <option value="">Seleccione una categoria:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM Categorias_POS");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Cat"].'">'.$valores["Nom_Cat"].'</option>';
          }
        ?>  </select>
    </div><label for="categoria" class="error">
    </div>
   
    <div class="col">
    <label for="exampleFormControlInput1">Marca <span class="text-danger">*</span></label>
    <div class="input-group mb-3">

   <select id = "marca" class = "form-control" name = "Marca">
                                               <option value="">Seleccione una marca:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM Marcas_POS ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Marca"].'">'.$valores["Nom_Marca"].'</option>';
          }
        ?>  </select>
    </div><label for="marca" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Presentacion<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <select id = "presentacion" class = "form-control" name = "Presentacion">
                                               <option value="">Seleccione una presentacion:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM Presentaciones ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Presentacion"].'">'.$valores["Nom_Presentacion"].'</option>';
          }
        ?>  </select> 
    </div><label for="presentacion" class="error">
    </div>

   </div>

<!-- TERCERA SECCION FIN -->


<!-- CUARTA SECCION -->



<!-- Tercera SECCION -->
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Proveedor 1 <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
 
   <select id = "proveedor" class = "form-control" name = "Proveedor">
                                               <option value="">Selecciona un tipo:</option>
                                               <?php
          $query = $conn -> query ("SELECT * FROM Proveedores");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select> 
    </div><label for="tip" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Proveedor 2<span class="text-danger">*</span></label>
    <div class="input-group mb-3">

   <select id = "proveedor2" class = "form-control" name = "Prov2">
                                               <option value="">Seleccione una categoria:</option>
        <?php
          $query = $conn -> query ("SELECT * FROM Proveedores");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select>
    </div><label for="categoria" class="error">
    </div>
   
    
   </div>

    <input type="text" class="form-control " name="EmpresaProductos" id="empresa" hidden value="<?php echo $row['Licencia']?>"aria-describedby="basic-addon1" >       
      
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
 
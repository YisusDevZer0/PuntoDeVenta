<!-- Central Modal Medium Info -->
<div class="modal fade" id="AltaProductos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true" style="overflow-y: scroll;">
   <div class="modal-dialog modal-xl modal-notify modal-success" role="document">
     <!--Content-->
     <div class="modal-content">
       <!--Header-->
       <div class="modal-header">
         <p class="heading lead">Alta de nuevo producto</p>

         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
       <div class="alert alert-info alert-styled-left text-blue-800 content-group">
						                <span class="text-semibold">Estimado usuario, </span>
                            los campos con un  <span class="text-danger"> * </span> son campos necesarios para el correcto ingreso de datos.
						                <button type="button" class="close" data-dismiss="alert">×</button>
                          </div>
       <!--Body-->
       <div class="modal-body">
         <div class="text-center">
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
        <?php
          $query = $conn -> query ("SELECT * FROM ComponentesActivos");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Com"].'">'.$valores["Nom_Com"].'</option>';
          }
        ?>  </select> -->
    </div><label for="presentacion" class="error">
    </div>
</div>
 <!-- <script>
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
  </script> -->
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
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Tipo servicio<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-plus"></i></span>
  </div>
  <!-- <select id = "tiposervicio" class = "form-control" name = "TipoServicio">
                                               <option value="">Seleccione una servicio:</option>
        <?php
          $query = $conn -> query ("SELECT Servicio_ID,Nom_Serv,Estado,ID_H_O_D FROM Servicios_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estado='Vigente'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Servicio_ID"].'">'.$valores["Nom_Serv"].'</option>';
          }
        ?>  </select>        -->
    </div><label for="maxe" class="error">
    </div>
    </div>
    

<!-- 
    <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Porcentaje<span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-receipt"></i></span>
  </div>
  <input type="text" class="form-control " disabled >
    </div>
    </div>
    
    <div class="col">

    <label for="exampleFormControlInput1">Descuento <span class="text-info">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" ><i class="fas fa-barcode"></i></span>
  </div>
  <input type="text" class="form-control " name="Clav" id="clav"  aria-describedby="basic-addon1" maxlength="60">            
</div><label for="clav" class="error"></div>
<div class="col">
    <label for="exampleFormControlInput1">Precio de promocion <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-at"></i></span>
  </div>
  <input type="text" class="form-control " id="nombreprod" name="NombreProd">          
    </div><label for="nombreprod" class="error">
    </div>
</div> -->
<div class="row">
<div class="col">
    <label for="exampleFormControlInput1">¿Requiere receta? <span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-dolly-flatbed"></i></span>
  </div>
  <select name="Receta" id="receta" class="form-control">
  <option value="">Elige una opcion</option>
  <option value="Si">Si</option>
  <option value="No">No</option>
  </select>
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Lote<span class="text-danger">*</span> </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-dolly-flatbed"></i></span>
  </div>
  <input type="text" class="form-control " name="LoteProd" id="loteprod" placeholder="Ingresa el lote">
    </div>
    </div>
    
    <div class="col">

    <label for="exampleFormControlInput1">Fecha caducidad <span class="text-info">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" ><i class="far fa-calendar-alt"></i></span>
  </div>
  <input type="date" class="form-control " name="FechaCad" id="fechacad"  aria-describedby="basic-addon1" maxlength="60">            
</div><label for="clav" class="error"></div>
<div class="col">
    <label for="exampleFormControlInput1">Existencia Cedis <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-list-ol"></i></span>
  </div>
  <input type="text" class="form-control " id="existenciacedis" name="ExistenciaCedis" placeholder="Ingresa las existencias">          
    </div><label for="nombreprod" class="error">
    </div>
</div>

   <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Tipo <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <!-- <select id = "tip" class = "form-control" name = "Tip">
                                               <option value="">Selecciona un tipo:</option>
        <?php
          $query = $conn -> query ("SELECT 	Tip_Prod_ID,Nom_Tipo_Prod,Estado,ID_H_O_D FROM TipProd_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estado='Vigente'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Tipo_Prod"].'">'.$valores["Nom_Tipo_Prod"].'</option>';
          }
        ?>  </select> -->
    </div><label for="tip" class="error">
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Categoria<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <!-- <select id = "categoria" class = "form-control" name = "Categoria">
                                               <option value="">Seleccione una categoria:</option>
        <?php
          $query = $conn -> query ("SELECT 	Cat_ID,Nom_Cat,Estado,ID_H_O_D FROM Categorias_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estado='Vigente'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Cat"].'">'.$valores["Nom_Cat"].'</option>';
          }
        ?>  </select> -->
    </div><label for="categoria" class="error">
    </div>
   
    <div class="col">
    <label for="exampleFormControlInput1">Marca <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <!-- <select id = "marca" class = "form-control" name = "Marca">
                                               <option value="">Seleccione una marca:</option>
        <?php
          $query = $conn -> query ("SELECT Marca_ID,Nom_Marca,ID_H_O_D,Estado FROM Marcas_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estado='Vigente'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Marca"].'">'.$valores["Nom_Marca"].'</option>';
          }
        ?>  </select> -->
    </div><label for="marca" class="error">
    </div>
    

   </div>

   <div class="row">
    
    <div class="col">
    <label for="exampleFormControlInput1">Presentacion<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-caret-down"></i></span>
  </div>
  <!-- <select id = "presentacion" class = "form-control" name = "Presentacion">
                                               <option value="">Seleccione una presentacion:</option>
        <?php
          $query = $conn -> query ("SELECT 	Pprod_ID,Nom_Presentacion,ID_H_O_D,Estado FROM Presentacion_Prod_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."'AND Estado='Vigente'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nom_Presentacion"].'">'.$valores["Nom_Presentacion"].'</option>';
          }
        ?>  </select> -->
    </div><label for="presentacion" class="error">
    </div>
   
    <div class="col">
    <label for="exampleFormControlInput1">Proveedor 1 <span class="text-danger">*</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-people-carry"></i></span>
  </div>
  <!-- <select id = "proveedor1" class = "form-control" name = "Provee1" onchange="verify(this)" onchange="Proveedorn1()">
                                               <option value="">Seleccione un proveedor:</option>
        <?php
          $query = $conn -> query ("SELECT 	ID_Proveedor,Nombre_Proveedor,ID_H_O_D,Estatus FROM Proveedores_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estatus='Alta'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select> -->
    </div><label for="Provee1" class="error">
    </div>
    
    <div class="col">
    <label for="exampleFormControlInput1">Proveedor 2<span class="text-info">Opcional</span></label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" ><i class="fas fa-people-carry"></i></span>
  </div>
  <!-- <select id = "proveedor2" class = "form-control" name = "Provee2" onchange="verify(this)">
                                               <option value=""></option>
        <?php
          $query = $conn -> query ("SELECT 	ID_Proveedor,Nombre_Proveedor,ID_H_O_D,Estatus FROM Proveedores_POS WHERE  ID_H_O_D='".$row['ID_H_O_D']."' AND Estatus='Alta'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_Proveedor"].'">'.$valores["Nombre_Proveedor"].'</option>';
          }
        ?>  </select> -->
    </div><label for="Provee2" class="error">
    </div>
   
 
   </div>
   
  
   
   
    

   </div>
   <div id="errorproveedor" class="alert alert-danger" role="alert" style="display: none;">No puedes repetir el proveedor</div>
    <input type="text" class="form-control " name="EmpresaProductos" id="empresa" hidden value="<?php echo $row['ID_H_O_D']?>"aria-describedby="basic-addon1" >       
    <input type="text" class="form-control " name="RevProvee1" hidden id="revprovee1"  >   
    <input type="text" class="form-control " name="RevProvee2" hidden id="revprovee2"  >   
    <input type="text" class="form-control"  name="AgregaProductosBy" id="agrega" hidden readonly value=" <?php echo $row['Nombre_Apellidos']?>">
    <input type="text" class="form-control"  name="SistemaProductos" id="sistema" hidden readonly value=" POS <?php echo $row['Nombre_rol']?>">
    
   

  

       </div>

       <!--Footer-->
       <div class="modal-footer justify-content-center">
       <button type="submit"   id="EnviarDatos" value="Guardar" class="btn btn-success" onclick="validarPrecios()">Guardar <i class="fas fa-save"></i></button>
                                        </form>
         
       </div>
     </div>
     <!--/.Content-->
   </div>
 </div>
 </div>


 <script type="text/javascript">
 var div1 = document.getElementById('errorproveedor');
    	function verify(s1) {
        var combo = document.getElementById("proveedor1");
var selected = combo.options[combo.selectedIndex].text;
$("#revprovee1").val(selected);
var combo = document.getElementById("proveedor2");
var selected = combo.options[combo.selectedIndex].text;
$("#revprovee2").val(selected);
    		var s2;
    		for (var i=1;i<=3;i++) {
    			s2 = document.getElementById('proveedor' + i);
    			if (s1.value == s2.value && s1 != s2) {
            div1.style.display = 'block';
            document.getElementById("EnviarDatos").disabled = true
    				s1.options[0].selected = true;
    				return;
          }
          else{
            div1.style.display = 'none';
            document.getElementById("EnviarDatos").disabled = false
          }
    		}
    	}
    </script>
 <!-- Central Modal Medium Info-->
 <script type="text/javascript">
  
$(function() {
  
    
$("#vigencia").on('change', function() {

  var selectValue = $(this).val();
  switch (selectValue) {

    case "background-color: #2BBB1D !important;":
        $("#SiVigente").show();
                        
                       
                        $("#Quizasproximo").hide();   
                      
     
      break;

   
      case "background-color: #ECB442 !important;":
        $("#Quizasproximo").show();    
      
        $("#SiVigente").hide();
        $("#VigenciaBD").hide();
     
      
      break;
      case "":
        $("#NoVigente").hide();
        $("#Quizasproximo").hide();    
        $("#SiVigente").hide();
        $("#VigenciaBD").hide();
        
     
      
      break;
     
    

  }
 
}).change();

});

</script>

<style>
    			.divOculto {
			display: none;
		}
</style>

<script>
$(document).ready(function() {
    $("#AltaProductos").keypress(function(e) {
        if (e.which == 13) {
            return false;
           
        }
    });
});
</script>
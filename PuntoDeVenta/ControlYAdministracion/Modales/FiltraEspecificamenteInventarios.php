
  
      <div class="modal fade bd-example-modal-xl" id="FiltroEspecifico" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-notify modal-success">
    <div class="modal-content">
    
    <div class="text-center">
    <div class="modal-header" style=" background-color: #ef7980 !important;">
    <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Cambiar de sucursal</h5>

         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
     
      <div class="modal-body">
     
 <form action="javascript:void(0)" method="post" id="CambiaDeSucursal">
    
 
 <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal Actual </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="far fa-hospital"></i></span>
  </div>
  <input type="text" class="form-control " disabled readonly  value="<?php echo $row['Nombre_Sucursal']?>">
    </div>
    </div>
    
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal a elegir </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="far fa-hospital"></i></span>
  </div>
  <select id = "sucursal" class = "form-control" name = "Sucursal" required >
                                               <option value="">Seleccione una Sucursal:</option>
        <?php 
          $query = $conn -> query ("SELECT ID_Sucursal,Nombre_Sucursal,Licencia FROM Sucursales WHERE  Licencia='".$row['Licencia']."'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
          }
        ?>  </select>
    </div>
    
    <input type="text"  name="user" hidden value="<?php echo $row['Id_PvUser']?>">
  <div>     </div>
  </div>  </div>
      <button type="submit"  id="submit_registroarea" value="Guardar" class="btn btn-success">Aplicar cambio de sucursal <i class="fas fa-exchange-alt"></i></button>
                                        </form>
                                        </div>
                                        </div>
     
    </div>
  </div>
  </div>
  </div>
  
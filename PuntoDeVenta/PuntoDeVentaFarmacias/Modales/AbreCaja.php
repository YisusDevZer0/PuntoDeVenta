<?php
date_default_timezone_set("America/Monterrey");
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id=null;
$sql1= "SELECT Fondos_Cajas.ID_Fon_Caja,Fondos_Cajas.Fk_Sucursal,Fondos_Cajas.Fondo_Caja,Fondos_Cajas.Licencia, 
Fondos_Cajas.Estatus, Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal FROM Fondos_Cajas,Sucursales 
where Fondos_Cajas.Fk_Sucursal = Sucursales.ID_Sucursal AND  Fondos_Cajas.ID_Fon_Caja = ".$_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
  $hora = date('G');
?>

<?php if($Especialistas!=null):?>

<form action="javascript:void(0)" method="post" id="OpenCaja" >
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Cantidad asignada en fondo de caja </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <input type="text" class="form-control " hidden name="FkFondo" id="fkfondo" readonly value="<?php echo $Especialistas->ID_Fon_Caja; ?>">
  <input type="number" class="form-control "  id="cantidad" name="Cantidad" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1" >  
    </div>
    </div>
    
   
    <div class="col">
    <label for="exampleFormControlInput1">Empleado<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
  <input type="text" class="form-control " readonly  name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']?>" aria-describedby="basic-addon1" >            
</div></div></div>

<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <input type="text" class="form-control " readonly  value="<?php echo $Especialistas->Nombre_Sucursal; ?>" aria-describedby="basic-addon1" >       
  <input type="text" class="form-control " readonly name="Sucursal" id="sucursal" hidden value="<?php echo $Especialistas->Fk_Sucursal; ?>" aria-describedby="basic-addon1" >       
    </div>
    </div>
    
   
    <div class="col">
    <label for="exampleFormControlInput1">Fecha<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
  <input type="text" class="form-control " readonly name="Fecha" id="fecha" value="<?php echo $fcha; ?>" aria-describedby="basic-addon1" >   
  <input type="text" class="form-control " hidden readonly name="Asignacion" id="asignacion" value="1" aria-describedby="basic-addon1" >            
</div></div></div>
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Turno </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <select name="Turno" id="turno"  onchange="TurnoElegido();"class="form-control">
  <option value="">Escoge un turno</option>
 
  <option value="Matutino">Matutino</option>
  <option value="Vespertino">Vespertino</option>
  <option value="Nocturno">Nocturno</option>
  </select>      
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Cantidad total en caja<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-coins"></i></span>
  </div>
  <input type="number" class="form-control " onfocus="habilitar();" step="any" name="TotalCaja" id="resultado" readonly   aria-describedby="basic-addon1" >     
  </div>
</div><label for="resultado" class="error"></div>
<div class="" id="Ok" role="alert">
  
</div>
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Enfermero en turno </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <select name="NombreEnfemero" id="nombreenfermero" class = "form-control"  onchange="CapturaNombreEnfermero();" required>
                                               <option value="">Seleccione un enfermero:</option>
                                               <option value="Ninguno">Ninguno</option>
                                               <option value="Otro">Otro</option>
        <?php
          $query = $conn -> query ("SELECT Enfermero_ID,Nombre_Apellidos,ID_H_O_D,Fk_Sucursal,Estatus FROM Personal_Enfermeria WHERE Estatus='Vigente' AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Sucursal='".$row['Fk_Sucursal']."' ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Apellidos"].'">'.$valores["Nombre_Apellidos"].'</option>';
          }
        ?>  </select>  
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Médico en turno<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-coins"></i></span>
  </div>
  <select name="NombreMedico" id="nombremedicoo" class = "form-control"  onchange="CapturaNombreMedico();" required>
                                               <option value="">Seleccione un médico:</option>
                                               <option value="Ninguno">Ninguno</option>
                                               <option value="Otro">Otro</option>
        <?php
          $query = $conn -> query ("SELECT Medico_ID,Nombre_Apellidos,ID_H_O_D,Fk_Sucursal,Estatus FROM Personal_Medico WHERE Estatus='Vigente' AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Sucursal='".$row['Fk_Sucursal']."' ");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["Nombre_Apellidos"].'">'.$valores["Nombre_Apellidos"].'</option>';
          }
        ?>  </select>  
  </div>
  <input type="text" name="MedicoEnturno" id="medicoenturno" hidden >
  <input type="text" name="EnfermeroEnturno" id="enfermeroturno" hidden >
</div><label for="resultado" class="error"></div>
  
  <button type="submit"  id="submit"  class="btn btn-info">Abrir caja <i class="fas fa-check"></i></button>
    


 <input type="text" hidden name="Empresa" value="<?php echo $row['ID_H_O_D']?>">
 
 <input type="text" hidden name="Estatus" value="Abierta">
 <input type="text" hidden name="CodEstatus" value="background-color: #2BBB1D !important;">
 <input type="text"  hidden name="Sistema" value="POS <?php echo $row['Nombre_rol']?>">
                          
</form>

<form method="post" 
      target="print_popup" 
      action="http://localhost:8080/ticket/TicketAperturaCaja.php"
      onsubmit="window.open('about:blank','print_popup','width=600,height=600');"  id="GeneraTicketAperturaCaja">

   
      <input type="text" class="form-control "   readonly name="VendedorTicket"  readonly value="<?php echo $row['Nombre_Apellidos']?>">
      <input type="text" class="form-control "   readonly name="TurnoTicket" id="turnoticket"  >
      <input type="number" class="form-control "   name="FondoBase" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1" >  
      <input type="number" class="form-control "  step="any" name="TotalCajaDeApertura" id="resultadoticket" readonly   aria-describedby="basic-addon1" >    
     
      <input type="datetime" name="Horadeimpresion" value="<?php echo date('h:i:s A');?>">
      <input type="text" class="form-control" name="SucursalApertura" readonly  value="<?php echo $Especialistas->Nombre_Sucursal; ?>" aria-describedby="basic-addon1" >     
      <button type="submit"  id="EnviaTicket"  class="btn btn-info">Realizar abono <i class="fas fa-money-check-alt"></i></button>
</form>

<script src="js/AbreCaja.js"></script>
<script src="js/ContadorDinero.js"></script>
<script src="js/Sumadinero.js"></script>
<?php else:?>
  <p class="alert alert-danger"><i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i> No encontramos algún fondo de caja asignado, por favor verifica e intenta de nuevo <i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i></p>
<?php endif;?>
 <script>

function CapturaNombreEnfermero() {

var nombredelpersonalenfermero = document.getElementById("nombreenfermero").value;
//Se actualiza en municipio inm

document.getElementById("enfermeroturno").value = nombredelpersonalenfermero;

}

function CapturaNombreMedico() {

var nombredelpersonalmedico = document.getElementById("nombremedicoo").value;
//Se actualiza en municipio inm

document.getElementById("medicoenturno").value = nombredelpersonalmedico;


}

 </script>
<script type="text/javascript">
function TurnoElegido()
{
var combo = document.getElementById("turno");
var selected = combo.options[combo.selectedIndex].text;
$("#turnoticket").val(selected);
}


    function habilitar()

    {

        var camp1= document.getElementById('cantidad');
        var camp2= document.getElementById('resultado');
        var boton= document.getElementById('submit');

        if (camp1.value != camp2.value) {
         
          document.getElementById("Ok").className = "alert alert-danger";
            document.getElementById("Ok").innerHTML="El valor de fondo de caja no coincide, verifica e intentalo de nuevo, se mantendra bloqueado el boton mientras tanto";
           
            boton.disabled = true;
        }else {
            boton.disabled = false;
            document.getElementById("Ok").className = "alert alert-success";
            document.getElementById("Ok").innerHTML="El valor de fondo de caja coincide con la suma del total de billetes y monedas";
     
           
        }
    }



</script>

<?php
// ... (tu código de consulta aquí)

// Verifica si hay resultados en la consulta
if ($resultset && mysqli_num_rows($resultset) > 0) {
    // Asigna los resultados a $ValorCaja
    $ValorCaja = mysqli_fetch_assoc($resultset);

    // Verifica si $ValorCaja no es null antes de acceder a sus propiedades
    if ($ValorCaja !== null && isset($ValorCaja["Estatus"])) {
        // Verifica el estado de la caja
        if ($ValorCaja["Estatus"] == 'Abierta') {
            echo '
            <script>
                $(document).ready(function() {
                    $("#submit").attr("disabled", false);
                });
            </script>
            ';
        } else {
            echo '
            <script>
                $(document).ready(function() {
                    $("#submit").attr("disabled", true);
                });
            </script>
            ';
        }
    } else {
        // Mostrar un mensaje si $ValorCaja es null o no tiene la propiedad "Estatus"
        echo "Error: No se pudo obtener la información de la caja.";
    }
} else {
    // Mostrar un mensaje si no hay resultados
    echo "Por el momento sin turno.";
}
?>

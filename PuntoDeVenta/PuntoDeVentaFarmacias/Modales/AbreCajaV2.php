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
<style>
  .input-negro {
    color: black;
  }
</style>

<!-- Botón para mostrar el formulario -->
<button id="mostrarFormulario" class="btn btn-primary">Abrir caja</button>

<!-- Formulario oculto inicialmente -->
<form action="javascript:void(0)" method="post" id="OpenCaja" style="display: none;">
  <div class="row">
    <div class="col">
      <label for="exampleFormControlInput1">Cantidad asignada en fondo de caja </label>
      <div class="input-group mb-3">
        <input type="number" class="form-control input-negro" id="cantidad" name="Cantidad" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1">
      </div>
    </div>
  </div>

  <!-- Contadores alineados en dos columnas -->
  <div class="row">
    <div class="col-md-6">
      <h5>Billetes</h5>
      <div id="contadorBilletes">
        <label>Billetes de $1000:</label>
        <input type="number" id="billetes1000" class="form-control mb-2" value="0" min="0">
        <label>Billetes de $500:</label>
        <input type="number" id="billetes500" class="form-control mb-2" value="0" min="0">
        <label>Billetes de $200:</label>
        <input type="number" id="billetes200" class="form-control mb-2" value="0" min="0">
        <label>Billetes de $100:</label>
        <input type="number" id="billetes100" class="form-control mb-2" value="0" min="0">
        <label>Billetes de $50:</label>
        <input type="number" id="billetes50" class="form-control mb-2" value="0" min="0">
        <label>Billetes de $20:</label>
        <input type="number" id="billetes20" class="form-control mb-2" value="0" min="0">
      </div>
    </div>

    <div class="col-md-6">
      <h5>Monedas</h5>
      <div id="contadorMonedas">
        <label>Monedas de $10:</label>
        <input type="number" id="monedas10" class="form-control mb-2" value="0" min="0">
        <label>Monedas de $5:</label>
        <input type="number" id="monedas5" class="form-control mb-2" value="0" min="0">
        <label>Monedas de $2:</label>
        <input type="number" id="monedas2" class="form-control mb-2" value="0" min="0">
        <label>Monedas de $1:</label>
        <input type="number" id="monedas1" class="form-control mb-2" value="0" min="0">
      </div>
    </div>
  </div>

  <div class="col">
    <label for="exampleFormControlInput1">Cantidad total en caja<span class="text-danger">*</span></label>
    <div class="input-group mb-3">
      <input type="number" class="form-control input-negro" step="any" name="TotalCaja" id="resultado" aria-describedby="basic-addon1" readonly>
    </div>
  </div>

  <button type="submit" id="submit" class="btn btn-info">Abrir caja</button>
</form>

<script>
// Mostrar el formulario cuando el botón sea clickeado
document.getElementById("mostrarFormulario").addEventListener("click", function() {
    document.getElementById("OpenCaja").style.display = "block";
    this.style.display = "none"; // Ocultar el botón después de mostrar el formulario
});

// Actualizar el valor total en caja según el contador de billetes y monedas
function actualizarTotalCaja() {
    // Billetes
    var billetes1000 = parseInt(document.getElementById("billetes1000").value) * 1000;
    var billetes500 = parseInt(document.getElementById("billetes500").value) * 500;
    var billetes200 = parseInt(document.getElementById("billetes200").value) * 200;
    var billetes100 = parseInt(document.getElementById("billetes100").value) * 100;
    var billetes50 = parseInt(document.getElementById("billetes50").value) * 50;
    var billetes20 = parseInt(document.getElementById("billetes20").value) * 20;

    // Monedas
    var monedas10 = parseInt(document.getElementById("monedas10").value) * 10;
    var monedas5 = parseInt(document.getElementById("monedas5").value) * 5;
    var monedas2 = parseInt(document.getElementById("monedas2").value) * 2;
    var monedas1 = parseInt(document.getElementById("monedas1").value) * 1;

    // Suma total de billetes y monedas
    var total = billetes1000 + billetes500 + billetes200 + billetes100 + billetes50 + billetes20 + monedas10 + monedas5 + monedas2 + monedas1;
    document.getElementById("resultado").value = total;
}

// Escuchar los cambios en los inputs de billetes y monedas para actualizar el total en caja
document.querySelectorAll('#contadorBilletes input, #contadorMonedas input').forEach(function(input) {
    input.addEventListener('input', actualizarTotalCaja);
});
</script>

<?php else:?>
<p class="alert alert-danger"><i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i> No encontramos algún fondo de caja asignado, por favor verifica e intenta de nuevo <i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i></p>
<?php endif;?>

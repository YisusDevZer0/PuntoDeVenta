
<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";


$fcha = date("Y-m-d");
$user_id=null;
$sql1= "SELECT 
    Ventas_POS.Folio_Ticket,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Cod_Barra,
    Ventas_POS.FormaDePago,
    Ventas_POS.Fecha_venta,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.CantidadPago,
    Ventas_POS.Cambio,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
WHERE 
    Ventas_POS.Folio_Ticket = '".$_POST["id"]."'";

$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
  $user_id=null;
  $sql2= "SELECT 
    Ventas_POS.Folio_Ticket,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Cod_Barra,
    Ventas_POS.FormaDePago,
    Ventas_POS.Fecha_venta,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.CantidadPago,
    Ventas_POS.Cambio,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
WHERE 
    Ventas_POS.Folio_Ticket = '".$_POST["id"]."'";
   $query = $conn->query($sql2);
?>

<!-- Formulario de reimpresión de ticket con simulación de cobro -->

<?php if($Especialistas!=null):?>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Saldo pendiente</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="Tarjeta"><i class="fas fa-info-circle"></i></span>
            </div>
            <input type="text" class="form-control" readonly id="AbonoPendiente" value="<?php echo $Especialistas->Total_VentaG - $Especialistas->CantidadPago; ?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <label for="exampleFormControlInput1">Abonado (Monto exacto)</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="Tarjeta"><i class="fas fa-info-circle"></i></span>
            </div>
            <!-- El campo de abonado estará bloqueado con el valor del saldo pendiente -->
            <input type="text" class="form-control" readonly id="Abonado" value="<?php echo $Especialistas->Total_VentaG - $Especialistas->CantidadPago; ?>">
        </div>
    </div>
</div>

<!-- Botón para simular la liquidación del abono -->
<div class="row">
    <div class="col text-center">
        <button type="button" class="btn btn-primary" id="liquidarSaldo">Liquidar</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Simulación de liquidación de abono
    $('#liquidarSaldo').on('click', function() {
        var saldoPendiente = parseFloat($('#AbonoPendiente').val()) || 0;
        var abonado = parseFloat($('#Abonado').val()) || 0;

        // Validar que el abonado sea exactamente el saldo pendiente
        if (abonado === saldoPendiente && saldoPendiente > 0) {
            Swal.fire({
                icon: 'success',
                title: 'Liquidación exitosa',
                text: 'El saldo de ' + abonado + ' ha sido liquidado con éxito.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Recargar la página al hacer clic en OK
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El saldo pendiente debe ser liquidado completamente.',
                confirmButtonText: 'OK'
            });
        }
    });
});
</script>

<?php else:?>
	<p class="alert alert-warning">No hay resultados</p>
<?php endif;?>

<?php $fcha = date("Y-m-d");?>
<script type="text/javascript">
$(document).ready( function () {
    $('#IngresoEmpleados').DataTable({
      "order": [[ 0, "desc" ]],
      "lengthMenu": [[25,50, 150, 200, -1], [25,50, 150, 200, "Todos"]],   
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
			     },
			     "sProcessing":"Procesando...",
            },
          
        //para usar los botones   
        responsive: "true",
          dom: "B<'#colvis row'><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'bottom'ip><'clear'>'",
        buttons:[ 
			{
				extend:    'excelHtml5',
				text:      'Descargar excel  <i Descargar excel class="fas fa-file-excel"></i> ',
				titleAttr: 'Descargar excel',
        title: 'Totales de horas trabajadas de empleados Doctor pez, archivo descargado <?php echo $fcha; ?>',
				className: 'btn btn-success'
			},
		
        ],
       
   
	   
        	        
    });     
});
   
	  
	 
</script>
<?php

include("db_connection_Huellas.php");



$user_id=null;
/* $sql1="SELECT Reloj_ChecadorV2.Nombre,Reloj_ChecadorV2_Salidas.Nombre,Reloj_ChecadorV2.Fecha_Registro,
Reloj_ChecadorV2.Sucursal,Reloj_ChecadorV2.Sucursal,Reloj_ChecadorV2.Area,Reloj_ChecadorV2.Area,
Reloj_ChecadorV2_Salidas.Number_Empleado,Reloj_ChecadorV2.Number_Empleado,TIMEDIFF(Reloj_ChecadorV2_Salidas.Hora_Registro,Reloj_ChecadorV2.Hora_Registro) as
 totaltrabajo FROM Reloj_ChecadorV2,Reloj_ChecadorV2_Salidas WHERE DATE(Reloj_ChecadorV2.Fecha_Registro) = DATE_FORMAT(CURDATE(),'%Y-%m-%d') AND DATE(Reloj_ChecadorV2.Fecha_Registro) 
 BETWEEN '2022-06-28' AND '2022-07-06'  AND Reloj_ChecadorV2.Nombre = Reloj_ChecadorV2_Salidas.Nombre
  GROUP BY Reloj_ChecadorV2.Fecha_Registro"; */

$sql2 ="SELECT
p.Id_pernl AS Id_Pernl,
p.Cedula   AS Cedula,
p.Nombre_Completo AS Nombre_Completo,
p.Sexo AS Sexo,
p.Cargo_rol AS Cargo_rol,
p.Domicilio AS Domicilio,
a.Id_asis AS Id_asis,
a.FechaAsis AS FechaAsis,
a.Nombre_dia AS Nombre_dia,
a.HoIngreso AS HoIngreso,
a.HoSalida AS HoSalida,
a.Tardanzas AS Tardanzas,
a.Justifacion AS Justifacion,
a.tipoturno AS tipoturno,
a.EstadoAsis AS EstadoAsis,
a.totalhora_tr AS totalhora_tr
FROM
(
  u858848268_SistemaHuellas.personal p
JOIN u858848268_SistemaHuellas.asistenciaper a
)
WHERE
(a.Id_Pernl = p.Id_pernl);";

$query = $conn->query($sql2);




?>

<?php if($query->num_rows>0):?>
  <div class="text-center">
	<div class="table-responsive">
	<table  id="IngresoEmpleados" class="table table-hover">
<thead>
  
    <th>ID</th>
    <th>Nombre completo</th>
    <th>Puesto</th>
    <th>Sucursal</th>
    <th>Fecha de asistencia</th>
    <th>Fecha corta</th>
    <th>Hora de entrada</th>
    <th>Hora de salida</th>
    <th>Estado</th>
    <th>Horas trabajadas</th>
    
	


</thead>
<?php while ($Usuarios=$query->fetch_array()):?>
<tr>
  <td><?php echo $Usuarios["Id_asis"]; ?></td>
  <td><?php echo $Usuarios["Nombre_Completo"]; ?></td>
  <td><?php echo $Usuarios["Cargo_rol"]; ?></td>
  <td><?php echo $Usuarios["Domicilio"]; ?></td>
  <td><?php echo FechaCastellano($Usuarios["FechaAsis"]); ?></td>
  <td><?php echo $Usuarios["FechaAsis"]; ?></td>
  <td><?php echo $Usuarios["HoIngreso"]; ?></td>
  <td><?php echo $Usuarios["HoSalida"]; ?></td>
  <td><?php echo $Usuarios["EstadoAsis"]; ?></td>
  <td><?php echo convertirDecimalAHoraMinutosSegundos($Usuarios["totalhora_tr"]); ?></td>

		
</tr>
<?php endwhile;?>
</table>
</div>
</div>
<?php else:?>
	<p class="alert alert-warning">No se puede generar el total de horas laboradas, hasta que el personal marque su salida.</p>
<?php endif;?>
<?php

function fechaCastellano ($fecha) {
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}
function convertirDecimalAHoraMinutosSegundos($decimalHoras) {
  $horas = floor($decimalHoras);  // Parte entera: horas
  $minutosDecimal = ($decimalHoras - $horas) * 60;  // Decimal a minutos
  $minutos = floor($minutosDecimal);  // Parte entera: minutos
  $segundosDecimal = ($minutosDecimal - $minutos) * 60;  // Decimal a segundos
  $segundos = round($segundosDecimal);  // Redondear a segundos

  return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);  // Formatear como HH:MM:SS
}

?>
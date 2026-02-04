function CargaServicios(){
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DesgloseDeTickets.php","",function(data){
      $("#DataDeServicios").html(data);
    })
}

CargaServicios();

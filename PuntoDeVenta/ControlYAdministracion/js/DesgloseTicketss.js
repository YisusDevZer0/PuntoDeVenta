function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DesgloseDeTickets","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
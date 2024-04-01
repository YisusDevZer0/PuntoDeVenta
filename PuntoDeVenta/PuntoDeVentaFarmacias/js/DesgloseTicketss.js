function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DesgloseDeTickets","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
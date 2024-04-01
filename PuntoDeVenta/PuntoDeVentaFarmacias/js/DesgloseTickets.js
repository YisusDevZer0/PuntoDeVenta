function CargaServicios(){


    $.get("hhttps://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DesgloseDeTickets","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
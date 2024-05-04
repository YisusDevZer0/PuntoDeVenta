function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/CEDIS/Controladores/DataComponentes","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
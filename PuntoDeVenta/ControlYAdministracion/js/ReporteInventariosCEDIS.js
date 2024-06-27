function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataDeInventariios","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
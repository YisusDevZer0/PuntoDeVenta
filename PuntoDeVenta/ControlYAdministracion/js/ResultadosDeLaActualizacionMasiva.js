function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ResultadosDeProductosActualizados","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
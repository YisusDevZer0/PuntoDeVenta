function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataHistorialDeCaja","",function(data){
      $("#CajasHistorial").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
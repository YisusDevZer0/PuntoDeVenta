function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataHistorialDeCaja.php","",function(data){
      $("#CajasHistorial").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
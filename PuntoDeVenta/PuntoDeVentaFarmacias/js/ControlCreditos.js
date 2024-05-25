function CargaFCajas(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CreditosActivos.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
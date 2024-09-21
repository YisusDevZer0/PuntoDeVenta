function CargaFCajas(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CortesDeCajasRealizados.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
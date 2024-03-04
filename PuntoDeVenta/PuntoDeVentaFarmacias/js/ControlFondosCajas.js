function CargaFCajas(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/FondosCajas.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
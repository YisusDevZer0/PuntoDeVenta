function CargaFCajas(){


    $.Post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/CreditosActivos.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
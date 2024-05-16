function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/RegistrosDiariosEnergia.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
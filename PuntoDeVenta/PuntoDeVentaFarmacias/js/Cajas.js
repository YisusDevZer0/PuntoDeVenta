function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/Cajas.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
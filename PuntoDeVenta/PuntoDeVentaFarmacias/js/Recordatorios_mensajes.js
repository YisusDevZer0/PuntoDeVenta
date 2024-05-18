function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/RecordatoriosMensajes.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
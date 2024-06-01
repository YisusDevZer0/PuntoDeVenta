function CargaClientes(){


    $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/AgendaDeRevaloraciones.php","",function(data){
      $("#RevaloracionesMedicas").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
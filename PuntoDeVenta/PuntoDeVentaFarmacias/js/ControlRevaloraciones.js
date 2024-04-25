function CargaClientes(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/AgendaDeRevaloraciones","",function(data){
      $("#RevaloracionesMedicas").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
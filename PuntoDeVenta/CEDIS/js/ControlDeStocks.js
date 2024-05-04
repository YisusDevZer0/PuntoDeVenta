function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/CEDIS/Controladores/DataStocks","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DataPagosServicios.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  
  CargaServicios();

  

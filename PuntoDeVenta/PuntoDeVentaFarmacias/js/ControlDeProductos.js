function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ListadoDeProductos.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
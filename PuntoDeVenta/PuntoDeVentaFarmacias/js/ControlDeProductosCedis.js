function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ListadoDeProductosCedis.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
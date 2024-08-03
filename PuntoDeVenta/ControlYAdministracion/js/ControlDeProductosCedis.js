function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ListadoDeProductosCedis.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/CEDIS/Controladores/ListadoDeProductos.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
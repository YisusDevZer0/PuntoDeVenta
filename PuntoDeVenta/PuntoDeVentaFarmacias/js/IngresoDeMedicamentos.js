function IngresosProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/IngresosProductos.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  IngresosProductos();

  
function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/ListadoDeProductosCedis.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"CEDIS/Controladores/ListadoDeProductos.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/ListadoDeRevaloraciones.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/LotesYCaducidades.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
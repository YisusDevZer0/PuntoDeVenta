function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/VentasPorFormaDePago.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
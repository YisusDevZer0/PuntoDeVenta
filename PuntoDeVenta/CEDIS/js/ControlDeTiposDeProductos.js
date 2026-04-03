function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataTiposProductos","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
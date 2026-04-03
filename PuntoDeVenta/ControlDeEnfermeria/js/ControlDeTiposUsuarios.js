function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/TiposDeUsuarios","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
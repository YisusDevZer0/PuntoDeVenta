function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataUsuariosVigentes","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
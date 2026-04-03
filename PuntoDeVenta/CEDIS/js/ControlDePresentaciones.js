function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataPresentaciones","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
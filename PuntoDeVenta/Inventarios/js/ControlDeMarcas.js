function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataMarcas","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"RecursosHumanos/ControlYAdministracion/Controladores/TiposDeUsuarios","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
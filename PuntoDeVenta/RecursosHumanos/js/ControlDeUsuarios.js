function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"RecursosHumanos/Controladores/DataUsuariosVigentes","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"CEDIS/Controladores/DataComponentes","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
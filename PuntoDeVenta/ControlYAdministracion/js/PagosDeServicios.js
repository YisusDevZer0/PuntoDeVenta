function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataPagosServicios.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  
  CargaServicios();

  

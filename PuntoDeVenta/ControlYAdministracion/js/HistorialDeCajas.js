function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataHistorialDeCaja.php","",function(data){
      $("#CajasHistorial").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
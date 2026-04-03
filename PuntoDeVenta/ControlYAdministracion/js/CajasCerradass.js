function CargaCajas(){


    $.post((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/CajasCerradas.php","",function(data){
      $("#CajasCerradas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
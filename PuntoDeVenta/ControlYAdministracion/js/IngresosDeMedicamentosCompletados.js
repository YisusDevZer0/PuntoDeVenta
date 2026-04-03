function CargaCajas(){


    $.post((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/IngresosCompletados.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
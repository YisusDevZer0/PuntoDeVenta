function CargaCajas(){


    $.post((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/IngresosRealizadosCedis.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
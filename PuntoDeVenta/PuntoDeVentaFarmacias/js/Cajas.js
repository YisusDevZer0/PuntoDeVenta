function CargaCajas(){


    $.post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/Cajas.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
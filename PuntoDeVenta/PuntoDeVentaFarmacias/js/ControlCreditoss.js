function CargaFCajas(){


    $.Post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/CreditosActivos.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
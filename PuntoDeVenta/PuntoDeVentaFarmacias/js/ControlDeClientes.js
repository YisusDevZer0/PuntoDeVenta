function CargaClientes(){


    $.get((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/DataClientes","",function(data){
      $("#DataDeClientes").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
function CargaClientes(){


    $.post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/AgendaDeRevaloraciones.php","",function(data){
      $("#RevaloracionesMedicas").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
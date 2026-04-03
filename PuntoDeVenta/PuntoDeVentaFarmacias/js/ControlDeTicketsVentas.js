function CargaTicketsDia(){


    $.post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/TicketsEnPanelVentas.php","",function(data){
      $("#TableVentasDelDia").html(data);
    })

  }
  
  
  
  CargaTicketsDia();
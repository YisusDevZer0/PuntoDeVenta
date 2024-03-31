function CargaTicketsDia(){


    $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/TicketsEnPanelVentas.php","",function(data){
      $("#TableVentasDelDia").html(data);
    })

  }
  
  
  
  CargaTicketsDia();
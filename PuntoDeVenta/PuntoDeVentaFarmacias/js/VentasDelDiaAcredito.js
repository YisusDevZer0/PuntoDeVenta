function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/VentasDelDiaACredito.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
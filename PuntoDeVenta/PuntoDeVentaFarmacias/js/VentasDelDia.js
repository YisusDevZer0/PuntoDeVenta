function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/VentasDelDia.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
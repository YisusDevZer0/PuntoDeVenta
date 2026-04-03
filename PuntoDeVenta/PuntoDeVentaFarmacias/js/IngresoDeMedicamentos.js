function IngresosProductos(){


    $.post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/IngresosProductos.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  IngresosProductos();

  
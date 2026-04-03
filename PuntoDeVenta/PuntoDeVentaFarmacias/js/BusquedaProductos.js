function  StockPorSucursales(){


    $.post((window.__FDP_BASE_URL__||"")+"PuntoDeVentaFarmacias/Controladores/StockSucursalesV2.php","",function(data){
      $("#TableStockSucursales").html(data);
    })

  }
  
  
  
  StockPorSucursales();
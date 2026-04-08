function CargaServicios(){


    $.get("Controladores/DataStocks.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
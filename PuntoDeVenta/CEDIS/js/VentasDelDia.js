function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"CEDIS/Controladores/VentasDelDia.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
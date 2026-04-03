function CargaListadoDeProductos(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/VentasPorMasVendidos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
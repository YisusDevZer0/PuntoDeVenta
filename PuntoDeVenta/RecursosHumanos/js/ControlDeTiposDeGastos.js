function CargaServicios(){


    $.get((window.__FDP_BASE_URL__||"")+"RecursosHumanos/Controladores/DataTiposDeGastos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
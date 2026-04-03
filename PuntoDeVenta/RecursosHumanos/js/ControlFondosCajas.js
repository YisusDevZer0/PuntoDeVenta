function CargaFCajas(){


    $.get((window.__FDP_BASE_URL__||"")+"RecursosHumanos/Controladores/FondosCajas.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
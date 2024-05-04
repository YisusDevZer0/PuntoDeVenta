function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/Controladores/DataUsuariosVigentes","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
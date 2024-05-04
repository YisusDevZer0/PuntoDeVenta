function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/ControlYAdministracion/Controladores/TiposDeUsuarios","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
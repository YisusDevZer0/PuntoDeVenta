function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataMarcas","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
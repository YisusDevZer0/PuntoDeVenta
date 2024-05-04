function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/CEDIS/Controladores/DataProveedores","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
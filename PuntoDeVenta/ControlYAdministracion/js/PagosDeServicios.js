function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataPagosServicios.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  
  CargaServicios();

  

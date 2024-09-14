function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/VentasPorFormaDePago.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
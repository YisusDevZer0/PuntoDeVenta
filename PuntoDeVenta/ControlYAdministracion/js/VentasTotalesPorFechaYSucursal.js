function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/VentasPorTotalesGeneral.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
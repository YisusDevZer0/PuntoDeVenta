function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/VentasPorMasVendidos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
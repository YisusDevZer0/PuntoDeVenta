function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/VentasDelDiaACredito.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
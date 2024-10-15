function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/AbonosDeCreditosRealizados.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
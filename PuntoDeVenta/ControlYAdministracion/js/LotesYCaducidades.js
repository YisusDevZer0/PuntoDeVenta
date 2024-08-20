function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/LotesYCaducidades.php","",function(data){
      $("#DataDeProductos").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
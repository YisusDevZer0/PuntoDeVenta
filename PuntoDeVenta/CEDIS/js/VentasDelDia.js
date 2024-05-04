function CargaListadoDeProductos(){


    $.get("https://doctorpez.mx/PuntoDeVenta/CEDIS/Controladores/VentasDelDia.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaListadoDeProductos();

  
  
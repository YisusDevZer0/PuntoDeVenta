function CargaClientes(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DataConteosPausados","",function(data){
      $("#DataDeClientes").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
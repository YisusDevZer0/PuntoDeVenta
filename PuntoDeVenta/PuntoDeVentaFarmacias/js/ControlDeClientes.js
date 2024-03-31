function CargaClientes(){


    $.get("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DataClientes","",function(data){
      $("#DataDeClientes").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
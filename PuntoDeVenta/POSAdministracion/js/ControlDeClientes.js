function CargaClientes(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataClientes","",function(data){
      $("#Clientes").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
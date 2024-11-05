function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CajasVentas.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
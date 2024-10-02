function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/IngresosRealizadosCedis.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
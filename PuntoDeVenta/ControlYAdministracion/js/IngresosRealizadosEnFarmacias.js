function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/IngresosRealizadosFarmacias.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
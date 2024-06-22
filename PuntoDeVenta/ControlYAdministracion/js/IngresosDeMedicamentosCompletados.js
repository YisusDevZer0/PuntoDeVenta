function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/IngresosCompletados.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
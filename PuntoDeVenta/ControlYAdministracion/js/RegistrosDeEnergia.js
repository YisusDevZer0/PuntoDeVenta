function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/RegistrosDiariosEnergia.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
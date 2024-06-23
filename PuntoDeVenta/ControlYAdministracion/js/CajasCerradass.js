function CargaCajas(){


    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CajasCerradas.php","",function(data){
      $("#CajasCerradas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
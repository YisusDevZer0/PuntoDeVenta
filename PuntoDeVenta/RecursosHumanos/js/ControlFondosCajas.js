function CargaFCajas(){


    $.get("https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/Controladores/FondosCajas.php","",function(data){
      $("#FCajas").html(data);
    })
  
  }
  
  
  
  CargaFCajas();

  
  
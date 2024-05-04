function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/Controladores/DataGastos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
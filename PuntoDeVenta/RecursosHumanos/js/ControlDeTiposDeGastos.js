function CargaServicios(){


    $.get("https://doctorpez.mx/PuntoDeVenta/RecursosHumanos/Controladores/DataTiposDeGastos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaServicios();

  
  
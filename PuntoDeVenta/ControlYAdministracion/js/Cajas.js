function CargaCajas(){


    // Ruta relativa al módulo (válida con o sin subpath tipo /PuntoDeVenta/)
    $.post("Controladores/CajasVentas.php","",function(data){
      $("#Cajas").html(data);
    })
  
  }
  
  
  
  CargaCajas();

  
  
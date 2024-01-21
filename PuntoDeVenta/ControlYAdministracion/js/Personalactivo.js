function CargaPersonalactivo(){


    $.post("Controladores/TablaPersonalActivo.php","",function(data){
      $("#tablaProductos").html(data);
    })
  
  }
  
  
  
  CargaProductos();
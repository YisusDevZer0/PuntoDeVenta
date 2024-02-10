function CargaPersonalactivo(){


    $.post("Controladores/TablaPersonalActivo.php","",function(data){
      $("#tablaPersonalactivo").html(data);
    })
  
  }
  
  
  CargaPersonalactivo();
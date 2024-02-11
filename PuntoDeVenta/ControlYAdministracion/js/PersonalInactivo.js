function CargaPersonalinactivo(){


    $.post("Controladores/TablaPersonalNonactivo.php","",function(data){
      $("#tablaPersonalinactivo").html(data);
    })
  
  }
  
  
  CargaPersonalinactivo();
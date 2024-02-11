function CargaPersonalactivo(){


    $.post("Controladores/TablaPersonalInactivo.php","",function(data){
      $("#tablaPersonalinactivo").html(data);
    })
  
  }
  
  
  CargaPersonalactivo();
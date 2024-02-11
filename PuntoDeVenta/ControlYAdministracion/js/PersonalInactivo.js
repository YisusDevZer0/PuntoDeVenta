function CargaPersonalinactivo(){


    $.post("Controladores/TablaPersonalInactivo.php","",function(data){
      $("#tablaPersonalinactivo").html(data);
    })
  
  }
  
  
  CargaPersonalinactivo();
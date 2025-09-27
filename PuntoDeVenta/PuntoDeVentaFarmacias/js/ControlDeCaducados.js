function CargaCaducados(){
    $.get("Controladores/DataCaducados","",function(data){
      $("#DataDeCaducados").html(data);
    })
}

CargaCaducados();
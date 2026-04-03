function CargaCaducados(){
    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataCaducados","",function(data){
      $("#DataDeCaducados").html(data);
    })
}

CargaCaducados();

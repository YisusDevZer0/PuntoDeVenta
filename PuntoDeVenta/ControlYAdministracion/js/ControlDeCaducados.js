function CargaCaducados(){
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataCaducados","",function(data){
      $("#DataDeCaducados").html(data);
    })
}

CargaCaducados();

function CargaServicios(){
    $.get("Controladores/DesgloseDeTickets","",function(data){
      $("#DataDeServicios").html(data);
    })
}

CargaServicios();

  
  
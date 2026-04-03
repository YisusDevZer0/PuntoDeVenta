function CargaServicios(){
    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DesgloseDeTickets.php","",function(data){
      $("#DataDeServicios").html(data);
    })
}

CargaServicios();

function CargaParticipaciones(){
    var sorteoId = $('#filtroSorteoParticipaciones').val() || '0';
    $.post("Controladores/DataParticipaciones.php?sorteo_id=" + sorteoId,"",function(data){
      $("#ParticipacionesDisponibles").html(data);
    })
}

CargaParticipaciones();

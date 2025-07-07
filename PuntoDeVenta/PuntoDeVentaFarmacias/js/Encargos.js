function CargaServicios(){
    $.get("Controladores/DataEncargos.php","",function(data){
      $("#DataDeServicios").html(data);
    })
}
CargaServicios(); 
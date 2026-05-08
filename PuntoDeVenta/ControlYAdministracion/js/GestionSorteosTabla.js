function CargaSorteos(){
    $.post("Controladores/DataSorteos.php","",function(data){
      $("#SorteosDisponibles").html(data);
    })
}

CargaSorteos();

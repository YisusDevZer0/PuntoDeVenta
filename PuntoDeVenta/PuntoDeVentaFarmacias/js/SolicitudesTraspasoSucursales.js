function CargaServicios() {
    $.get("Controladores/DataDeSolicitudesTraspaso.php", "", function(data) {
        $("#DataDeServicios").html(data);
    });
}
CargaServicios();

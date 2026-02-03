function CargaFCajas(){
    // Cargar todos los cortes del año en curso
    var fecha_inicio = new Date().getFullYear() + '-01-01'; // Primer día del año
    var fecha_fin = new Date().getFullYear() + '-12-31'; // Último día del año
    
    var params = {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin
    };
    
    var url = "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/CortesDeCajasRealizados.php";
    url += "?" + $.param(params);
    
    $.get(url, function(data){
        $("#FCajas").html(data);
    });
}


// Cargar datos al inicio
$(document).ready(function() {
    CargaFCajas();
});
  
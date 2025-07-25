function CargaReportePorProducto(){


    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ReportePorProducto","",function(data){
      $("#DataDeServicios").html(data);
    })
  
  }
  
  
  
  CargaReportePorProducto();

  
  
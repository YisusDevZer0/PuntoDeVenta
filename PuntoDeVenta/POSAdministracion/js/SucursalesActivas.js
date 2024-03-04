function CargaSucursales(){


    $.post("Controladores/SucursalesActivas.php","",function(data){
      $("#SucursalesDisponibles").html(data);
    })
  
  }
  
  
  CargaSucursales();
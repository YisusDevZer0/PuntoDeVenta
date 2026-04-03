function CargaClientes(){


    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/DataClientes","",function(data){
      $("#Clientes").html(data);
    })
  
  }
  
  
  
  CargaClientes();

  
  
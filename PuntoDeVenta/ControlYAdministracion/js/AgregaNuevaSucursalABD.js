$(document).ready(function($){
  $("#NewTypeUser").validate({
      rules: {
          NombreSucursal: {
              required: true,
              minlength: 2,
              maxlength: 40,
              Sololetras: "",
          },
          // Agrega reglas de validación para otros campos si es necesario
      },
      messages: {
          NombreSucursal: {
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa el nombre ",
              maxlength: "No puede tener más de 40 caracteres",
              minlength: "Un nombre no puede tener solo 1 caracter"
          },
          // Agrega mensajes de validación para otros campos si es necesario
      },
      submitHandler: submitForm
  });

  function submitForm() {
      $.ajax({
          type: 'POST',
          url: "Controladores/AgregaSucursal.php",
          data: $('#NewTypeUser').serialize(),
          cache: false,
          beforeSend: function(){
              $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
          },
          success: function(dataResult){
              var dataResult = JSON.parse(dataResult);
              if(dataResult.statusCode == 250){
                  $("#submit_registro").html("Algo no salió bien.. <i class='fas fa-exclamation-triangle'></i>");
                  $('#ErrorDupli').modal('toggle');
              }
              else if(dataResult.statusCode == 200){
                  $("#submit_registro").html("Enviado <i class='fas fa-check'></i>");
                  $("#NewTypeUser")[0].reset();
                  $("#AltaTiposProductos").removeClass("in");
                  $(".modal-backdrop").remove();
                  $("#AltaTiposProductos").hide();
                  $('#Exito').modal('toggle');
              }
              else if(dataResult.statusCode == 201){
                  $("#submit_Age").html("Algo no salió bien.. <i class='fas fa-exclamation-triangle'></i>");
                  $('#ErrorData').modal('toggle');
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
              console.error("Error en la solicitud AJAX: " + textStatus, errorThrown);
              // Muestra un mensaje de error en caso de problemas con la solicitud AJAX
          }
      });
      return false;
  }
});

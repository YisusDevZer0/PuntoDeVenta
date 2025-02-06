$('document').ready(function($){
  $.validator.addMethod("Sololetras", function(value, element) {
    return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
  
  $.validator.addMethod("Telefonico", function(value, element) {
      return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar numeros!");
  
  $.validator.addMethod("Correos", function(value, element) {
      return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z09](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo valido!");
  
  $.validator.addMethod("NEmpresa", function(value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
  
  $.validator.addMethod("Problema", function(value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  // Validación del formulario
  $("#NewTypeUser").validate({
      rules: {
          NombreTipoProd: {
              required: true,
              minlength: 2,
              maxlength: 40,
              Sololetras: "",
          },
          VigenciaProdT: {
              required: true,        
          },
      },
      messages: {
          NombreTipoProd: {
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa el nombre",
              maxlength: "No puede tener mas de 40 caracteres",
              minlength: "Un nombre no puede tener solo 1 caracter"
          },
          VigenciaProdT: {
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
          },
      },
      submitHandler: submitForm
  });   

  // Función para manejar el envío del formulario
  function submitForm() {
      $.ajax({				
          type: 'POST',
          url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/NuevasCategorias.php",
          data: $('#NewTypeUser').serialize(),
          cache: false,
          beforeSend: function(){	
              $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
          },
          success: function(dataResult){
              var dataResult = JSON.parse(dataResult);

              if(dataResult.statusCode == 250) {
                  // Modal de error de duplicación
                  $("#submit_registro").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                  $('#ErrorDupli').modal('toggle'); 
                  setTimeout(function(){ 
                      $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                  }, 3000); // tiempo de espera para cambiar el botón

              } else if(dataResult.statusCode == 200) {
                  $("#submit_registro").html("Enviado <i class='fas fa-check'></i>");
                  $("#NewTypeUser")[0].reset();
                  $("#myModal").removeClass("in");
                  $(".modal-backdrop").remove();
                  $("#myModal").hide();
                  $('#successModal').modal('toggle'); 

                  setTimeout(function(){ 
                      $('#successModal').modal('hide');
                      location.reload(); // Recargar la página después de 4 segundos
                  }, 4000);
              } else if(dataResult.statusCode == 201) {
                  $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
                  $('#errorModal').modal('toggle');
                  setTimeout(function(){ 
                      $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                  }, 3000); // tiempo de espera para cambiar el botón
              }
          },
          error: function(xhr, status, error) {
              console.error("Error en la solicitud: " + error);
          }
      });
      return false;
  }   
});

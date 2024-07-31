$('document').ready(function($){
  $.validator.addMethod("Sololetras", function(value, element) {
      return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $.validator.addMethod("Telefonico", function(value, element) {
      return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar numeros!");

  $.validator.addMethod("Correos", function(value, element) {
      return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo valido!");

  $.validator.addMethod("NEmpresa", function(value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $.validator.addMethod("Problema", function(value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $("#EliminaServiciosForm").validate({
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
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa el nombre ",
              maxlength: "No puede tener mas de 40 caracteres",
              minlength: "Un nombre no puede tener solo 1 caracter",
          },
          VigenciaProdT: {
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
          },
      },
      submitHandler: submitForm
  });

  function submitForm() {
      $.ajax({
          type: 'POST',
          url: "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/RegistraGastos.php",
          data: $('#EliminaServiciosForm').serialize(),
          cache: false,
          beforeSend: function() {
              $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
          },
          success: function(dataResult) {
              var dataResult = JSON.parse(dataResult);

              if (dataResult.statusCode == 250) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Algo no salió bien...',
                      text: 'Error duplicado',
                      confirmButtonText: 'Aceptar'
                  }).then(function() {
                      $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
                  });
              } else if (dataResult.statusCode == 200) {
                  Swal.fire({
                      icon: 'success',
                      title: 'Enviado',
                      text: 'El formulario ha sido enviado correctamente',
                      confirmButtonText: 'Aceptar'
                  }).then(function() {
                      $("#EliminaServiciosForm")[0].reset();
                      $("#myModal").removeClass("in");
                      $(".modal-backdrop").remove();
                      $("#myModal").hide();
                      setTimeout(function() {
                          location.reload(); // Recargar la página completa después de 2 segundos
                      }, 2000);
                  });
              } else if (dataResult.statusCode == 201) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Algo no salió bien...',
                      text: 'Error en los datos',
                      confirmButtonText: 'Aceptar'
                  }).then(function() {
                      $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
                  });
              }
          }
      });
      return false;
  }
});

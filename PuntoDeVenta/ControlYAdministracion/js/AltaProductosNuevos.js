$('document').ready(function ($) {
  $.validator.addMethod("Sololetras", function (value, element) {
    return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $.validator.addMethod("Telefonico", function (value, element) {
    return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números!");

  $.validator.addMethod("Correos", function (value, element) {
    return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo válido!");

  $.validator.addMethod("NEmpresa", function (value, element) {
    return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $.validator.addMethod("Problema", function (value, element) {
    return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
  }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

  $("#AgregaProductos").validate({
    rules: {
      CodBarra: {
        required: true,
      },
      Clav: {
        maxlength: 15,
      },
      NombreProd: {
        required: true,
        minlength: 6,
        maxlength: 130,
      },
      PV: {
        required: true,
      },
      PC: {
        required: true,
      },
      MinE: {
        required: true,
      },
      MaxE: {
        required: true,
      },
      ExistenciaCedis: {
        required: true,
      },
      Provee1: {
        required: true,
      },
      tiposervicio: {
        required: true,
      },
    },
    messages: {
      Clav: {
        maxlength: "No puedes ingresar más de 5 caracteres",
        minlength: "Debes ingresar mínimo 3 caracteres"
      },
      NombreProd: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
        maxlength: "No puedes ingresar más de 5 caracteres",
        minlength: "Debes ingresar mínimo 3 caracteres"
      },
      Sucursal: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      PV: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      PC: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      MinE: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      MaxE: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      Provee1: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
      tiposervicio: {
        required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
      },
    },
    submitHandler: submitForm
  });

  function submitForm() {
    $.ajax({
      type: 'POST',
      url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/AltaProductos.php",
      data: $('#AgregaProductos').serialize(),
      cache: false,
      beforeSend: function () {
        $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
      },
      success: function (dataResult) {
        var dataResult = JSON.parse(dataResult);

        if (dataResult.statusCode == 250) {
          swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Algo salió mal...',
          });
          $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
        } else if (dataResult.statusCode == 200) {
          swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Registro exitoso!',
          }).then((result) => {
            if (result.isConfirmed || result.isDismissed) {
              location.reload(); // Recargar la página
            }
          });
                   $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
        } else if (dataResult.statusCode == 201) {
          swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Algo salió mal...',
          });
          $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
        }
      },
      error: function () {
        swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error de conexión...',
        });
        $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
      }
    });
    return false;
  }
});

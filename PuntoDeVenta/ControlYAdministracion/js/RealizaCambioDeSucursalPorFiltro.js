$('document').ready(function ($) {
    $.validator.addMethod("Sololetras", function (value, element) {
      return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
    $.validator.addMethod("Telefonico", function (value, element) {
      return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar numeros!");
    $.validator.addMethod("Correos", function (value, element) {
      return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo valido!");
    $.validator.addMethod("NEmpresa", function (value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
    $.validator.addMethod("Problema", function (value, element) {
      return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
  
  
    $("#CambiaDeSucursal").validate({
      rules: {
       
        Provee1:{
          required:true,
        },
      },
      messages: {
  
       
        Provee1: {
          required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
        },
  
      },
      submitHandler: submitForm
    });
    // hide messages 
  
  
    function submitForm() {
  
  
  
      $.ajax({
        type: 'POST',
        url: "Controladores/CambioDeSucursal.php",
        data: $('#CambiaDeSucursal').serialize(),
        cache: false,
        beforeSend: function () {
  
          $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
  
  
        },
        success: function (dataResult) {
          var dataResult = JSON.parse(dataResult);
   if (dataResult.statusCode == 200) {
  
            $("#submit_registro").html("Enviado <i class='fas fa-check'></i>")
  
            $("#CambiaDeSucursal")[0].reset();
            $("#FiltroEspecifico").removeClass("in");
            $(".modal-backdrop").remove();
            $("#FiltroEspecifico").hide();
            location.reload();
  
            //  Solucionar muestra de modal de exito
  
  
          }
          else if (dataResult.statusCode == 201) {
            $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
            $('#ErrorData').modal('toggle');
  
            setTimeout(function () {
              $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
            }, 3000); // abrir
  
  
          }
  
  
  
  
  
        }
      });
      return false;
    }
  });
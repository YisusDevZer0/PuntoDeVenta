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
  
  
    $("#ActualizaDatosDeTraspasos").validate({
      rules: {
  
        CodBarra:{
          required:true,
        },
        Clav: {
          maxlength: 15,
        },
       
      },
      messages: {
  
        
        NombreProd: {
          required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
          maxlength: "No puedes ingresar mas de 5 caracteres",
          minlength: "Debes ingresar minimo 3 caracteres"
        },
        Sucursal: {
          required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
        },
       
      },
      submitHandler: submitForm
    });
    // hide messages 
  
  
    function submitForm() {
  
  
  
      $.ajax({
        type: 'POST',
        url: "Controladores/ActualizaTraspasoOK.php",
        data: $('#ActualizaDatosDeTraspasos').serialize(),
        cache: false,
        beforeSend: function () {
  
          $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
  
  
        },
        success: function (dataResult) {
          var dataResult = JSON.parse(dataResult);
  
          if (dataResult.statusCode == 250) {
            var modal_lv = 0;
            $('.modal').on('shown.bs.modal', function (e) {
              $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
              $(e.currentTarget).css('zIndex', 1052 + modal_lv);
              modal_lv++
            });
  
            $('.modal').on('hidden.bs.modal', function (e) {
              modal_lv--
            });
            $("#submit_registro").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
            $('#ErrorDupli').modal('toggle');
            setTimeout(function () {
            }, 2000); // abrir
            setTimeout(function () {
              $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
            }, 3000); // abrir
  
  
          }
  
          else if (dataResult.statusCode == 200) {
  
            $("#submit_registro").html("Enviado <i class='fas fa-check'></i>")
  
            $("#ActualizaDatosDeTraspasos")[0].reset();
            $("#AltaProductos").removeClass("in");
            $(".modal-backdrop").remove();
            $("#AltaProductos").hide();
          
  
            $('#TraspasoCompleto').modal('toggle');
            setTimeout(function () {
              $('#TraspasoCompleto').modal('hide')
            }, 3000); // abrir
            setTimeout(' window.location.href = "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/ListadoDeTraspasos"; ',3000);
  
  
            //  Solucionar muestra de modal de TraspasoCompleto
  
  
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
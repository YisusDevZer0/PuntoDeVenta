$('document').ready(function($){
    $.validator.addMethod("Sololetras", function(value, element) {
      return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
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
            
			NombreTipoProd:{
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa el nombre ",
              maxlength: "No puede tener mas de 40 caracteres",
              minlength: "Un nombre no puede tener solo 1 caracter"
            
             },
             
             
              
               VigenciaProdT:{
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Dato requerido ",
                
               },               
              
		},
        submitHandler: submitForm	
	});	   
    // hide messages 
   
   
    function submitForm() {	
     
        
  
        $.ajax({				
            type : 'POST',
            url: "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/RegistraGastos.php",
            data: $('#NewTypeUser').serialize(),
            cache: false,
            beforeSend: function(){	
			
              $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
				
                    
			},
      success: function(dataResult){
        var dataResult = JSON.parse(dataResult);

        if(dataResult.statusCode==250){
            var modal_lv = 0;
            $('.modal').on('shown.bs.modal', function (e) {
              $('.modal-backdrop:last').css('zIndex',1051+modal_lv);
              $(e.currentTarget).css('zIndex',1052+modal_lv);
              modal_lv++
            });
            
            $('.modal').on('hidden.bs.modal', function (e) {
              modal_lv--
            });	
            $("#submit_registro").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
            $('#ErrorDupli').modal('toggle'); 
            setTimeout(function(){ 
        }, 2000); // abrir
        setTimeout(function(){ 
            $("#submit_registro").html("Guardar <i class='fas fa-save'></i>");
        }, 3000); // abrir

           
        }
       
        else if(dataResult.statusCode==200){
            
             $("#submit_registro").html("Enviado <i class='fas fa-check'></i>")	
           
             $("#NewTypeUser")[0].reset();
             $("#myModal").removeClass("in");
             $(".modal-backdrop").remove();
             $("#myModal").hide();
           

             $('#successModal').modal('toggle'); 
             setTimeout(function(){ 
                 $('#successModal').modal('hide') 
                 location.reload(); // Recargar la página completa después de 5 segundos
             }, 2000); // abrir
       
  
             
             
            //  Solucionar muestra de modal de successModal
               
                   
        }
        else if(dataResult.statusCode==201){
          $("#submit_Age").html("Algo no salio bien.. <i class='fas fa-exclamation-triangle'></i>");
          $('#ErrorData').modal('toggle'); 
        
      setTimeout(function(){ 
          $("#submit_Age").html("Guardar <i class='fas fa-save'></i>");
      }, 3000); // abrir
         
       
         }		
               
    
                
  

        }
      }); 
      return false;
    }   
  });


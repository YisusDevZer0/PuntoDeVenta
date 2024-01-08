$('document').ready(function($){
    $.validator.addMethod("Sololetras", function(value, element) {
        return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
      }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");
      $.validator.addMethod("Telefonico", function(value, element) {
        return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
      }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar numeros!");
      $.validator.addMethod("Correos", function(value, element) {
        return this.optional(element) || /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(value);
      }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa un correo valido!");
     
      $.validator.addMethod("Problema", function(value, element) {
        return this.optional(element) || /^[\u00F1A-Za-z _]*[\u00F1A-Za-z][\u00F1A-Za-z _]*$/.test(value);
      }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

     
  
  
    $("#Soporte").validate({
		rules: {
		
			nombres: {
                required: true,
                minlength: 2,
                maxlength: 40,
                Sololetras: "",
                
            },
            telefono: {
                required: true,
                minlength:10,
                maxlength:10,
                Telefonico: "",
                
            },
            correo: {
                required: true,
                minlength:5,
                maxlength:30,
                Correos: "",
                
            },
            empresa: {
                required: true,
               
                
            },
            descripcion: {
                required: true,
                minlength:25,
                maxlength:150,
                Problema: "",
                
			},
			
			
		},
		messages: {
            
			nombres:{
              required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Ingresa tu nombre ",
              maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> No puede tener mas de 40 caracteres",
              minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Un nombre no puede tener solo 1 caracter"
            
             },
             telefono:{
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Ingresa el numero de telefono ",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> El numero de telefono no puede tener mas de 10 caracteres",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Debes ingresar los 10 caracteres del numero de telefono"
               
               },
               correo:{
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Ingresa un correo ",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> No puedes sobrepasar mas de 30 caracteres",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Debes ingresar tu correo completo"
               
               },
               empresa:{
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Elige una empresa ",
           
              
               },
               descripcion:{
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i>Ingresa la descripcion del problema ",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i>No puede tener mas de 150 caracteres",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Debes ingresar al menos una breve descripcion"
              
               },
          
		},
        submitHandler: submitForm	
	});	   
    // hide messages 
   
 
    function submitForm() {		
		var Sistema = $('#Sistema').val();
		var nombres = $('#nombres').val();
		var telefono = $('#telefono').val();
        var correo = $('#correo').val();			
        var empresa = $('#empresa').val();			
        var descripcion = $('#descripcion').val();			
		$.ajax({				
			type : 'POST',
			url  : 'Scripts/Soporte.php',
			data: {
                Sistema:Sistema,
                nombres:nombres,
                telefono:telefono,
                correo: correo,
                empresa: empresa,		
                descripcion:descripcion		

            },
            cache: false,
            beforeSend: function(){	
				$("#success").fadeOut();
				
				$("#CS").html("Enviando<span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
				
                    
			},
            success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    
                     $("#CS").html("Enviado <i class='fas fa-check'></i>")	
                     $("#success").show();
                        $("#Soporte")[0].reset();	
                        $("#CS").html("Un Momento <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                           $("#CS").html("Enviar <i class='fas fa-paper-plane'></i>")	

                }
                else if(dataResult.statusCode==201){
                    alert("Error occured !");
                    $("#CS").html("Enviar <i class='fas fa-paper-plane'></i>")	
                 }		
                       
						
                        
					
				
			}
		});
		return false;
	}   
});
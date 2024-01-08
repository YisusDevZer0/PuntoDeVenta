$('document').ready(function() { 
// Cuando el checkbox cambie de estado.
 $('#show_password').on('change',function(event){
    // Si el checkbox esta "checkeado"
    if($('#show_password').is(':checked')){
       // Convertimos el input de contraseña a texto.
       $('#password').get(0).type='text';
    // En caso contrario..
    } else {
       // Lo convertimos a contraseña.
       $('#password').get(0).type='password';
    }
 });
});
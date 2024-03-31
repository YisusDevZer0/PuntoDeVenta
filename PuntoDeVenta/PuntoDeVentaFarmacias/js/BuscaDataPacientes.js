

  $(function() {
    $("#nombres").autocomplete({
        source: "Controladores/BuscandoPaciente.php",
        minLength: 2,
        appendTo: "#AgendaEnSucursalA",
        select: function(event, ui) {
            event.preventDefault();
          
           
            $('#nombres').val(ui.item.nombres);
            $('#tel').val(ui.item.tel);
           
            
         }
        
    });
          
          });

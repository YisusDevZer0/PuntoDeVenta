$(document).ready(function() {
    // Manejar el envío del formulario
    $("#RegistrarEncargoForm").submit(function(e) {
        e.preventDefault();
        
        // Mostrar loading
        $("#loading-overlay").show();
        $("#loading-text").text("Registrando encargo...");
        
        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar datos via AJAX
        $.ajax({
            url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/RegistrarEncargo.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                $("#loading-overlay").hide();
                
                if (response.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        // Cerrar modal y recargar página
                        $('#editModal').modal('hide');
                        location.reload();
                    });
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                $("#loading-overlay").hide();
                
                // Mostrar mensaje de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Intente nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
                
                console.error("Error AJAX:", error);
            }
        });
    });
    
    // Validar campos numéricos
    $("#cantidad, #precioventa, #costo, #abono_parcial").on("input", function() {
        var value = $(this).val();
        if (value < 0) {
            $(this).val(0);
        }
    });
    
    // Calcular total automáticamente
    $("#cantidad, #precioventa").on("input", function() {
        var cantidad = parseFloat($("#cantidad").val()) || 0;
        var precio = parseFloat($("#precioventa").val()) || 0;
        var total = cantidad * precio;
        
        // Actualizar el campo de costo si está vacío
        if ($("#costo").val() === "" || $("#costo").val() === "0") {
            $("#costo").val(total.toFixed(2));
        }
    });
    
    // Validar que el abono no sea mayor al costo
    $("#abono_parcial").on("input", function() {
        var abono = parseFloat($(this).val()) || 0;
        var costo = parseFloat($("#costo").val()) || 0;
        
        if (abono > costo) {
            $(this).val(costo.toFixed(2));
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'El abono no puede ser mayor al costo total.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}); 
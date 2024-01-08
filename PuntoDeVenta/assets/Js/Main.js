$(document).ready(function () {
    $('form').submit(function (event) {
        event.preventDefault();

        var userName = $('#userName').val();
        var password = $('#pass').val();

        $.ajax({
            type: 'POST',
            url: 'https://doctorpez.mx/PuntoDeVenta/Consultas/Login.php',
            data: { userName: userName, pass: password },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Redirigir o realizar acciones según el tipo de usuario
                    if (response.message.includes('administrador')) {
                        // Redirigir a la página de administrador
                        window.location.href = 'admin_home.html';
                    } else if (response.message.includes('vendedor')) {
                        // Redirigir a la página de vendedor
                        window.location.href = 'vendedor_home.html';
                    } else {
                        // Manejar otro tipo de usuario o caso no reconocido
                        alert('Rol no reconocido');
                    }
                } else {
                    // Mostrar mensaje de error
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error al procesar la solicitud.');
            }
        });
    });
});

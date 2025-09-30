$(document).ready(function () {
    // Función para logging de errores
    function logError(context, error, additionalData = {}) {
        const timestamp = new Date().toISOString();
        const errorInfo = {
            timestamp: timestamp,
            context: context,
            error: error,
            url: window.location.href,
            userAgent: navigator.userAgent,
            ...additionalData
        };
        
        console.error('Error en FinalizaInventarioSucursales:', errorInfo);
        
        // Opcional: enviar a un servicio de logging
        // fetch('/api/log-error', {
        //     method: 'POST',
        //     headers: {'Content-Type': 'application/json'},
        //     body: JSON.stringify(errorInfo)
        // });
    }

    // Función para mostrar errores detallados
    function mostrarErrorDetallado(titulo, mensaje, detalles = null) {
        let htmlContent = `<div style="text-align: left;">
            <p><strong>Error:</strong> ${mensaje}</p>`;
        
        if (detalles) {
            htmlContent += `<p><strong>Detalles técnicos:</strong></p>
                <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px;">${detalles}</pre>`;
        }
        
        htmlContent += `<p><strong>Hora:</strong> ${new Date().toLocaleString()}</p>
            <p><strong>Página:</strong> ${window.location.href}</p>
        </div>`;

        Swal.fire({
            icon: 'error',
            title: titulo,
            html: htmlContent,
            width: '600px',
            confirmButtonText: 'Entendido',
            showCancelButton: true,
            cancelButtonText: 'Reportar problema'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // Aquí podrías abrir un formulario para reportar el problema
                console.log('Usuario quiere reportar problema');
            }
        });
    }

    // Agregar los métodos de validación personalizados
    function validarFormulario() {
        var clienteInput = $("#clienteInput");
        var errores = [];

        // Validación del cliente
        if (clienteInput.val() === "" || clienteInput.val().trim() === "") {
            errores.push("El nombre del cliente es obligatorio y no puede estar vacío");
        } else if (clienteInput.val().trim().length < 2) {
            errores.push("El nombre del cliente debe tener al menos 2 caracteres");
        }

        // Validación de otros campos si existen
        var camposRequeridos = $('[required]');
        camposRequeridos.each(function() {
            var campo = $(this);
            var valor = campo.val();
            var nombre = campo.attr('name') || campo.attr('id') || 'campo';
            
            if (!valor || valor.trim() === '') {
                errores.push(`El campo "${nombre}" es obligatorio`);
            }
        });

        if (errores.length > 0) {
            logError('validacion_formulario', errores.join('; '), {
                clienteInput: clienteInput.val(),
                camposRequeridos: camposRequeridos.length
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Errores de validación',
                html: `<div style="text-align: left;">
                    <p><strong>Se encontraron los siguientes errores:</strong></p>
                    <ul>${errores.map(error => `<li>${error}</li>`).join('')}</ul>
                </div>`,
                confirmButtonText: 'Corregir'
            });
            return false;
        }
        return true;
    }

    // Validar el formulario
    $("#VentasAlmomento").validate({
        rules: {
            clienteInput: {
                required: true,
                minlength: 2
            },
        },
        messages: {
            clienteInput: {
                required: 'El nombre del cliente es obligatorio',
                minlength: 'El nombre del cliente debe tener al menos 2 caracteres'
            },
        },
        errorPlacement: function(error, element) {
            // Mostrar errores de validación de manera más clara
            error.insertAfter(element);
        },
        submitHandler: function () {
            if (validarFormulario()) {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Guardando los datos del inventario',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "Controladores/RegistraInventariosSucursales.php",
                    data: $('#VentasAlmomento').serialize(),
                    cache: false,
                    timeout: 30000, // 30 segundos de timeout
                    success: function (data) {
                        try {
                            var response = JSON.parse(data);

                            if (response.status === 'success') {
                                logError('operacion_exitosa', 'Inventario registrado correctamente', {
                                    response: response
                                });
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Inventario registrado exitosamente!',
                                    text: 'Los datos se han guardado correctamente en el sistema',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    didOpen: () => {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                    },
                                });
                            } else {
                                logError('error_servidor', response.message || 'Error desconocido del servidor', {
                                    response: response,
                                    data: data
                                });
                                
                                mostrarErrorDetallado(
                                    'Error al guardar el inventario',
                                    response.message || 'El servidor devolvió un error inesperado',
                                    `Código de respuesta: ${response.code || 'N/A'}\nDatos recibidos: ${JSON.stringify(response, null, 2)}`
                                );
                            }
                        } catch (parseError) {
                            logError('error_parseo_json', parseError.message, {
                                data: data,
                                parseError: parseError
                            });
                            
                            mostrarErrorDetallado(
                                'Error de formato de respuesta',
                                'El servidor devolvió una respuesta en formato incorrecto',
                                `Error de parsing: ${parseError.message}\nRespuesta del servidor: ${data.substring(0, 500)}...`
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        let mensajeError = 'Error desconocido en la comunicación con el servidor';
                        let detalles = `Status: ${status}\nError: ${error}\nCódigo HTTP: ${xhr.status}`;

                        if (xhr.status === 0) {
                            mensajeError = 'No se pudo conectar con el servidor. Verifica tu conexión a internet.';
                        } else if (xhr.status === 404) {
                            mensajeError = 'El archivo del servidor no fue encontrado. Contacta al administrador.';
                        } else if (xhr.status === 500) {
                            mensajeError = 'Error interno del servidor. El administrador ha sido notificado.';
                        } else if (status === 'timeout') {
                            mensajeError = 'La operación tardó demasiado tiempo. Intenta nuevamente.';
                        } else if (status === 'parsererror') {
                            mensajeError = 'Error al procesar la respuesta del servidor.';
                        }

                        logError('error_ajax', error, {
                            xhr: xhr,
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });

                        mostrarErrorDetallado(
                            'Error de comunicación',
                            mensajeError,
                            detalles
                        );
                    }
                });
            }
        },
    });
});

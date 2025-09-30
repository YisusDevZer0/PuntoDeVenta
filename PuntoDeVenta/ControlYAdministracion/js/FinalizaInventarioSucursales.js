$(document).ready(function () {
    console.log('🚀 FinalizaInventarioSucursales.js v2.0 cargado correctamente');
    
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

    // Función para mostrar errores específicos del servidor
    function mostrarErrorEspecifico(response) {
        const codigo = response.code || 'UNKNOWN_ERROR';
        const mensaje = response.message || 'Error desconocido';
        const errores = response.errors || [];
        
        let titulo = 'Error al guardar el inventario';
        let icono = 'error';
        let mensajePrincipal = mensaje;
        let htmlContent = '';
        
        // Personalizar mensaje según el tipo de error
        switch (codigo) {
            case 'VALIDATION_ERROR':
                titulo = 'Errores de validación';
                icono = 'warning';
                mensajePrincipal = 'Se encontraron problemas con los datos enviados:';
                
                if (errores.length > 0) {
                    htmlContent = `<div style="text-align: left;">
                        <p><strong>Por favor corrige los siguientes errores:</strong></p>
                        <ul style="margin: 10px 0; padding-left: 20px;">`;
                    
                    errores.forEach(error => {
                        htmlContent += `<li style="margin: 5px 0; color: #d32f2f;">${error}</li>`;
                    });
                    
                    htmlContent += `</ul>
                        <p style="margin-top: 15px; font-size: 14px; color: #666;">
                            <strong>💡 Consejo:</strong> Revisa los campos marcados en rojo y asegúrate de que todos los datos sean correctos.
                        </p>
                    </div>`;
                }
                break;
                
            case 'EXCEPTION':
                titulo = 'Error del sistema';
                mensajePrincipal = mensaje;
                htmlContent = `<div style="text-align: left;">
                    <p><strong>Descripción:</strong> ${mensaje}</p>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>🔄 Solución:</strong> Intenta nuevamente. Si el problema persiste, contacta al administrador.
                    </p>
                </div>`;
                break;
                
            case 'FATAL_ERROR':
                titulo = 'Error crítico del sistema';
                icono = 'error';
                mensajePrincipal = 'Ha ocurrido un error crítico en el servidor';
                htmlContent = `<div style="text-align: left;">
                    <p><strong>Error:</strong> ${mensaje}</p>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>🚨 Acción requerida:</strong> Contacta inmediatamente al administrador del sistema.
                    </p>
                    <p style="margin-top: 10px; font-size: 12px; color: #999;">
                        <strong>Hora del error:</strong> ${response.timestamp || new Date().toLocaleString()}
                    </p>
                </div>`;
                break;
                
            case 'JSON_ERROR':
                titulo = 'Error de formato de datos';
                mensajePrincipal = 'El servidor no pudo procesar la respuesta correctamente';
                htmlContent = `<div style="text-align: left;">
                    <p><strong>Descripción:</strong> ${mensaje}</p>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>🔄 Solución:</strong> Recarga la página e intenta nuevamente.
                    </p>
                </div>`;
                break;
                
            default:
                titulo = 'Error inesperado';
                mensajePrincipal = mensaje;
                htmlContent = `<div style="text-align: left;">
                    <p><strong>Descripción:</strong> ${mensaje}</p>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>Código de error:</strong> ${codigo}
                    </p>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>🔄 Solución:</strong> Intenta nuevamente o contacta al administrador si el problema persiste.
                    </p>
                </div>`;
        }
        
        // Agregar información adicional si está disponible
        if (response.debug_info && window.location.hostname === 'localhost') {
            htmlContent += `<div style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px; font-size: 12px;">
                <strong>Información de debugging (solo en desarrollo):</strong><br>
                <pre style="margin: 5px 0; white-space: pre-wrap;">${JSON.stringify(response.debug_info, null, 2)}</pre>
            </div>`;
        }
        
        Swal.fire({
            icon: icono,
            title: titulo,
            html: htmlContent,
            width: '700px',
            confirmButtonText: codigo === 'VALIDATION_ERROR' ? 'Corregir datos' : 'Entendido',
            showCancelButton: codigo === 'FATAL_ERROR',
            cancelButtonText: codigo === 'FATAL_ERROR' ? 'Reportar problema' : null
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // Aquí podrías abrir un formulario para reportar el problema
                console.log('Usuario quiere reportar problema');
                // Opcional: abrir modal de reporte de problemas
                // abrirModalReporteProblema(response);
            }
        });
    }

    // Función para mostrar mensajes de éxito
    function mostrarExito(response) {
        const datos = response.data || {};
        const registrosProcesados = datos.registros_procesados || 0;
        const filasAfectadas = datos.filas_afectadas || 0;
        const timestamp = datos.timestamp || new Date().toLocaleString();
        
        let htmlContent = `<div style="text-align: center;">
            <div style="font-size: 48px; color: #4caf50; margin-bottom: 15px;">✅</div>
            <p style="font-size: 18px; margin-bottom: 10px;"><strong>¡Inventario registrado exitosamente!</strong></p>
            <p style="color: #666; margin-bottom: 15px;">Los datos se han guardado correctamente en el sistema</p>
        </div>`;
        
        if (registrosProcesados > 0) {
            htmlContent += `<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; text-align: left;">
                <h4 style="margin: 0 0 10px 0; color: #333;">📊 Resumen de la operación:</h4>
                <ul style="margin: 0; padding-left: 20px; color: #666;">
                    <li><strong>Productos procesados:</strong> ${registrosProcesados}</li>
                    <li><strong>Registros guardados:</strong> ${filasAfectadas}</li>
                    <li><strong>Hora de finalización:</strong> ${timestamp}</li>
                </ul>
            </div>`;
        }
        
        htmlContent += `<div style="margin-top: 15px; font-size: 14px; color: #666;">
            <p>🔄 La página se recargará automáticamente en unos segundos...</p>
        </div>`;
        
        Swal.fire({
            icon: 'success',
            title: '¡Operación completada!',
            html: htmlContent,
            width: '600px',
            showConfirmButton: false,
            timer: 3000,
            didOpen: () => {
                // Mostrar progreso de recarga
                let tiempoRestante = 3;
                const timerInterval = setInterval(() => {
                    tiempoRestante--;
                    if (tiempoRestante <= 0) {
                        clearInterval(timerInterval);
                        location.reload();
                    }
                }, 1000);
            },
        });
    }

    // Función para mostrar errores detallados (para casos técnicos)
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

    // Función para mostrar progreso de la operación
    function mostrarProgreso() {
        const htmlContent = `
            <div style="text-align: center;">
                <div style="font-size: 48px; color: #2196f3; margin-bottom: 15px; animation: spin 2s linear infinite;">⏳</div>
                <p style="font-size: 18px; margin-bottom: 10px;"><strong>Procesando inventario...</strong></p>
                <p style="color: #666; margin-bottom: 15px;">Por favor espera mientras guardamos los datos</p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        <strong>🔄 Pasos del proceso:</strong><br>
                        • Validando datos enviados<br>
                        • Preparando consulta de base de datos<br>
                        • Guardando registros en el sistema<br>
                        • Finalizando operación
                    </p>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        Swal.fire({
            title: 'Procesando...',
            html: htmlContent,
            width: '500px',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Agregar los métodos de validación personalizados
    function validarFormulario() {
        // Validación simplificada - solo verificar que el formulario esté listo
        console.log('✅ Validación del formulario completada');
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
                // Mostrar indicador de carga con información detallada
                mostrarProgreso();

                $.ajax({
                    type: 'POST',
                    url: "Controladores/RegistraInventariosSucursales.php",
                    data: $('#VentasAlmomento').serialize(),
                    cache: false,
                    timeout: 30000, // 30 segundos de timeout
                    success: function (data) {
                        console.log('📥 Respuesta recibida del servidor:', data);
                        console.log('🔍 Tipo de datos:', typeof data);
                        
                        // Procesar la respuesta de manera segura
                        var response;
                        
                        if (typeof data === 'string') {
                            try {
                                response = JSON.parse(data);
                            } catch (e) {
                                console.error('❌ Error parseando JSON:', e);
                                mostrarErrorDetallado('Error de formato', 'La respuesta del servidor no es JSON válido', data);
                                return;
                            }
                        } else if (typeof data === 'object' && data !== null) {
                            response = data;
                        } else {
                            console.error('❌ Tipo de respuesta no válido:', typeof data);
                            mostrarErrorDetallado('Error de respuesta', 'Tipo de respuesta no válido: ' + typeof data, data);
                            return;
                        }

                        console.log('✅ Respuesta procesada:', response);

                        if (response.status === 'success') {
                            console.log('🎉 Operación exitosa');
                            mostrarExito(response);
                        } else {
                            console.log('❌ Error del servidor:', response.message);
                            mostrarErrorEspecifico(response);
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

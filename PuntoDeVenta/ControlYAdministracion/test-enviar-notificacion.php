<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Envío de Notificaciones Push</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-container {
            max-height: 400px;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Test Envío de Notificaciones Push</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Enviar Notificación</h5>
                <form id="notificationForm">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" required 
                               value="Nueva Notificación">
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="mensaje" rows="3" required
                                  placeholder="Escribe el mensaje de la notificación..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label">URL (opcional)</label>
                        <input type="url" class="form-control" id="url" 
                               placeholder="https://doctorpez.mx/...">
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Notificación</label>
                        <select class="form-select" id="tipo">
                            <option value="sistema">Sistema</option>
                            <option value="venta">Venta</option>
                            <option value="inventario">Inventario</option>
                            <option value="alerta">Alerta</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Notificación</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Resultado</h5>
                <div id="result" class="result-container">
                    <pre>Esperando envío de notificación...</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('notificationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<pre>Enviando notificación...</pre>';
            
            try {
                const response = await fetch('api/enviar_notificacion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: document.getElementById('titulo').value,
                        mensaje: document.getElementById('mensaje').value,
                        url: document.getElementById('url').value || undefined,
                        tipo: document.getElementById('tipo').value
                    })
                });

                const data = await response.json();
                resultDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                
                if (data.success) {
                    resultDiv.querySelector('pre').style.color = 'green';
                } else {
                    resultDiv.querySelector('pre').style.color = 'red';
                }
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red">Error: ${error.message}</pre>`;
            }
        });
    </script>
</body>
</html> 
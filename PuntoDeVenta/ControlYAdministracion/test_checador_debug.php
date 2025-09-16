<?php
// Test de debug del checador
session_start();

// Simular sesión de usuario para pruebas
$_SESSION['ControlMaestro'] = 1;

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
    <title>Test Debug Checador</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js'></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .test-button { padding: 10px 20px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .test-button:hover { background: #0056b3; }
        .result { margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <h1>Test Debug del Checador</h1>
    <p>Usuario ID: 1</p>
    
    <div class='test-section'>
        <h3>Test de Conexión</h3>
        <button class='test-button' onclick='testConnection()'>Probar Conexión</button>
        <div id='connectionResult' class='result'></div>
    </div>
    
    <div class='test-section'>
        <h3>Test de Registro de Entrada</h3>
        <button class='test-button' onclick='testRegistrarEntrada()'>Probar Entrada</button>
        <div id='entradaResult' class='result'></div>
    </div>
    
    <div class='test-section'>
        <h3>Test de Registro de Salida</h3>
        <button class='test-button' onclick='testRegistrarSalida()'>Probar Salida</button>
        <div id='salidaResult' class='result'></div>
    </div>
    
    <div class='test-section'>
        <h3>Test de Verificación de Ubicación</h3>
        <button class='test-button' onclick='testVerificarUbicacion()'>Probar Ubicación</button>
        <div id='ubicacionResult' class='result'></div>
    </div>
    
    <script>
        async function testConnection() {
            const resultDiv = document.getElementById('connectionResult');
            resultDiv.innerHTML = 'Probando conexión...';
            
            try {
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=obtener_ubicaciones'
                });
                
                const text = await response.text();
                console.log('Respuesta raw:', text);
                
                try {
                    const data = JSON.parse(text);
                    resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    resultDiv.className = 'result success';
                } catch (e) {
                    resultDiv.innerHTML = 'Error parseando JSON: ' + e.message + '<br><br>Respuesta: <pre>' + text + '</pre>';
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = 'Error de conexión: ' + error.message;
                resultDiv.className = 'result error';
            }
        }
        
        async function testRegistrarEntrada() {
            const resultDiv = document.getElementById('entradaResult');
            resultDiv.innerHTML = 'Probando registro de entrada...';
            
            try {
                const formData = new FormData();
                formData.append('action', 'registrar_asistencia');
                formData.append('tipo', 'entrada');
                formData.append('latitud', '21.02341360');
                formData.append('longitud', '-89.57087560');
                formData.append('timestamp', new Date().toISOString());
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                console.log('Respuesta raw:', text);
                
                try {
                    const data = JSON.parse(text);
                    resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    resultDiv.className = 'result success';
                } catch (e) {
                    resultDiv.innerHTML = 'Error parseando JSON: ' + e.message + '<br><br>Respuesta: <pre>' + text + '</pre>';
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = 'Error de conexión: ' + error.message;
                resultDiv.className = 'result error';
            }
        }
        
        async function testRegistrarSalida() {
            const resultDiv = document.getElementById('salidaResult');
            resultDiv.innerHTML = 'Probando registro de salida...';
            
            try {
                const formData = new FormData();
                formData.append('action', 'registrar_asistencia');
                formData.append('tipo', 'salida');
                formData.append('latitud', '21.02341360');
                formData.append('longitud', '-89.57087560');
                formData.append('timestamp', new Date().toISOString());
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                console.log('Respuesta raw:', text);
                
                try {
                    const data = JSON.parse(text);
                    resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    resultDiv.className = 'result success';
                } catch (e) {
                    resultDiv.innerHTML = 'Error parseando JSON: ' + e.message + '<br><br>Respuesta: <pre>' + text + '</pre>';
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = 'Error de conexión: ' + error.message;
                resultDiv.className = 'result error';
            }
        }
        
        async function testVerificarUbicacion() {
            const resultDiv = document.getElementById('ubicacionResult');
            resultDiv.innerHTML = 'Probando verificación de ubicación...';
            
            try {
                const formData = new FormData();
                formData.append('action', 'verificar_ubicacion');
                formData.append('latitud', '21.02341360');
                formData.append('longitud', '-89.57087560');
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                console.log('Respuesta raw:', text);
                
                try {
                    const data = JSON.parse(text);
                    resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    resultDiv.className = 'result success';
                } catch (e) {
                    resultDiv.innerHTML = 'Error parseando JSON: ' + e.message + '<br><br>Respuesta: <pre>' + text + '</pre>';
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = 'Error de conexión: ' + error.message;
                resultDiv.className = 'result error';
            }
        }
    </script>
</body>
</html>";
?>

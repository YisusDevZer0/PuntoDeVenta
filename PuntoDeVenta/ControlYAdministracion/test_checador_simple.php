<?php
// Test simple del checador
session_start();

// Simular sesión de usuario para pruebas
$_SESSION['ControlMaestro'] = 1;

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
    <title>Test Checador Simple</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js'></script>
</head>
<body>
    <h1>Test del Checador</h1>
    <p>Usuario ID: 1</p>
    
    <button onclick='testRegistrarEntrada()'>Test Entrada</button>
    <button onclick='testRegistrarSalida()'>Test Salida</button>
    <button onclick='testVerificarUbicacion()'>Test Verificar Ubicación</button>
    
    <div id='resultado'></div>
    
    <script>
        async function testRegistrarEntrada() {
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
                
                const result = await response.json();
                document.getElementById('resultado').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                
                if (result.success) {
                    Swal.fire('Éxito', result.message, 'success');
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }
        
        async function testRegistrarSalida() {
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
                
                const result = await response.json();
                document.getElementById('resultado').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                
                if (result.success) {
                    Swal.fire('Éxito', result.message, 'success');
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }
        
        async function testVerificarUbicacion() {
            try {
                const formData = new FormData();
                formData.append('action', 'verificar_ubicacion');
                formData.append('latitud', '21.02341360');
                formData.append('longitud', '-89.57087560');
                
                const response = await fetch('Controladores/ChecadorController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                document.getElementById('resultado').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                
                if (result.success) {
                    Swal.fire('Éxito', 'En área: ' + result.en_area, 'success');
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }
    </script>
</body>
</html>";
?>

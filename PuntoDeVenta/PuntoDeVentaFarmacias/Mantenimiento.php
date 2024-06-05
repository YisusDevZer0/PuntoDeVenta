<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página en Mantenimiento</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f2f2f2;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .container img {
            max-width: 100%;
            height: auto;
        }
        h1 {
            color: #0073e6;
            font-size: 2em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2em;
            line-height: 1.6;
        }
        .fish {
            animation: swim 10s linear infinite;
        }
        @keyframes swim {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://doctorpez.mx/PuntoDeVenta/FotosMedidores/pez.gif" alt="Peces nadando" class="fish">
        <h1>¡Estamos realizando mantenimiento!</h1>
        <p>Nos encontramos mejorando nuestro sistema para ofrecerte una mejor experiencia. Volveremos en breve.</p>
        <p>Gracias por tu paciencia.</p>
    </div>
    <footer>
        &copy; 2024 Doctor Pez Todos los derechos reservados.
    </footer>
</body>
</html>

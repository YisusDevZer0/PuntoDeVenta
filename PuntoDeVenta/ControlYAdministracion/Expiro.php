<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Vencida</title>
    <style>
        body {
            background-image: url('https://wallpaperaccess.com/full/1941924.jpg');
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 20px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="message">
        <h1>¡La sesión ha expirado!</h1>
        <p>Para continuar, por favor, inicia sesión nuevamente.</p>
        <a href="inicio_sesion.html" class="btn">Iniciar Sesión</a>
    </div>
</body>
</html>

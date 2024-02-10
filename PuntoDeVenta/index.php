<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNTO DE VENTA</title>
    <!-- Agrega los estilos de Material Design -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 400px;
            padding: 20px;
        }
        .card-content {
            padding: 20px;
        }
        .card-action {
            background-color: #C80096;
            text-align: center;
        }
        .btn {
            background-color: #C80096;
        }
        .btn:hover {
            background-color: #960056;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-content">
            <span class="card-title">Inicio de sesión</span>
            <div class="input-field">
                <input id="user_email" type="email" class="validate">
                <label for="user_email">Correo electrónico</label>
            </div>
            <div class="input-field">
                <input id="password" type="password" class="validate">
                <label for="password">Contraseña</label>
            </div>
        </div>
        <div class="card-action">
            <a href="#" class="btn waves-effect waves-light">Ingresar</a>
        </div>
    </div>
    
    <!-- Agrega los scripts de Materialize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>

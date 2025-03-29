<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('dbconect.php');
// Verificar si el archivo autoload.php existe
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: El archivo 'vendor/autoload.php' no existe. Asegúrate de ejecutar 'composer install'.");
}


use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST["import"])) {
    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (isset($_FILES["file"]) && in_array($_FILES["file"]["type"], $allowedFileType)) {
        $uploadDir = 'subidas/';
        if (!is_dir($uploadDir)) {
            die("El directorio de carga no existe.");
        }

        $targetPath = $uploadDir . $_FILES['file']['name'];
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            die("Error al mover el archivo subido. Verifica los permisos y la ruta.");
        }

        // Leer el archivo Excel
        $spreadsheet = IOFactory::load($targetPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Definir el usuario que realiza la actualización
        $ActualizadoPor = isset($_POST["ActualizadoPor"]) ? mysqli_real_escape_string($con, $_POST["ActualizadoPor"]) : 'Desconocido';

        $ignoredRows = []; // Para registrar las filas ignoradas
        $processedRows = 0; // Contador de filas procesadas

        foreach ($rows as $index => $row) {
            // Saltar la fila de encabezados (puedes ajustar esta lógica si los encabezados tienen un patrón específico)
            if ($index === 0 || empty($row[0])) {
                $ignoredRows[] = $index + 1; // Registrar la fila ignorada (sumamos 1 porque los índices comienzan en 0)
                continue;
            }

            // Validar y limpiar los datos
            $Folio_Prod_Stock = isset($row[0]) ? mysqli_real_escape_string($con, $row[0]) : '';
            $ID_Prod_POS = isset($row[1]) ? mysqli_real_escape_string($con, $row[1]) : '';
            $Cod_Barra = isset($row[2]) ? mysqli_real_escape_string($con, $row[2]) : '';
            $Nombre_Prod = isset($row[3]) ? mysqli_real_escape_string($con, $row[3]) : '';
            $Fk_sucursal = isset($row[4]) ? mysqli_real_escape_string($con, $row[4]) : '';
            $Nombre_Sucursal = isset($row[5]) ? mysqli_real_escape_string($con, $row[5]) : '';
            $Max_Existencia = isset($row[6]) ? mysqli_real_escape_string($con, $row[6]) : '';
            $Min_Existencia = isset($row[7]) ? mysqli_real_escape_string($con, $row[7]) : '';

            // Validar que los campos obligatorios no estén vacíos
            if (empty($Folio_Prod_Stock) || empty($Max_Existencia) || empty($Min_Existencia)) {
                $ignoredRows[] = $index + 1; // Registrar la fila ignorada
                continue;
            }

            // Insertar los datos en la tabla
            $query = "INSERT INTO ActualizacionMaxMin (
                Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Nombre_Sucursal, 
                Max_Existencia, Min_Existencia, AgregadoPor, FechaAgregado, ActualizadoPor, FechaActualizado
            ) VALUES (
                '$Folio_Prod_Stock', '$ID_Prod_POS', '$Cod_Barra', '$Nombre_Prod', '$Fk_sucursal', '$Nombre_Sucursal', 
                '$Max_Existencia', '$Min_Existencia', '$ActualizadoPor', NOW(), '$ActualizadoPor', NOW()
            )";
            $resultados = mysqli_query($con, $query);

            if (!$resultados) {
                die("Error en la consulta SQL: " . mysqli_error($con));
            }

            $processedRows++; // Incrementar el contador de filas procesadas
        }

        // Mostrar un mensaje al usuario
        $message = "Se procesaron $processedRows filas correctamente.";
        if (!empty($ignoredRows)) {
            $message .= " Las siguientes filas fueron ignoradas debido a campos vacíos o encabezados: " . implode(', ', $ignoredRows) . ".";
        }

        echo "<script>alert('$message');</script>";
        header("Location: https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ResultadoActualizacionMaxMin");
        exit();
    } else {
        die("El archivo enviado es inválido. Por favor vuelva a intentarlo.");
    }
}

include_once "Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Actualización de máximos y mínimos</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <style>
        .error {
            color: red;
            margin-left: 5px;
        }
        #frmExcelImport {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        button.btn-submit {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        select {
            padding: 8px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="text-center">
            <div class="container-fluid pt-4 px-4">
                <div class="col-12">
                <h6 class="mb-4" style="color:#0172b6;">Actualización masiva de máximos y mínimos</h6> <br>
                    <form id="frmExcelImport" method="post" enctype="multipart/form-data">
                        <label for="file">Seleccionar archivo Excel:</label>
                        <input type="file" name="file" id="file" required>
                        <input type="hidden" name="ActualizadoPor" value="<?php echo $row['Nombre_Apellidos']; ?>">
                        <button class="btn-submit" type="submit" name="import">Importar Excel</button>
                        <div class="error"><?php if (isset($message)) echo $message; ?></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
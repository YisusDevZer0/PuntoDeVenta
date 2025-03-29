<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('dbconect.php');
require 'vendor/autoload.php';

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

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Saltar la fila de encabezados

            $Folio_Prod_Stock = mysqli_real_escape_string($con, $row[0]);
            $ID_Prod_POS = mysqli_real_escape_string($con, $row[1]);
            $Cod_Barra = mysqli_real_escape_string($con, $row[2]);
            $Nombre_Prod = mysqli_real_escape_string($con, $row[3]);
            $Fk_sucursal = mysqli_real_escape_string($con, $row[4]);
            $Nombre_Sucursal = mysqli_real_escape_string($con, $row[5]);
            $Max_Existencia = mysqli_real_escape_string($con, $row[6]);
            $Min_Existencia = mysqli_real_escape_string($con, $row[7]);

            if (!empty($Folio_Prod_Stock) && !empty($Max_Existencia) && !empty($Min_Existencia)) {
                $query = "INSERT INTO ActualizacionMaxMin (
                    Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Nombre_Sucursal, 
                    Max_Existencia, Min_Existencia, AgregadoPor, FechaActualizado
                ) VALUES (
                    '$Folio_Prod_Stock', '$ID_Prod_POS', '$Cod_Barra', '$Nombre_Prod', '$Fk_sucursal', '$Nombre_Sucursal', 
                    '$Max_Existencia', '$Min_Existencia', '$ActualizadoPor', NOW()
                )";
                $resultados = mysqli_query($con, $query);

                if (!$resultados) {
                    die("Error en la consulta SQL: " . mysqli_error($con));
                }
            }
        }

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
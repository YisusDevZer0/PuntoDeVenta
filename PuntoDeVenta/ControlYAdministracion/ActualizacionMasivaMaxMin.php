<?php
include('dbconect.php');
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"])) {
    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    
    if (isset($_FILES["file"]) && in_array($_FILES["file"]["type"], $allowedFileType)) {
        $uploadDir = 'subidas/';
        
        if (!is_dir($uploadDir)) {
            die("El directorio de carga no existe.");
        }

        $targetPath = $uploadDir . $_FILES['file']['name'];
        
        // Verificar errores de archivo
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            die("Error al subir el archivo. Código de error: " . $_FILES['file']['error']);
        }
        
        // Intentar mover el archivo
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            die("Error al mover el archivo subido. Verifica los permisos y la ruta.");
        }

        $ActualizadoPor = mysqli_real_escape_string($con, $_POST["ActualizadoPor"]); // Hidden input for user
        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        for ($i = 0; $i < $sheetCount; $i++) {
            $Reader->ChangeSheet($i);
            
            foreach ($Reader as $Row) {
                $Folio_Prod_Stock = isset($Row[0]) ? mysqli_real_escape_string($con, $Row[0]) : "";
                $ID_Prod_POS = isset($Row[1]) ? mysqli_real_escape_string($con, $Row[1]) : "";
                $Cod_Barra = isset($Row[2]) ? mysqli_real_escape_string($con, $Row[2]) : "";
                $Nombre_Prod = isset($Row[3]) ? mysqli_real_escape_string($con, $Row[3]) : "";
                $Fk_sucursal = isset($Row[4]) ? mysqli_real_escape_string($con, $Row[4]) : "";
                $Nombre_Sucursal = isset($Row[5]) ? mysqli_real_escape_string($con, $Row[5]) : "";
                $Max_Existencia = isset($Row[6]) ? mysqli_real_escape_string($con, $Row[6]) : "";
                $Min_Existencia = isset($Row[7]) ? mysqli_real_escape_string($con, $Row[7]) : "";
                
                if (!empty($Folio_Prod_Stock) && !empty($Max_Existencia) && !empty($Min_Existencia)) {
                    // Insertar los datos en la tabla ActualizacionMaxMin
                    $query = "INSERT INTO ActualizacionMaxMin (
                        Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Nombre_Sucursal, 
                        Max_Existencia, Min_Existencia, AgregadoPor
                    ) VALUES (
                        '$Folio_Prod_Stock', '$ID_Prod_POS', '$Cod_Barra', '$Nombre_Prod', '$Fk_sucursal', '$Nombre_Sucursal', 
                        '$Max_Existencia', '$Min_Existencia', '$ActualizadoPor'
                    )";
                    $resultados = mysqli_query($con, $query);
                
                    if ($resultados) {
                        $type = "success";
                        $message = "Excel importado correctamente y datos insertados.";
                    } else {
                        $type = "error";
                        $message = "Hubo un problema al insertar los registros.";
                    }
                }
            }
        }
        header("Location: https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ResultadoActualizacionMaxMin");
        exit();
    } else {
        $type = "error";
        $message = "El archivo enviado es inválido. Por favor vuelva a intentarlo.";
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
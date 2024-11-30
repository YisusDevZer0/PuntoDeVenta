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
        $ActualizadoEl = date("Y-m-d H:i:s"); // Fecha y hora actual

        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        for ($i = 0; $i < $sheetCount; $i++) {
            $Reader->ChangeSheet($i);
            
            foreach ($Reader as $Row) {
                $ID_Prod_POS = isset($Row[0]) ? mysqli_real_escape_string($con, $Row[0]) : "";
                $Cod_Barra = isset($Row[1]) ? mysqli_real_escape_string($con, $Row[1]) : "";
                $Clave_adicional = isset($Row[2]) ? mysqli_real_escape_string($con, $Row[2]) : "";
                $Clave_Levic = isset($Row[3]) ? mysqli_real_escape_string($con, $Row[3]) : "";
                $Nombre_Prod = isset($Row[4]) ? mysqli_real_escape_string($con, $Row[4]) : "";
                $Precio_Venta = isset($Row[5]) ? mysqli_real_escape_string($con, $Row[5]) : "";
                $Precio_C = isset($Row[6]) ? mysqli_real_escape_string($con, $Row[6]) : "";
                $Tipo_Servicio = isset($Row[7]) ? mysqli_real_escape_string($con, $Row[7]) : "";
                $Componente_Activo = isset($Row[8]) ? mysqli_real_escape_string($con, $Row[8]) : "";
                $Tipo = isset($Row[9]) ? mysqli_real_escape_string($con, $Row[9]) : "";
                $FkCategoria = isset($Row[10]) ? mysqli_real_escape_string($con, $Row[10]) : "";
                $FkMarca = isset($Row[11]) ? mysqli_real_escape_string($con, $Row[11]) : "";
                $FkPresentacion = isset($Row[12]) ? mysqli_real_escape_string($con, $Row[12]) : "";
                $Proveedor1 = isset($Row[13]) ? mysqli_real_escape_string($con, $Row[13]) : "";
                $Proveedor2 = isset($Row[14]) ? mysqli_real_escape_string($con, $Row[14]) : "";
                $RecetaMedica = isset($Row[15]) ? mysqli_real_escape_string($con, $Row[15]) : "";
                $Licencia = isset($Row[16]) ? mysqli_real_escape_string($con, $Row[16]) : "";
                $Ivaal16 = isset($Row[17]) ? mysqli_real_escape_string($con, $Row[17]) : "";
                $Contable = isset($Row[18]) ? mysqli_real_escape_string($con, $Row[18]) : "";
                
                if (!empty($Cod_Barra) || !empty($Nombre_Prod) || !empty($Precio_Venta)) {
                    $query = "INSERT INTO ActualizacionMasivaProductosGlobales (
                        ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, Nombre_Prod, Precio_Venta, 
                        Precio_C, Tipo_Servicio, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, 
                        Proveedor1, Proveedor2, RecetaMedica, AgregadoPor, AgregadoEl, Licencia, Ivaal16, 
                        ActualizadoPor, ActualizadoEl, Contable
                    ) VALUES (
                        '$ID_Prod_POS', '$Cod_Barra', '$Clave_adicional', '$Clave_Levic', '$Nombre_Prod', '$Precio_Venta', 
                        '$Precio_C', '$Tipo_Servicio', '$Componente_Activo', '$Tipo', '$FkCategoria', '$FkMarca', '$FkPresentacion', 
                        '$Proveedor1', '$Proveedor2', '$RecetaMedica', '$ActualizadoPor', '$ActualizadoEl', '$Licencia', '$Ivaal16', 
                        '$ActualizadoPor', '$ActualizadoEl', '$Contable'
                    )";
                    $resultados = mysqli_query($con, $query);
                
                    if ($resultados) {
                        $type = "success";
                        $message = "Excel importado correctamente";
                    } else {
                        $type = "error";
                        $message = "Hubo un problema al importar registros";
                    }
                }
            }
        }
        header("Location: https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ResultadoActualizacionMasiva");
        exit();
    } else {
        $type = "error";
        $message = "El archivo enviado es inválido. Por favor vuelva a intentarlo";
    }
}

include_once "Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Actualización de datos masivamente</title>
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
                <h6 class="mb-4" style="color:#0172b6;">Actualizacion masiva de datos de  <?php echo isset($row['Licencia']) ? $row['Licencia'] : ''; ?></h6> <br>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#resultadosinventarios').DataTable({
                                "order": [[0, "desc"]],
                                "language": {
                                    "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                                }
                            });
                        });
                    </script>
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

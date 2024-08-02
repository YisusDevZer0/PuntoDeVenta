<?php
include('dbconect.php');
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"])) {
    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    
    if (in_array($_FILES["file"]["type"], $allowedFileType)) {
        $targetPath = 'subidas/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        $Sucursal = mysqli_real_escape_string($con, $_POST["Sucursalnueva"]);
        $Tipo_ajuste = mysqli_real_escape_string($con, $_POST["TipAjuste"]);
        $Agrego = mysqli_real_escape_string($con, $_POST["Agrega"]);
        $ActualizadoPor = mysqli_real_escape_string($con, $_POST["ActualizadoPor"]); // Hidden input for user
        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        for ($i = 0; $i < $sheetCount; $i++) {
            $Reader->ChangeSheet($i);
            
            foreach ($Reader as $Row) {
                $Cod_Barra = isset($Row[0]) ? mysqli_real_escape_string($con, $Row[0]) : "";
                $Clave_adicional = isset($Row[1]) ? mysqli_real_escape_string($con, $Row[1]) : "";
                $Nombre_Prod = isset($Row[2]) ? mysqli_real_escape_string($con, $Row[2]) : "";
                $Precio_Venta = isset($Row[3]) ? mysqli_real_escape_string($con, $Row[3]) : "";
                $Precio_C = isset($Row[4]) ? mysqli_real_escape_string($con, $Row[4]) : "";
                $Componente_Activo = isset($Row[5]) ? mysqli_real_escape_string($con, $Row[5]) : "";
                $Tipo = isset($Row[6]) ? mysqli_real_escape_string($con, $Row[6]) : "";
                $FkCategoria = isset($Row[7]) ? mysqli_real_escape_string($con, $Row[7]) : "";
                $FkMarca = isset($Row[8]) ? mysqli_real_escape_string($con, $Row[8]) : "";
                $FkPresentacion = isset($Row[9]) ? mysqli_real_escape_string($con, $Row[9]) : "";
                $Proveedor1 = isset($Row[10]) ? mysqli_real_escape_string($con, $Row[10]) : "";
                $Proveedor2 = isset($Row[11]) ? mysqli_real_escape_string($con, $Row[11]) : "";
                $RecetaMedica = isset($Row[12]) ? mysqli_real_escape_string($con, $Row[12]) : "";
                $Licencia = isset($Row[13]) ? mysqli_real_escape_string($con, $Row[13]) : "";
                $Ivaal16 = isset($Row[14]) ? mysqli_real_escape_string($con, $Row[14]) : "";
                
                if (!empty($Cod_Barra) || !empty($Nombre_Prod) || !empty($Precio_Venta)) {
                    $query = "INSERT INTO ActualizacionesMasivasProductosPOS (Cod_Barra, Clave_adicional, Nombre_Prod, Precio_Venta, Precio_C, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1, Proveedor2, RecetaMedica, Licencia, Ivaal16, ActualizadoPor) VALUES ('$Cod_Barra', '$Clave_adicional', '$Nombre_Prod', '$Precio_Venta', '$Precio_C', '$Componente_Activo', '$Tipo', '$FkCategoria', '$FkMarca', '$FkPresentacion', '$Proveedor1', '$Proveedor2', '$RecetaMedica', '$Licencia', '$Ivaal16', '$ActualizadoPor')";
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
                    <h6 class="mb-4" style="color:#0172b6;">Actualización masiva de datos</h6>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#resultadosinventarios').DataTable({
                                "order": [[0, "desc"]],
                                "paging": true,
                                "lengthMenu": [10, 25, 50, 75, 100],
                                "stateSave": true,
                                "language": {
                                    "url": "Componentes/Spanish.json"
                                }
                            });
                        });
                    </script>
                    <div class="card-body">
                        <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                            <div>
                                <label for="file">Cargar archivo</label>
                                <input type="file" name="file" id="file" accept=".xls, .xlsx">
                            </div>
                            <input type="hidden" name="ActualizadoPor" value="<?php echo $row['Nombre_Apellidos']; ?>">
                            <input type="text" hidden name="Sucursalnueva" value="<?php echo $row['Sucursal']; ?>">
                            <input type="text" hidden name="TipAjuste" value="TipoAjusteValue"> <!-- Adjust this as needed -->
                            <input type="text" hidden name="Agrega" value="<?php echo $row['Nombre_Apellidos']; ?>">
                            <br>
                            <button type="submit" id="submit" name="import" class="btn-submit">Importar Registros</button>
                        </form>
                    </div>
                    <div id="response" class="<?php if (!empty($type)) { echo $type . " display-block"; } ?>"><?php if (!empty($message)) { echo $message; } ?></div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/AltaProductosNuevos.js"></script>
    <?php 
    include "Modales/NuevoProductos.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php"; ?>
</body>
</html>

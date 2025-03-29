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

require $autoloadPath;

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start(); // Asegurarse de que la sesión esté iniciada

if (isset($_POST["preview"])) {
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

        // Generar la tabla para el modal
        $tableHtml = "<table id='dataTable' class='table table-bordered'>";
        $tableHtml .= "<thead class='table-primary'><tr>
                <th>Folio_Prod_Stock</th>
                <th>ID_Prod_POS</th>
                <th>Cod_Barra</th>
                <th>Nombre_Prod</th>
                <th>Fk_sucursal</th>
                <th>Nombre_Sucursal</th>
                <th>Max_Existencia</th>
                <th>Min_Existencia</th>
              </tr></thead>";
        $tableHtml .= "<tbody>";
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Saltar encabezados
            $tableHtml .= "<tr>";
            foreach ($row as $key => $cell) {
                $value = isset($cell) ? htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') : '';
                $tableHtml .= "<td><input type='text' name='data[$index][$key]' value='$value' class='form-control'></td>";
            }
            $tableHtml .= "</tr>";
        }
        $tableHtml .= "</tbody></table>";

        // Guardar la tabla en una variable de sesión para mostrarla en el modal
        $_SESSION['tableHtml'] = $tableHtml;
        $_SESSION['filePath'] = $targetPath;

        header("Location: ActualizacionMasivaMaxMin.php?showModal=1");
        exit();
    } else {
        die("El archivo enviado es inválido. Por favor vuelva a intentarlo.");
    }
}

if (isset($_POST["import"])) {
    $data = $_POST['data']; // Datos editados por el usuario
    $ActualizadoPor = isset($_POST["ActualizadoPor"]) ? mysqli_real_escape_string($con, $_POST["ActualizadoPor"]) : 'Desconocido';

    $ignoredRows = []; // Para registrar las filas ignoradas
    $processedRows = 0; // Contador de filas procesadas

    foreach ($data as $index => $row) {
        $Folio_Prod_Stock = isset($row[0]) && trim($row[0]) !== '' ? mysqli_real_escape_string($con, $row[0]) : null;
        $Max_Existencia = isset($row[6]) && trim($row[6]) !== '' ? mysqli_real_escape_string($con, $row[6]) : null;
        $Min_Existencia = isset($row[7]) && trim($row[7]) !== '' ? mysqli_real_escape_string($con, $row[7]) : null;

        if (is_null($Folio_Prod_Stock) || is_null($Max_Existencia) || is_null($Min_Existencia)) {
            $ignoredRows[] = $index + 1;
            continue;
        }

        $query = "INSERT INTO ActualizacionMaxMin (
            Folio_Prod_Stock, Max_Existencia, Min_Existencia, AgregadoPor, FechaAgregado, ActualizadoPor, FechaActualizado
        ) VALUES (
            '$Folio_Prod_Stock', '$Max_Existencia', '$Min_Existencia', '$ActualizadoPor', NOW(), '$ActualizadoPor', NOW()
        )";
        $resultados = mysqli_query($con, $query);

        if (!$resultados) {
            die("Error en la consulta SQL: " . mysqli_error($con));
        }

        $processedRows++;
    }

    $message = "Se procesaron $processedRows filas correctamente.";
    if (!empty($ignoredRows)) {
        $message .= " Las siguientes filas fueron ignoradas debido a campos vacíos o encabezados: " . implode(', ', $ignoredRows) . ".";
    }

    echo "<script>alert('$message');</script>";
    header("Location: ActualizacionMasivaMaxMin.php");
    exit();
}

include_once "Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Actualización de máximos y mínimos</title>
    <?php include "header.php"; ?>
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
                        <button class="btn-submit" type="submit" name="preview">Vista previa</button>
                        <div class="error"><?php if (isset($message)) echo $message; ?></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para la vista previa -->
    <?php if (isset($_GET['showModal']) && $_GET['showModal'] == 1): ?>
    <div class="modal fade show" id="previewModal" tabindex="-1" role="dialog" style="display: block;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista previa del archivo Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.href='ActualizacionMasivaMaxMin.php';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="table-responsive">
                            <?php
                            echo $_SESSION['tableHtml'];
                            ?>
                        </div>
                        <button type="submit" name="import" class="btn btn-success mt-3">Confirmar y Procesar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include "footer.php"; ?>
</body>
</html>
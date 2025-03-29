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
                <th>Acciones</th>
              </tr></thead>";
        $tableHtml .= "<tbody>";
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Saltar encabezados
            $tableHtml .= "<tr>";
            foreach ($row as $key => $cell) {
                $value = isset($cell) ? htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') : '';
                $tableHtml .= "<td><input type='text' name='data[$index][$key]' value='$value' class='form-control'></td>";
            }
            $tableHtml .= "<td><button type='button' class='btn btn-danger btn-sm delete-row'>Eliminar</button></td>";
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
        // Validar y limpiar los datos
        $Folio_Prod_Stock = isset($row[0]) && trim($row[0]) !== '' ? mysqli_real_escape_string($con, $row[0]) : null;
        $ID_Prod_POS = isset($row[1]) && trim($row[1]) !== '' ? mysqli_real_escape_string($con, $row[1]) : null;
        $Cod_Barra = isset($row[2]) && trim($row[2]) !== '' ? mysqli_real_escape_string($con, $row[2]) : null;
        $Nombre_Prod = isset($row[3]) && trim($row[3]) !== '' ? mysqli_real_escape_string($con, $row[3]) : null;
        $Fk_sucursal = isset($row[4]) && trim($row[4]) !== '' ? mysqli_real_escape_string($con, $row[4]) : null;
        $Nombre_Sucursal = isset($row[5]) && trim($row[5]) !== '' ? mysqli_real_escape_string($con, $row[5]) : null;
        $Max_Existencia = isset($row[6]) && trim($row[6]) !== '' ? mysqli_real_escape_string($con, $row[6]) : null;
        $Min_Existencia = isset($row[7]) && trim($row[7]) !== '' ? mysqli_real_escape_string($con, $row[7]) : null;

        // Validar que los campos obligatorios no estén vacíos
        if (is_null($Folio_Prod_Stock) || is_null($Max_Existencia) || is_null($Min_Existencia)) {
            $ignoredRows[] = $index + 1; // Registrar la fila ignorada
            continue;
        }

        // Insertar los datos en la tabla
        $query = "INSERT INTO ActualizacionMaxMin (
            Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Nombre_Sucursal, Max_Existencia, Min_Existencia, AgregadoPor, FechaAgregado, ActualizadoPor, FechaActualizado
        ) VALUES (
            '$Folio_Prod_Stock', '$ID_Prod_POS', '$Cod_Barra', '$Nombre_Prod', '$Fk_sucursal', '$Nombre_Sucursal', '$Max_Existencia', '$Min_Existencia', '$ActualizadoPor', NOW(), '$ActualizadoPor', NOW()
        )";
        $resultados = mysqli_query($con, $query);

        if (!$resultados) {
            die("Error en la consulta SQL: " . mysqli_error($con));
        }

        $processedRows++; // Incrementar el contador de filas procesadas
    }

    // Mostrar un mensaje al usuario
    if ($processedRows > 0) {
        $message = "Se procesaron $processedRows filas correctamente.";
    } else {
        $message = "No se procesaron filas debido a campos vacíos o errores.";
    }

    if (!empty($ignoredRows)) {
        $message .= " Las siguientes filas fueron ignoradas debido a campos vacíos: " . implode(', ', $ignoredRows) . ".";
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

    <!-- Incluir Bootstrap CSS y JS si no están en header.php -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Función para eliminar una fila
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('#previewModal').addEventListener('click', function (event) {
                if (event.target.classList.contains('delete-row')) {
                    const row = event.target.closest('tr');
                    row.remove();
                }
            });
        });
    </script>
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
                        <button class="btn-submit btn btn-primary" type="submit" name="preview">Vista previa</button>
                        <div class="error"><?php if (isset($message)) echo $message; ?></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para la vista previa -->
    <?php if (isset($_GET['showModal']) && $_GET['showModal'] == 1): ?>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Vista previa del archivo Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="table-responsive">
                            <?php
                            if (isset($_SESSION['tableHtml'])) {
                                echo $_SESSION['tableHtml'];
                            } else {
                                echo "<p>No hay datos para mostrar.</p>";
                            }
                            ?>
                        </div>
                        <button type="submit" name="import" class="btn btn-success mt-3">Confirmar y Procesar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Mostrar el modal automáticamente
        document.addEventListener('DOMContentLoaded', function () {
            var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        });
    </script>
    <?php endif; ?>

    <?php include "Footer.php"; ?>
</body>
</html>
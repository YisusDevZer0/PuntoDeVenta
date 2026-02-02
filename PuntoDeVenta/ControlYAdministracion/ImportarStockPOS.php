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

session_start();

// Función para obtener el ID de categoría por nombre
function obtenerIdCategoria($con, $nombreCategoria) {
    if (empty($nombreCategoria)) return null;
    $nombreCategoria = mysqli_real_escape_string($con, trim($nombreCategoria));
    $query = "SELECT Cat_ID FROM Categorias_POS WHERE Nom_Cat = '$nombreCategoria' LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['Cat_ID'];
    }
    return null;
}

// Función para obtener el ID de servicio por nombre
function obtenerIdServicio($con, $nombreServicio) {
    if (empty($nombreServicio)) return null;
    $nombreServicio = mysqli_real_escape_string($con, trim($nombreServicio));
    $query = "SELECT Servicio_ID FROM Servicios_POS WHERE Nom_Serv = '$nombreServicio' LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['Servicio_ID'];
    }
    return null;
}

if (isset($_POST["preview"])) {
    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (isset($_FILES["file"]) && in_array($_FILES["file"]["type"], $allowedFileType)) {
        $uploadDir = 'subidas/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
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
        $tableHtml = "<table id='dataTable' class='table table-bordered table-striped'>";
        $tableHtml .= "<thead class='table-primary'><tr>
                <th>Tipo_Servicio</th>
                <th>Nombre_Servicio</th>
                <th>Tipo</th>
                <th>FkCategoria</th>
                <th>Nombre_Categoria</th>
                <th>FkMarca</th>
                <th>FkPresentacion</th>
                <th>Proveedor1</th>
                <th>Proveedor2</th>
                <th>Contable</th>
                <th>Anaquel</th>
                <th>Repisa</th>
                <th>Fk_sucursal</th>
                <th>Max_Existencia</th>
                <th>Min_Existencia</th>
                <th>Acciones</th>
              </tr></thead>";
        $tableHtml .= "<tbody>";
        
        $headerRow = isset($rows[0]) ? $rows[0] : [];
        $headerMap = [];
        foreach ($headerRow as $idx => $header) {
            $headerMap[trim(strtolower($header))] = $idx;
        }
        
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Saltar encabezados
            
            // Mapear columnas según nombres de encabezados
            $tipoServicio = isset($headerMap['tipo_servicio']) && isset($row[$headerMap['tipo_servicio']]) ? $row[$headerMap['tipo_servicio']] : (isset($row[0]) ? $row[0] : '');
            $nombreServicio = isset($headerMap['nombre_servicio']) && isset($row[$headerMap['nombre_servicio']]) ? $row[$headerMap['nombre_servicio']] : (isset($row[1]) ? $row[1] : '');
            $tipo = isset($headerMap['tipo']) && isset($row[$headerMap['tipo']]) ? $row[$headerMap['tipo']] : (isset($row[2]) ? $row[2] : '');
            $fkCategoria = isset($headerMap['fkcategoria']) && isset($row[$headerMap['fkcategoria']]) ? $row[$headerMap['fkcategoria']] : (isset($row[3]) ? $row[3] : '');
            $nombreCategoria = isset($headerMap['nombre_categoria']) && isset($row[$headerMap['nombre_categoria']]) ? $row[$headerMap['nombre_categoria']] : (isset($row[4]) ? $row[4] : '');
            $fkMarca = isset($headerMap['fkmarca']) && isset($row[$headerMap['fkmarca']]) ? $row[$headerMap['fkmarca']] : (isset($row[5]) ? $row[5] : '');
            $fkPresentacion = isset($headerMap['fkpresentacion']) && isset($row[$headerMap['fkpresentacion']]) ? $row[$headerMap['fkpresentacion']] : (isset($row[6]) ? $row[6] : '');
            $proveedor1 = isset($headerMap['proveedor1']) && isset($row[$headerMap['proveedor1']]) ? $row[$headerMap['proveedor1']] : (isset($row[7]) ? $row[7] : '');
            $proveedor2 = isset($headerMap['proveedor2']) && isset($row[$headerMap['proveedor2']]) ? $row[$headerMap['proveedor2']] : (isset($row[8]) ? $row[8] : '');
            $contable = isset($headerMap['contable']) && isset($row[$headerMap['contable']]) ? $row[$headerMap['contable']] : (isset($row[9]) ? $row[9] : '');
            $anaquel = isset($headerMap['anaquel']) && isset($row[$headerMap['anaquel']]) ? $row[$headerMap['anaquel']] : (isset($row[10]) ? $row[10] : '');
            $repisa = isset($headerMap['repisa']) && isset($row[$headerMap['repisa']]) ? $row[$headerMap['repisa']] : (isset($row[11]) ? $row[11] : '');
            
            // Si no hay Fk_sucursal en el Excel, usar el valor del formulario
            $fkSucursal = '';
            if (isset($headerMap['fk_sucursal']) && isset($row[$headerMap['fk_sucursal']])) {
                $fkSucursal = $row[$headerMap['fk_sucursal']];
            } else if (isset($_POST['Fk_sucursal']) && !empty($_POST['Fk_sucursal'])) {
                $fkSucursal = $_POST['Fk_sucursal'];
            }
            
            // Buscar Max y Min en diferentes posibles posiciones
            $maxExistencia = '';
            if (isset($headerMap['max_existencia']) || isset($headerMap['max'])) {
                $colIdx = isset($headerMap['max_existencia']) ? $headerMap['max_existencia'] : $headerMap['max'];
                $maxExistencia = isset($row[$colIdx]) ? $row[$colIdx] : '';
            } else if (count($row) > 12 && isset($row[12])) {
                $maxExistencia = $row[12];
            }
            
            $minExistencia = '';
            if (isset($headerMap['min_existencia']) || isset($headerMap['min'])) {
                $colIdx = isset($headerMap['min_existencia']) ? $headerMap['min_existencia'] : $headerMap['min'];
                $minExistencia = isset($row[$colIdx]) ? $row[$colIdx] : '';
            } else if (count($row) > 13 && isset($row[13])) {
                $minExistencia = $row[13];
            }
            
            $tableHtml .= "<tr>";
            $tableHtml .= "<td><input type='text' name='data[$index][tipo_servicio]' value='" . htmlspecialchars($tipoServicio, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][nombre_servicio]' value='" . htmlspecialchars($nombreServicio, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][tipo]' value='" . htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][fk_categoria]' value='" . htmlspecialchars($fkCategoria, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][nombre_categoria]' value='" . htmlspecialchars($nombreCategoria, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][fk_marca]' value='" . htmlspecialchars($fkMarca, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][fk_presentacion]' value='" . htmlspecialchars($fkPresentacion, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][proveedor1]' value='" . htmlspecialchars($proveedor1, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][proveedor2]' value='" . htmlspecialchars($proveedor2, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][contable]' value='" . htmlspecialchars($contable, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][anaquel]' value='" . htmlspecialchars($anaquel, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='text' name='data[$index][repisa]' value='" . htmlspecialchars($repisa, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='number' name='data[$index][fk_sucursal]' value='" . htmlspecialchars($fkSucursal, ENT_QUOTES, 'UTF-8') . "' class='form-control' required></td>";
            $tableHtml .= "<td><input type='number' name='data[$index][max_existencia]' value='" . htmlspecialchars($maxExistencia, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><input type='number' name='data[$index][min_existencia]' value='" . htmlspecialchars($minExistencia, ENT_QUOTES, 'UTF-8') . "' class='form-control'></td>";
            $tableHtml .= "<td><button type='button' class='btn btn-danger btn-sm delete-row'>Eliminar</button></td>";
            $tableHtml .= "</tr>";
        }
        $tableHtml .= "</tbody></table>";

        // Guardar la tabla en una variable de sesión
        $_SESSION['tableHtml'] = $tableHtml;
        $_SESSION['filePath'] = $targetPath;

        header("Location: ImportarStockPOS.php?showModal=1");
        exit();
    } else {
        die("El archivo enviado es inválido. Por favor vuelva a intentarlo.");
    }
}

if (isset($_POST["import"])) {
    $data = $_POST['data'];
    $ActualizadoPor = isset($_POST["ActualizadoPor"]) ? mysqli_real_escape_string($con, $_POST["ActualizadoPor"]) : 'Desconocido';

    $ignoredRows = [];
    $processedRows = 0;
    $errors = [];

    foreach ($data as $index => $row) {
        // Validar y limpiar los datos
        $tipoServicio = isset($row['tipo_servicio']) ? trim($row['tipo_servicio']) : '';
        $nombreServicio = isset($row['nombre_servicio']) ? trim($row['nombre_servicio']) : '';
        $tipo = isset($row['tipo']) ? mysqli_real_escape_string($con, trim($row['tipo'])) : '';
        $fkCategoria = isset($row['fk_categoria']) ? trim($row['fk_categoria']) : '';
        $nombreCategoria = isset($row['nombre_categoria']) ? trim($row['nombre_categoria']) : '';
        $fkMarca = isset($row['fk_marca']) ? mysqli_real_escape_string($con, trim($row['fk_marca'])) : '';
        $fkPresentacion = isset($row['fk_presentacion']) ? mysqli_real_escape_string($con, trim($row['fk_presentacion'])) : '';
        $proveedor1 = isset($row['proveedor1']) ? mysqli_real_escape_string($con, trim($row['proveedor1'])) : '';
        $proveedor2 = isset($row['proveedor2']) ? mysqli_real_escape_string($con, trim($row['proveedor2'])) : '';
        $contable = isset($row['contable']) ? mysqli_real_escape_string($con, trim($row['contable'])) : '';
        $anaquel = isset($row['anaquel']) ? mysqli_real_escape_string($con, trim($row['anaquel'])) : '';
        $repisa = isset($row['repisa']) ? mysqli_real_escape_string($con, trim($row['repisa'])) : '';
        $fkSucursal = isset($row['fk_sucursal']) && trim($row['fk_sucursal']) !== '' ? intval($row['fk_sucursal']) : null;
        $maxExistencia = isset($row['max_existencia']) && trim($row['max_existencia']) !== '' ? intval($row['max_existencia']) : null;
        $minExistencia = isset($row['min_existencia']) && trim($row['min_existencia']) !== '' ? intval($row['min_existencia']) : null;

        // Validar campos obligatorios
        if (empty($nombreServicio) || is_null($fkSucursal)) {
            $ignoredRows[] = $index + 1;
            continue;
        }

        // Obtener ID de servicio si se proporciona nombre
        $servicioId = null;
        if (!empty($tipoServicio)) {
            $servicioId = is_numeric($tipoServicio) ? intval($tipoServicio) : obtenerIdServicio($con, $tipoServicio);
        } else if (!empty($nombreServicio)) {
            $servicioId = obtenerIdServicio($con, $nombreServicio);
        }

        // Obtener ID de categoría si se proporciona nombre
        $categoriaId = null;
        if (!empty($fkCategoria)) {
            $categoriaId = is_numeric($fkCategoria) ? intval($fkCategoria) : obtenerIdCategoria($con, $fkCategoria);
        } else if (!empty($nombreCategoria)) {
            $categoriaId = obtenerIdCategoria($con, $nombreCategoria);
        }

        // Buscar productos en Stock_POS que coincidan con los criterios
        $whereConditions = [];
        $whereParams = [];
        $whereTypes = '';

        // Siempre requerir Fk_sucursal
        $whereConditions[] = "Fk_sucursal = ?";
        $whereParams[] = $fkSucursal;
        $whereTypes .= 'i';

        // Agregar criterios de búsqueda si están disponibles
        if ($servicioId !== null) {
            $whereConditions[] = "Tipo_Servicio = ?";
            $whereParams[] = $servicioId;
            $whereTypes .= 'i';
        }

        if ($categoriaId !== null) {
            $whereConditions[] = "FkCategoria = ?";
            $whereParams[] = $categoriaId;
            $whereTypes .= 's';
        }

        if (!empty($fkMarca)) {
            $whereConditions[] = "FkMarca = ?";
            $whereParams[] = $fkMarca;
            $whereTypes .= 's';
        }

        if (!empty($fkPresentacion)) {
            $whereConditions[] = "FkPresentacion = ?";
            $whereParams[] = $fkPresentacion;
            $whereTypes .= 's';
        }

        // Si no hay criterios específicos pero hay nombre de servicio, buscar por nombre de producto
        if (empty($whereConditions) && !empty($nombreServicio)) {
            // Buscar productos que contengan el nombre del servicio en Nombre_Prod
            $whereConditions[] = "Nombre_Prod LIKE ?";
            $whereParams[] = '%' . mysqli_real_escape_string($con, $nombreServicio) . '%';
            $whereTypes .= 's';
        }

        // Si tenemos al menos Fk_sucursal, proceder con la búsqueda
        if (count($whereConditions) > 0) {
            $whereClause = implode(' AND ', $whereConditions);
            $query = "SELECT Folio_Prod_Stock, ID_Prod_POS, Nombre_Prod FROM Stock_POS WHERE $whereClause";

            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, $whereTypes, ...$whereParams);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $updated = false;
                $productosEncontrados = [];
                while ($producto = mysqli_fetch_assoc($result)) {
                    $productosEncontrados[] = $producto;
                    
                    // Construir la consulta UPDATE
                    $updateFields = [];
                    $updateParams = [];
                    $updateTypes = '';

                    if ($maxExistencia !== null) {
                        $updateFields[] = "Max_Existencia = ?";
                        $updateParams[] = $maxExistencia;
                        $updateTypes .= 'i';
                    }

                    if ($minExistencia !== null) {
                        $updateFields[] = "Min_Existencia = ?";
                        $updateParams[] = $minExistencia;
                        $updateTypes .= 'i';
                    }

                    if (!empty($anaquel)) {
                        $updateFields[] = "Anaquel = ?";
                        $updateParams[] = $anaquel;
                        $updateTypes .= 's';
                    }

                    if (!empty($repisa)) {
                        $updateFields[] = "Repisa = ?";
                        $updateParams[] = $repisa;
                        $updateTypes .= 's';
                    }

                    if (!empty($contable)) {
                        $updateFields[] = "Contable = ?";
                        $updateParams[] = $contable;
                        $updateTypes .= 's';
                    }

                    if (!empty($proveedor1)) {
                        $updateFields[] = "Proveedor1 = ?";
                        $updateParams[] = $proveedor1;
                        $updateTypes .= 's';
                    }

                    if (!empty($proveedor2)) {
                        $updateFields[] = "Proveedor2 = ?";
                        $updateParams[] = $proveedor2;
                        $updateTypes .= 's';
                    }

                    if (!empty($updateFields)) {
                        $updateFields[] = "ActualizadoPor = ?";
                        $updateParams[] = $ActualizadoPor;
                        $updateTypes .= 's';

                        $updateQuery = "UPDATE Stock_POS SET " . implode(', ', $updateFields) . 
                                      " WHERE Folio_Prod_Stock = ? AND Fk_sucursal = ?";
                        $updateParams[] = $producto['Folio_Prod_Stock'];
                        $updateParams[] = $fkSucursal;
                        $updateTypes .= 'ii';

                        $updateStmt = mysqli_prepare($con, $updateQuery);
                        if ($updateStmt) {
                            mysqli_stmt_bind_param($updateStmt, $updateTypes, ...$updateParams);
                            if (mysqli_stmt_execute($updateStmt)) {
                                $updated = true;
                            } else {
                                $errors[] = "Error actualizando producto ID {$producto['ID_Prod_POS']}: " . mysqli_error($con);
                            }
                            mysqli_stmt_close($updateStmt);
                        }
                    }
                }
                mysqli_stmt_close($stmt);

                if ($updated) {
                    $processedRows++;
                } else if (empty($productosEncontrados)) {
                    $ignoredRows[] = $index + 1 . " (no se encontraron productos coincidentes para: " . htmlspecialchars($nombreServicio) . ")";
                } else {
                    $ignoredRows[] = $index + 1 . " (productos encontrados pero sin valores Max/Min para actualizar)";
                }
            } else {
                $ignoredRows[] = $index + 1 . " (error en la consulta de búsqueda)";
            }
        } else {
            $ignoredRows[] = $index + 1 . " (faltan criterios de búsqueda - se requiere al menos Fk_sucursal)";
        }
    }

    // Preparar el mensaje
    $message = "Se procesaron $processedRows filas correctamente.";
    $icon = "success";

    if ($processedRows == 0) {
        $message = "No se procesaron filas. Verifique los datos.";
        $icon = "warning";
    }

    if (!empty($ignoredRows)) {
        $message .= " Filas ignoradas: " . implode(', ', array_slice($ignoredRows, 0, 10));
        if (count($ignoredRows) > 10) {
            $message .= " y " . (count($ignoredRows) - 10) . " más.";
        }
    }

    if (!empty($errors)) {
        $message .= " Errores: " . implode('; ', array_slice($errors, 0, 5));
    }

    // Mostrar notificación con SweetAlert2
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Resultado del procesamiento',
                text: '$message',
                icon: '$icon',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = 'ImportarStockPOS.php';
            });
        });
    </script>";
    exit();
}

include_once "Controladores/ControladorUsuario.php";

// Obtener lista de sucursales
$sucursalesQuery = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
$sucursalesResult = mysqli_query($con, $sucursalesQuery);
$sucursales = [];
if ($sucursalesResult) {
    while ($suc = mysqli_fetch_assoc($sucursalesResult)) {
        $sucursales[] = $suc;
    }
}

$fcha = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Importar Stock POS</title>
    <?php include "header.php"; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const previewModal = document.querySelector('#previewModal');
            if (previewModal) {
                previewModal.addEventListener('click', function (event) {
                    if (event.target.classList.contains('delete-row')) {
                        const row = event.target.closest('tr');
                        row.remove();
                    }
                });
            }
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
                    <h6 class="mb-4" style="color:#0172b6;">Importar Stock POS - Actualización de Máximos y Mínimos por Sucursal</h6>
                    <div class="alert alert-info">
                        <strong>Columnas requeridas en el Excel:</strong><br>
                        <ul class="mb-0">
                            <li><strong>Tipo_Servicio</strong> o <strong>Nombre_Servicio</strong> (para identificar el producto)</li>
                            <li><strong>Fk_sucursal</strong> (ID de la sucursal) - O puede seleccionarla abajo si no está en el Excel</li>
                            <li><strong>Max_Existencia</strong> y/o <strong>Min_Existencia</strong> (valores a actualizar)</li>
                        </ul>
                        <strong>Columnas opcionales:</strong><br>
                        <ul class="mb-0">
                            <li>Tipo, FkCategoria, Nombre_Categoria, FkMarca, FkPresentacion</li>
                            <li>Proveedor1, Proveedor2, Contable, Anaquel, Repisa</li>
                        </ul>
                        <small class="text-muted">Nota: El sistema buscará productos en Stock_POS usando los criterios proporcionados y actualizará los valores Max/Min según la sucursal especificada.</small>
                    </div>
                    
                    <form id="frmExcelImport" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="Fk_sucursal" class="form-label">Sucursal (si no está en el Excel):</label>
                            <select name="Fk_sucursal" id="Fk_sucursal" class="form-control">
                                <option value="">Seleccione una sucursal (opcional si está en el Excel)</option>
                                <?php foreach ($sucursales as $suc): ?>
                                    <option value="<?php echo $suc['ID_Sucursal']; ?>"><?php echo htmlspecialchars($suc['Nombre_Sucursal']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Seleccionar archivo Excel:</label>
                            <input type="file" name="file" id="file" class="form-control" required accept=".xlsx,.xls">
                        </div>
                        
                        <input type="hidden" name="ActualizadoPor" value="<?php echo isset($row['Nombre_Apellidos']) ? htmlspecialchars($row['Nombre_Apellidos']) : 'Sistema'; ?>">
                        <button class="btn btn-primary" type="submit" name="preview">Vista previa</button>
                        <div class="error mt-2"><?php if (isset($message)) echo $message; ?></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para la vista previa -->
    <?php if (isset($_GET['showModal']) && $_GET['showModal'] == 1): ?>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Vista previa del archivo Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <?php
                            if (isset($_SESSION['tableHtml'])) {
                                echo $_SESSION['tableHtml'];
                            } else {
                                echo "<p>No hay datos para mostrar.</p>";
                            }
                            ?>
                        </div>
                        <input type="hidden" name="ActualizadoPor" value="<?php echo isset($row['Nombre_Apellidos']) ? htmlspecialchars($row['Nombre_Apellidos']) : 'Sistema'; ?>">
                        <button type="submit" name="import" class="btn btn-success mt-3">Confirmar y Procesar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        });
    </script>
    <?php endif; ?>

    <?php include "Footer.php"; ?>
</body>
</html>

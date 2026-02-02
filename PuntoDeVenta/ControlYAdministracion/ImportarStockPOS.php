<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aumentar límites para archivos grandes
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 3600); // 1 hora máximo

include('dbconect.php');
require_once('vendor/SpreadsheetReader.php');

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

// Función auxiliar para obtener valor de columna
function obtenerValorColumna($row, $headerMap, $key, $defaultIndex) {
    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
        return trim($row[$headerMap[$key]]);
    }
    return isset($row[$defaultIndex]) ? trim($row[$defaultIndex]) : '';
}

// Función para procesar una fila
function procesarFila($con, $data) {
    $tipoServicio = $data['tipo_servicio'];
    $nombreServicio = $data['nombre_servicio'];
    $tipo = mysqli_real_escape_string($con, $data['tipo']);
    $fkCategoria = $data['fk_categoria'];
    $nombreCategoria = $data['nombre_categoria'];
    $fkMarca = mysqli_real_escape_string($con, $data['fk_marca']);
    $fkPresentacion = mysqli_real_escape_string($con, $data['fk_presentacion']);
    $proveedor1 = mysqli_real_escape_string($con, $data['proveedor1']);
    $proveedor2 = mysqli_real_escape_string($con, $data['proveedor2']);
    $contable = mysqli_real_escape_string($con, $data['contable']);
    $anaquel = mysqli_real_escape_string($con, $data['anaquel']);
    $repisa = mysqli_real_escape_string($con, $data['repisa']);
    $fkSucursal = intval($data['fk_sucursal']);
    $maxExistencia = !empty($data['max_existencia']) ? intval($data['max_existencia']) : null;
    $minExistencia = !empty($data['min_existencia']) ? intval($data['min_existencia']) : null;
    $ActualizadoPor = mysqli_real_escape_string($con, $data['actualizado_por']);

    // Obtener ID de servicio
    $servicioId = null;
    if (!empty($tipoServicio)) {
        $servicioId = is_numeric($tipoServicio) ? intval($tipoServicio) : obtenerIdServicio($con, $tipoServicio);
    } else if (!empty($nombreServicio)) {
        $servicioId = obtenerIdServicio($con, $nombreServicio);
    }

    // Obtener ID de categoría
    $categoriaId = null;
    if (!empty($fkCategoria)) {
        $categoriaId = is_numeric($fkCategoria) ? intval($fkCategoria) : obtenerIdCategoria($con, $fkCategoria);
    } else if (!empty($nombreCategoria)) {
        $categoriaId = obtenerIdCategoria($con, $nombreCategoria);
    }

    // Buscar productos
    $whereConditions = ["Fk_sucursal = ?"];
    $whereParams = [$fkSucursal];
    $whereTypes = 'i';

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

    if (count($whereConditions) == 1 && !empty($nombreServicio)) {
        $whereConditions[] = "Nombre_Prod LIKE ?";
        $whereParams[] = '%' . mysqli_real_escape_string($con, $nombreServicio) . '%';
        $whereTypes .= 's';
    }

    $whereClause = implode(' AND ', $whereConditions);
    $query = "SELECT Folio_Prod_Stock, ID_Prod_POS FROM Stock_POS WHERE $whereClause LIMIT 100";

    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return ['success' => false, 'error' => 'Error en consulta: ' . mysqli_error($con)];
    }

    mysqli_stmt_bind_param($stmt, $whereTypes, ...$whereParams);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $updated = false;
    while ($producto = mysqli_fetch_assoc($result)) {
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
                }
                mysqli_stmt_close($updateStmt);
            }
        }
    }
    mysqli_stmt_close($stmt);

    return $updated ? ['success' => true] : ['success' => false, 'error' => 'No se encontraron productos coincidentes'];
}

// Endpoint para obtener preview limitado (solo primeras filas)
if (isset($_POST["preview"])) {
    header('Content-Type: application/json');
    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (isset($_FILES["file"]) && in_array($_FILES["file"]["type"], $allowedFileType)) {
        $uploadDir = 'subidas/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetPath = $uploadDir . time() . '_' . $_FILES['file']['name'];
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            echo json_encode(['error' => 'Error al mover el archivo subido. Verifica los permisos y la ruta.']);
            exit();
        }

        try {
            // Leer solo las primeras 100 filas para preview
            if (!is_readable($targetPath)) {
                throw new Exception('El archivo no es legible o no existe: ' . $targetPath);
            }
            
            $Reader = new SpreadsheetReader($targetPath);
            
            // Verificar que el reader se inicializó correctamente
            if (!$Reader) {
                throw new Exception('No se pudo inicializar el lector de Excel');
            }
            
            $previewRows = [];
            $totalRows = 0;
            $headerRow = null;
            $headerMap = [];

            foreach ($Reader as $index => $row) {
                if ($index === 0) {
                    $headerRow = $row;
                    foreach ($row as $idx => $header) {
                        $headerMap[trim(strtolower($header))] = $idx;
                    }
                    continue;
                }
                
                $totalRows++;
                if ($totalRows <= 100) {
                    $previewRows[] = $row;
                }
            }

            // Guardar información en sesión
            $_SESSION['filePath'] = $targetPath;
            $_SESSION['totalRows'] = $totalRows;
            $_SESSION['headerMap'] = $headerMap;
            $_SESSION['defaultSucursal'] = isset($_POST['Fk_sucursal']) ? $_POST['Fk_sucursal'] : '';

            // Generar preview HTML limitado
            $tableHtml = "<div class='alert alert-info'><strong>Total de filas en el archivo: " . number_format($totalRows) . "</strong><br>";
            $tableHtml .= "Mostrando las primeras 100 filas como vista previa. El procesamiento completo se realizará al confirmar.</div>";
            $tableHtml .= "<div class='table-responsive' style='max-height: 500px; overflow-y: auto;'>";
            $tableHtml .= "<table id='dataTable' class='table table-bordered table-striped table-sm'>";
            $tableHtml .= "<thead class='table-primary'><tr>";
            foreach ($headerRow as $header) {
                $tableHtml .= "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>";
            }
            $tableHtml .= "</tr></thead><tbody>";

            foreach ($previewRows as $row) {
                $tableHtml .= "<tr>";
                foreach ($row as $cell) {
                    $tableHtml .= "<td>" . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . "</td>";
                }
                $tableHtml .= "</tr>";
            }
            $tableHtml .= "</tbody></table></div>";

            echo json_encode([
                'success' => true,
                'totalRows' => $totalRows,
                'previewHtml' => $tableHtml
            ]);
        } catch (Exception $e) {
            error_log('Error en ImportarStockPOS preview: ' . $e->getMessage());
            echo json_encode(['error' => 'Error leyendo archivo: ' . $e->getMessage() . '. Verifique que el archivo sea un Excel válido y que tenga permisos de lectura.']);
        } catch (Error $e) {
            error_log('Error fatal en ImportarStockPOS preview: ' . $e->getMessage());
            echo json_encode(['error' => 'Error fatal procesando archivo: ' . $e->getMessage() . '. Verifique que el archivo sea un Excel válido (.xlsx o .xls).']);
        }
        exit();
    } else {
        echo json_encode(['error' => 'El archivo enviado es inválido.']);
        exit();
    }
}

// Procesar archivo directamente sin cargar todo en memoria
if (isset($_POST["import"])) {
    header('Content-Type: application/json');
    $filePath = isset($_SESSION['filePath']) ? $_SESSION['filePath'] : '';
    $defaultSucursal = isset($_SESSION['defaultSucursal']) ? $_SESSION['defaultSucursal'] : '';
    $headerMap = isset($_SESSION['headerMap']) ? $_SESSION['headerMap'] : [];
    $ActualizadoPor = isset($_POST["ActualizadoPor"]) ? mysqli_real_escape_string($con, $_POST["ActualizadoPor"]) : 'Desconocido';

    if (empty($filePath) || !file_exists($filePath)) {
        echo json_encode(['error' => 'Archivo no encontrado. Por favor vuelva a subirlo.']);
        exit();
    }

    $processedRows = 0;
    $ignoredRows = [];
    $errors = [];

    try {
        if (!is_readable($filePath)) {
            throw new Exception('El archivo no es legible o no existe: ' . $filePath);
        }
        
        $Reader = new SpreadsheetReader($filePath);
        
        // Verificar que el reader se inicializó correctamente
        if (!$Reader) {
            throw new Exception('No se pudo inicializar el lector de Excel');
        }
        
        $rowIndex = 0;

        foreach ($Reader as $row) {
            if ($rowIndex === 0) {
                $rowIndex++;
                continue; // Saltar encabezados
            }

            $rowIndex++;
            
            // Procesar fila
            $tipoServicio = obtenerValorColumna($row, $headerMap, 'tipo_servicio', 0);
            $nombreServicio = obtenerValorColumna($row, $headerMap, 'nombre_servicio', 1);
            $tipo = obtenerValorColumna($row, $headerMap, 'tipo', 2);
            $fkCategoria = obtenerValorColumna($row, $headerMap, 'fkcategoria', 3);
            $nombreCategoria = obtenerValorColumna($row, $headerMap, 'nombre_categoria', 4);
            $fkMarca = obtenerValorColumna($row, $headerMap, 'fkmarca', 5);
            $fkPresentacion = obtenerValorColumna($row, $headerMap, 'fkpresentacion', 6);
            $proveedor1 = obtenerValorColumna($row, $headerMap, 'proveedor1', 7);
            $proveedor2 = obtenerValorColumna($row, $headerMap, 'proveedor2', 8);
            $contable = obtenerValorColumna($row, $headerMap, 'contable', 9);
            $anaquel = obtenerValorColumna($row, $headerMap, 'anaquel', 10);
            $repisa = obtenerValorColumna($row, $headerMap, 'repisa', 11);
            $fkSucursal = obtenerValorColumna($row, $headerMap, 'fk_sucursal', 12);
            if (empty($fkSucursal) && !empty($defaultSucursal)) {
                $fkSucursal = $defaultSucursal;
            }
            $maxExistencia = obtenerValorColumna($row, $headerMap, 'max_existencia', 13);
            $minExistencia = obtenerValorColumna($row, $headerMap, 'min_existencia', 14);

            // Validar campos obligatorios
            if (empty($nombreServicio) || empty($fkSucursal)) {
                $ignoredRows[] = $rowIndex;
                continue;
            }

            // Procesar esta fila
            $result = procesarFila($con, [
                'tipo_servicio' => $tipoServicio,
                'nombre_servicio' => $nombreServicio,
                'tipo' => $tipo,
                'fk_categoria' => $fkCategoria,
                'nombre_categoria' => $nombreCategoria,
                'fk_marca' => $fkMarca,
                'fk_presentacion' => $fkPresentacion,
                'proveedor1' => $proveedor1,
                'proveedor2' => $proveedor2,
                'contable' => $contable,
                'anaquel' => $anaquel,
                'repisa' => $repisa,
                'fk_sucursal' => $fkSucursal,
                'max_existencia' => $maxExistencia,
                'min_existencia' => $minExistencia,
                'actualizado_por' => $ActualizadoPor
            ]);

            if ($result['success']) {
                $processedRows++;
            } else {
                if (count($ignoredRows) < 100) { // Limitar cantidad de errores guardados
                    $ignoredRows[] = $rowIndex . ": " . $result['error'];
                }
            }

            // Liberar memoria periódicamente
            if ($rowIndex % 1000 == 0) {
                gc_collect_cycles();
            }
        }

        // Limpiar archivo temporal
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Preparar respuesta
        $message = "Se procesaron " . number_format($processedRows) . " filas correctamente.";
        $icon = "success";

        if ($processedRows == 0) {
            $message = "No se procesaron filas. Verifique los datos.";
            $icon = "warning";
        }

        if (!empty($ignoredRows) && count($ignoredRows) <= 20) {
            $message .= " Filas ignoradas: " . implode(', ', array_slice($ignoredRows, 0, 10));
        } else if (!empty($ignoredRows)) {
            $message .= " " . count($ignoredRows) . " filas fueron ignoradas.";
        }

        echo json_encode([
            'success' => true,
            'message' => $message,
            'icon' => $icon,
            'processed' => $processedRows,
            'ignored' => count($ignoredRows)
        ]);

    } catch (Exception $e) {
        error_log('Error en ImportarStockPOS import: ' . $e->getMessage());
        echo json_encode(['error' => 'Error procesando archivo: ' . $e->getMessage()]);
    } catch (Error $e) {
        error_log('Error fatal en ImportarStockPOS import: ' . $e->getMessage());
        echo json_encode(['error' => 'Error fatal procesando archivo: ' . $e->getMessage() . '. Verifique que el archivo sea un Excel válido (.xlsx o .xls).']);
    }
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

    <style>
        #previewContainer { display: none; }
        #processingIndicator { display: none; }
    </style>
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
                        <small class="text-muted">Nota: El sistema procesará el archivo fila por fila sin cargar todo en memoria, permitiendo manejar archivos muy grandes.</small>
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
                        <button class="btn btn-primary" type="button" id="btnPreview">Vista previa</button>
                        <div id="errorMessage" class="text-danger mt-2"></div>
                    </form>

                    <div id="previewContainer" class="mt-4">
                        <div id="previewContent"></div>
                        <button class="btn btn-success mt-3" type="button" id="btnImport">Confirmar y Procesar</button>
                    </div>

                    <div id="processingIndicator" class="mt-4">
                        <div class="alert alert-info">
                            <h5>Procesando archivo...</h5>
                            <p>Por favor espere, esto puede tomar varios minutos para archivos grandes.</p>
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('frmExcelImport');
            const btnPreview = document.getElementById('btnPreview');
            const btnImport = document.getElementById('btnImport');
            const previewContainer = document.getElementById('previewContainer');
            const previewContent = document.getElementById('previewContent');
            const processingIndicator = document.getElementById('processingIndicator');
            const errorMessage = document.getElementById('errorMessage');

            btnPreview.addEventListener('click', function() {
                const fileInput = document.getElementById('file');
                const sucursal = document.getElementById('Fk_sucursal').value;

                if (!fileInput.files.length) {
                    errorMessage.textContent = 'Por favor seleccione un archivo.';
                    return;
                }

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('Fk_sucursal', sucursal);
                formData.append('preview', '1');
                formData.append('ActualizadoPor', document.querySelector('input[name="ActualizadoPor"]').value);

                errorMessage.textContent = '';
                btnPreview.disabled = true;
                btnPreview.textContent = 'Cargando...';

                fetch('ImportarStockPOS.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btnPreview.disabled = false;
                    btnPreview.textContent = 'Vista previa';

                    if (data.error) {
                        errorMessage.textContent = data.error;
                        previewContainer.style.display = 'none';
                    } else if (data.success) {
                        previewContent.innerHTML = data.previewHtml;
                        previewContainer.style.display = 'block';
                    }
                })
                .catch(error => {
                    btnPreview.disabled = false;
                    btnPreview.textContent = 'Vista previa';
                    errorMessage.textContent = 'Error: ' + error.message;
                });
            });

            btnImport.addEventListener('click', function() {
                if (!confirm('¿Está seguro de procesar el archivo completo? Esto puede tomar varios minutos.')) {
                    return;
                }

                previewContainer.style.display = 'none';
                processingIndicator.style.display = 'block';

                const formData = new FormData();
                formData.append('import', '1');
                formData.append('ActualizadoPor', document.querySelector('input[name="ActualizadoPor"]').value);

                fetch('ImportarStockPOS.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    processingIndicator.style.display = 'none';

                    if (data.error) {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            title: 'Procesamiento completado',
                            html: '<p>' + data.message + '</p><p>Filas procesadas: ' + data.processed + '</p>',
                            icon: data.icon,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    processingIndicator.style.display = 'none';
                    Swal.fire({
                        title: 'Error',
                        text: 'Error procesando archivo: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                });
            });
        });
    </script>

    <?php include "Footer.php"; ?>
</body>
</html>

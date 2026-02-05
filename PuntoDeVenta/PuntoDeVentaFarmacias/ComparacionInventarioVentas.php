<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Comparación de Inventario con Ventas - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    
        <!-- Spinner End -->

        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->

            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-4">
                <div class="col-12">
                    <div class="bg-light rounded h-100 p-4">
                        <h6 class="mb-4" style="color:#0172b6;">
                            <i class="fa-solid fa-file-excel"></i> Comparación de Inventario con Ventas
                        </h6>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fa-solid fa-upload"></i> Subir Archivo de Inventario</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="formUploadInventario" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="archivoInventario" class="form-label">
                                                    Seleccione el archivo Excel de inventario (inventario_sucursal.xlsx)
                                                </label>
                                                <input type="file" class="form-control" id="archivoInventario" 
                                                       name="archivoInventario" accept=".xlsx,.xls,.csv" required>
                                                <small class="form-text text-muted">
                                                    El archivo debe ser el inventario descargado del sistema. 
                                                    Se comparará con las ventas del corte de caja de la fecha del inventario.
                                                </small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="fechaInventario" class="form-label">
                                                    <i class="fa-solid fa-calendar"></i> Fecha del Inventario
                                                </label>
                                                <input type="date" class="form-control" id="fechaInventario" 
                                                       name="fechaInventario" required>
                                                <small class="form-text text-muted">
                                                    Seleccione la fecha del inventario para comparar con las ventas de ese día.
                                                </small>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-magnifying-glass"></i> Comparar Inventario con Ventas
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resultados de la comparación -->
                        <div id="resultadosComparacion" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Resultados de la Comparación</h5>
                                </div>
                                <div class="card-body">
                                    <div id="resumenComparacion" class="mb-4"></div>
                                    <div id="tablaComparacion"></div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
          
<script src="js/ComparacionInventarioVentas.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>

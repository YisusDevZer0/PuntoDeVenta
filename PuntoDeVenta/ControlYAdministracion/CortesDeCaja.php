<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Cortes de caja de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    
    <style>
        /* Estilos para las estadísticas con colores por importancia */
        .stats-card-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }
    </style>
    
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
          
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">
                <i class="fas fa-scissors me-2"></i>
                Cortes de caja <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?>
            </h6>
            
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">
                        <i class="fas fa-calendar me-1"></i> Fecha Inicio
                    </label>
                    <input type="date" id="fecha_inicio" class="form-control"
                           value="<?php echo date('Y-m-01'); ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">
                        <i class="fas fa-calendar me-1"></i> Fecha Fin
                    </label>
                    <input type="date" id="fecha_fin" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3">
                    <label for="filtro_sucursal" class="form-label">
                        <i class="fas fa-store me-1"></i> Sucursal
                    </label>
                    <select id="filtro_sucursal" class="form-control">
                        <option value="">Todas las sucursales</option>
                        <?php
                        include_once "db_connect.php";
                        $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal";
                        $result_sucursales = $conn->query($sql_sucursales);
                        if ($result_sucursales && $result_sucursales->num_rows > 0) {
                            while ($sucursal = $result_sucursales->fetch_assoc()) {
                                $selected = ($sucursal['ID_Sucursal'] == $row['Fk_Sucursal']) ? 'selected' : '';
                                echo "<option value='" . $sucursal['ID_Sucursal'] . "' $selected>" . htmlspecialchars($sucursal['Nombre_Sucursal']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_cajero" class="form-label">
                        <i class="fas fa-user me-1"></i> Cajero
                    </label>
                    <input type="text" id="filtro_cajero" class="form-control" 
                           placeholder="Buscar por nombre de cajero...">
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-eraser me-1"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportarExcel()">
                            <i class="fas fa-file-excel me-1"></i> Exportar a Excel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas Rápidas -->
            <div class="row mb-4" id="statsRow" style="display: none;">
                <div class="col-md-3">
                    <div class="stats-card-primary">
                        <div class="stats-icon">
                            <i class="fas fa-scissors"></i>
                        </div>
                        <div class="stats-number" id="total-cortes">0</div>
                        <div class="stats-label">Total de Cortes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-success">
                        <div class="stats-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stats-number" id="total-monto">$0</div>
                        <div class="stats-label">Total Monto</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-info">
                        <div class="stats-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stats-number" id="promedio-corte">$0</div>
                        <div class="stats-label">Promedio por Corte</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-warning">
                        <div class="stats-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="stats-number" id="cajas-cerradas">0</div>
                        <div class="stats-label">Cajas Cerradas</div>
                    </div>
                </div>
            </div>
           
            <div id="FCajas"></div>
            </div></div></div>
            </div>
            
            <script src="js/CortesDeCajaRealizados.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>

<script>
   $(document).ready(function() {
    // Delegación de eventos para el botón "btn-MostrarElCorte"
    $(document).on("click", ".btn-MostrarElCorte", function() {
        var id = $(this).data("id");
        console.log("Botón de mostrar corte clickeado para el ID:", id);
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/MuestraElCorteDeCaja.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Mostrando el desglose del corte");
            
        });
        $('#ModalEdDele').modal('show');
    });

});

</script>





  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
    <div class="text-center">
      <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;" >
         <p class="heading lead" id="TitulosCajas"  style="color:white;" ></p>

         
       </div>
        
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCajas"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal --></div>
</body>

</html>
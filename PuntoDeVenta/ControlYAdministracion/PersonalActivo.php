
<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Personal Vigente <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <style>
        /* Estilos para el loading */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ef7980;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
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
          <div class="text-center"> 
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">
                <i class="fas fa-users me-2"></i>
                Personal Activo <?php echo $row['Licencia']?>
            </h6>
            
            <!-- Botón para agregar nuevo personal -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fas fa-user-plus me-1"></i> Agregar nuevo personal
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-success" onclick="exportarExcel()">
                        <i class="fas fa-file-excel me-1"></i> Exportar a Excel
                    </button>
                </div>
            </div>
            
            <!-- Cards Informativas -->
            <div class="row mb-4" id="statsRow">
                <div class="col-md-3">
                    <div class="stats-card-primary">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-number" id="totalPersonal">0</div>
                        <div class="stats-label">Total Personal</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-success">
                        <div class="stats-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stats-number" id="totalAdministrativos">0</div>
                        <div class="stats-label">Administrativos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-info">
                        <div class="stats-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stats-number" id="totalSucursales">0</div>
                        <div class="stats-label">Sucursales Activas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card-warning">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stats-number" id="personalReciente">0</div>
                        <div class="stats-label">Nuevos este mes</div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="filtro_tipo" class="form-label">
                        <i class="fas fa-user-tag me-1"></i> Tipo de Usuario
                    </label>
                    <select id="filtro_tipo" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="Administrativo">Administrativo</option>
                        <option value="Vendedor">Vendedor</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Gerente">Gerente</option>
                    </select>
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
                                echo "<option value='" . $sucursal['ID_Sucursal'] . "'>" . htmlspecialchars($sucursal['Nombre_Sucursal']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">
                        <i class="fas fa-toggle-on me-1"></i> Estado
                    </label>
                    <select id="filtro_estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeUsuarios.js"></script>
<script src="js/AgregarUsuarioss.js"></script>
<script src="js/PersonalActivo.js"></script>


            <!-- Footer Start -->
            <?php 
            include "Modales/AltaNuevoUsuario.php";
           
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>

<script>
   $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Movimientos" dentro de .dropdown-menu
    $(document).on("click", ".btn-edita", function() {
      console.log("Botón de cancelar clickeado para el ID:", id);
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EditaDatosDeUsuario.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Editar datos del usuario");
             // Cambiar tamaño del modal agregando la clase 'modal-xl'
             $("#ModalEdDele .modal-dialog").removeClass("modal-sm modal-lg modal-xl").addClass("modal-lg");
            
        });
        $('#ModalEdDele').modal('show');
    });

    // Delegación de eventos para el botón "btn-Ventas" dentro de .dropdown-menu
    $(document).on("click", ".btn-elimina", function() {
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminarDatosDeUsuario.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Eliminar servicio");
           
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
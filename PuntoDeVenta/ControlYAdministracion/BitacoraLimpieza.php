<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Registro diario de control de energia de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
    <?php
   include "header.php";?>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->
<style>
    .imgpez {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px; /* Esto añade bordes redondeados, opcional */
}

</style>

            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Registros de bitacora de limpieza de  <?php echo $row['Licencia']?></h6>
            <div class="text-center">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#RegistroLimpiezaModal">
                            Registrar Bitácora de Limpieza
                        </button>
        <br>
<div id="Cajas"></div>
            </div></div></div></div>
            </div>
            <script src="js/RegistrosDeBitacora.js"></script>
            <script src="js/GuardaRegistrosBitacora.js"></script>
            <!-- Footer Start -->
            <script>
                // Función para agregar una fila de elemento a la tabla
function agregarElemento() {
    const tabla = document.getElementById("tablaElementos");
    const fila = document.createElement("tr");

    // Campo para el nombre del elemento
    fila.innerHTML = `
        <td><input type="text" class="form-control" name="elemento[]" required></td>
        <td>
            <input type="checkbox" name="lunes_mat[]"> Mañana
            <input type="checkbox" name="lunes_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="martes_mat[]"> Mañana
            <input type="checkbox" name="martes_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="miercoles_mat[]"> Mañana
            <input type="checkbox" name="miercoles_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="jueves_mat[]"> Mañana
            <input type="checkbox" name="jueves_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="viernes_mat[]"> Mañana
            <input type="checkbox" name="viernes_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="sabado_mat[]"> Mañana
            <input type="checkbox" name="sabado_vesp[]"> Tarde
        </td>
        <td>
            <input type="checkbox" name="domingo_mat[]"> Mañana
            <input type="checkbox" name="domingo_vesp[]"> Tarde
        </td>
    `;
    tabla.appendChild(fila);
}

// Función para guardar la bitácora
function guardarBitacora() {
    const formData = new FormData(document.getElementById("formRegistroLimpieza"));

    fetch("Controladores/GuardarBitacoraLimpieza.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Bitácora guardada correctamente.");
            location.reload(); // Recargar la página para ver los cambios
        } else {
            alert("Error al guardar la bitácora.");
        }
    })
    .catch(error => console.error("Error:", error));
}
            </script>
            <?php 
           include "Modales/RegistroDeBitacora.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
        
</body>

</html>
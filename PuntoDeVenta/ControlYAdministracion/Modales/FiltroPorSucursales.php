<div class="modal fade" id="FiltroPorSucursal" tabindex="-1" role="dialog" aria-labelledby="FiltroPorSucursalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-success">
        <div class="modal-content">
            <div class="text-center">
                <div class="modal-header" style="background-color: #ef7980 !important;">
                    <h5 class="modal-title" style="color:white;" id="FiltroPorSucursalLabel">
                        <i class="fas fa-clinic-medical me-2"></i>Filtrar por Sucursal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color:white; opacity: 1;"></button>
                </div>
                <div class="modal-body">
                    <form id="formFiltroSucursal">
                        <div class="form-group">
                            <label for="sucursalFiltro">Seleccione una Sucursal:</label>
                            <select id="sucursalFiltro" class="form-control" required>
                                <option value="">Todas las sucursales</option>
                                <?php 
                                $query = $conn->query("SELECT ID_Sucursal, Nombre_Sucursal, Licencia FROM Sucursales WHERE Licencia='".$row['Licencia']."'");
                                while ($valores = mysqli_fetch_array($query)) {
                                    echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" onclick="aplicarFiltroSucursal()">
                                <i class="fas fa-filter me-1"></i>Aplicar Filtro
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function aplicarFiltroSucursal() {
    var sucursalId = $('#sucursalFiltro').val();
    // Las funciones están definidas en DesgloseDeTickets.php
    if (typeof filtrarPorSucursal === 'function') {
        filtrarPorSucursal(sucursalId);
    } else {
        // Si no está disponible, intentar recargar la página con el filtro
        console.log('Aplicando filtro de sucursal:', sucursalId);
        location.reload();
    }
}
</script>

<!-- Modal para ver elementos de limpieza -->
<div class="modal fade" id="ModalVerElementos" tabindex="-1" aria-labelledby="ModalVerElementosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalVerElementosLabel">
                    <i class="fa fa-list me-2"></i>
                    Elementos de Limpieza
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Elemento</th>
                                <th>Lunes M</th>
                                <th>Lunes V</th>
                                <th>Martes M</th>
                                <th>Martes V</th>
                                <th>Miércoles M</th>
                                <th>Miércoles V</th>
                                <th>Jueves M</th>
                                <th>Jueves V</th>
                                <th>Viernes M</th>
                                <th>Viernes V</th>
                                <th>Sábado M</th>
                                <th>Sábado V</th>
                                <th>Domingo M</th>
                                <th>Domingo V</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyElementos">
                            <tr>
                                <td colspan="16" class="text-center">Cargando elementos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

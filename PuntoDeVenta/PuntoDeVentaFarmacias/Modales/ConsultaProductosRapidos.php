  <!-- Modal -->
  <div class="modal fade" id="ConsultaProductos" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-notify modal-success" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resultModalLabel">Consulta de producto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <!-- Aquí se mostrarán los detalles del producto -->
                            <table class="table table-bordered" id="detailTable">
                                <thead>
                                    <tr>
                                        <th>Código de Barra</th>
                                        <th>Clave Adicional</th>
                                        <th>Nombre del Producto</th>
                                        <th>Precio de Venta</th>
                                        <th>Servicio</th>
                                        <th>Tipo</th>
                                        <th>Proveedor 1</th>
                                        <th>Proveedor 2</th>
                                        <th>Último Movimiento</th>
                                        <th>Existencias</th>
                                        <th>Min. Existencia</th>
                                        <th>Max. Existencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="detailRow">
                                        <!-- Detalles del producto -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
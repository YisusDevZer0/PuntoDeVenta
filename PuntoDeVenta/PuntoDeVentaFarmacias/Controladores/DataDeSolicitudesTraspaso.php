<style>
  .dataTables_wrapper .dataTables_paginate { text-align: center !important; margin-top: 10px !important; }
  .dataTables_paginate .paginate_button { padding: 5px 10px !important; border: 1px solid #ef7980 !important; margin: 2px !important; cursor: pointer !important; font-size: 16px !important; color: #ef7980 !important; background-color: #fff !important; }
  .dataTables_paginate .paginate_button.current { background-color: #ef7980 !important; color: #fff !important; border-color: #ef7980 !important; }
  .dataTables_paginate .paginate_button:hover { background-color: #C80096 !important; color: #fff !important; border-color: #C80096 !important; }
  #TablaSolicitudes th { font-size: 12px; padding: 4px; white-space: nowrap; }
  #TablaSolicitudes { font-size: 12px; border-collapse: collapse; width: 100%; text-align: center; }
  #TablaSolicitudes th { font-size: 16px; background-color: #ef7980 !important; color: white; padding: 10px; }
  #TablaSolicitudes td { font-size: 14px; padding: 8px; border-bottom: 1px solid #ccc; color: #000; }
</style>

<script>
$(function() {
  if ($.fn.DataTable && $.fn.DataTable.isDataTable('#TablaSolicitudes')) {
    $('#TablaSolicitudes').DataTable().destroy();
    $('#TablaSolicitudes').empty();
  }
  $('#TablaSolicitudes').DataTable({
    bProcessing: true,
    ordering: true,
    order: [[6, 'desc']],
    bAutoWidth: false,
    ajax: { url: 'Controladores/ArraySolicitudesTraspasoSucursales.php', dataSrc: 'aaData' },
    columns: [
      { data: 'ID_Solicitud' },
      { data: 'Cod_Barra' },
      { data: 'Nombre_Prod' },
      { data: 'Cantidad_solicitada' },
      { data: 'Sucursal_solicitante' },
      { data: 'Sucursal_solicitada' },
      { data: 'Solicitado_el' },
      { data: 'Estatus' },
      { data: 'Solicitado_por' }
    ],
    lengthMenu: [[20, 50, 100, 250, -1], [20, 50, 100, 250, 'Todos']],
    language: {
      lengthMenu: 'Mostrar _MENU_ registros',
      zeroRecords: 'No hay solicitudes.',
      info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
      infoEmpty: 'Mostrando 0 a 0 de 0',
      infoFiltered: '(filtrado de _MAX_)',
      search: 'Buscar:',
      paginate: { first: '<i class="fas fa-angle-double-left"></i>', last: '<i class="fas fa-angle-double-right"></i>', next: '<i class="fas fa-angle-right"></i>', previous: '<i class="fas fa-angle-left"></i>' }
    },
    dom: '<"d-flex justify-content-between"lf>rtip',
    responsive: true
  });
});
</script>
<div class="table-responsive">
  <table id="TablaSolicitudes" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Código</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Solicitante (sucursal)</th>
        <th>Para sucursal</th>
        <th>Fecha solicitud</th>
        <th>Estatus</th>
        <th>Solicitado por</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

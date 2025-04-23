function CargaChecador(){
  $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayChecadorDiario.php","",function(data){
    $("#DataDeServicios").html(data);
  })
}

$(document).ready(function() {
  // Mensajes de carga aleatorios
  var mensajesCarga = [
    "🔄 Cargando registros...",
    "⌛ Un momento por favor...",
    "📊 Procesando datos...",
    "🔍 Buscando registros...",
    "💫 Casi listo...",
    "🎯 Preparando resultados...",
    "🔄 Sincronizando datos...",
    "⚡ Cargando entradas y salidas...",
    "🎩 Procesando registros del checador..."
  ];

  // Función para mostrar el mensaje de carga con un texto aleatorio
  function mostrarCargando(event, settings) {
    var randomIndex = Math.floor(Math.random() * mensajesCarga.length);
    var mensaje = mensajesCarga[randomIndex];
    document.getElementById('loading-text').innerText = mensaje;
    document.getElementById('loading-overlay').style.display = 'flex';
  }

  // Función para ocultar el mensaje de carga
  function ocultarCargando() {
    document.getElementById('loading-overlay').style.display = 'none';
  }

  tabla = $('#Checador').DataTable({
    "bProcessing": true,
    "ordering": true,
    "stateSave":true,
    "bAutoWidth": false,
    "order": [[ 0, "desc" ]],
    "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayChecadorDiario.php",
    "aoColumns": [
      { mData: 'ID_Registro' },
      { mData: 'Nombre_Emp' },
      { mData: 'Fecha_Registro' },
      { mData: 'Hora_Registro' },
      { mData: 'Tipo_Registro' },
      { mData: 'Sucursal' },
      { mData: 'Turno' }
    ],
    "lengthMenu": [[20,150,250,500, -1], [20,50,250,500, "Todos"]],  
    "language": {
      "lengthMenu": "Mostrar _MENU_ registros",
      "sPaginationType": "extStyle",
      "zeroRecords": "No se encontraron resultados",
      "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "infoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sSearch": "Buscar:",
      "paginate": {
        "first": '<i class="fas fa-angle-double-left"></i>',
        "last": '<i class="fas fa-angle-double-right"></i>',
        "next": '<i class="fas fa-angle-right"></i>',
        "previous": '<i class="fas fa-angle-left"></i>'
      },
      "processing": function () {
        mostrarCargando();
      }
    },
    "initComplete": function() {
      ocultarCargando();
    },
    "buttons": [
      {
        extend: 'excelHtml5',
        text: 'Exportar a Excel  <i class="fas fa-file-excel"></i> ',
        titleAttr: 'Exportar a Excel',
        title: 'Registro de Entradas y Salidas',
        className: 'btn btn-success',
        exportOptions: {
          columns: ':visible'
        }
      }
    ],
    "dom": '<"d-flex justify-content-between"lBf>rtip',
    "responsive": true
  });
});

  
  
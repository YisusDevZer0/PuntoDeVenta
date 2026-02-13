<?php
include_once "Controladores/ControladorUsuario.php";
$fechaActual = date('Y-m-d'); // Esto obtiene la fecha actual en el formato 'Año-Mes-Día'$fecha
  $sql = "SELECT * FROM Solicitudes_Ingresos ORDER BY IdProdCedis  DESC LIMIT 1";
  $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
  $Ticketss = mysqli_fetch_assoc($resultset);
  
  $monto1 = $Ticketss['NumOrden'];
  $monto2 = 1;
  $totalmonto = $monto1 + $monto2;
  

  
  




?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ingresos con lotes y caducidad <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
   <div id="loading-overlay"><div class="loader"></div><div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div></div>
<body class="ingresos-lotes-module">
<style>
  /* ========== Módulo Ingresos con Lotes - Interfaz mejorada ========== */
  .ingresos-lotes-module .page-hero {
    background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
    color: #fff;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(13, 71, 161, 0.35);
  }
  .ingresos-lotes-module .page-hero h1 { font-size: 1.5rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
  .ingresos-lotes-module .page-hero p { margin: 0.35rem 0 0 0; opacity: 0.95; font-size: 0.9rem; }
  .ingresos-lotes-module .card-modulo {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 1.25rem;
    overflow: hidden;
  }
  .ingresos-lotes-module .card-modulo .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 0.75rem 1.25rem;
    font-size: 0.95rem;
  }
  .ingresos-lotes-module .card-modulo .card-body { padding: 1.25rem; }
  .ingresos-lotes-module .input-group-text.ico {
    background: #e3f2fd;
    color: #1565c0;
    border-color: #90caf9;
    border-radius: 8px 0 0 8px;
    min-width: 42px;
    justify-content: center;
  }
  .ingresos-lotes-module .form-control:focus { border-color: #1976d2; box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.2); }
  .ingresos-lotes-module .scanner-zone {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border: 2px dashed #43a047;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
  }
  .ingresos-lotes-module .scanner-zone:focus-within { border-color: #2e7d32; background: #fff; box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.2); }
  .ingresos-lotes-module .scanner-zone .form-control { border-radius: 8px; font-size: 1rem; padding: 0.6rem 1rem; }
  .ingresos-lotes-module #tablaAgregarArticulos {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    font-size: 13px;
  }
  .ingresos-lotes-module #tablaAgregarArticulos thead th {
    background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%) !important;
    color: #fff !important;
    padding: 12px 10px;
    font-weight: 600;
    font-size: 13px;
    border: none;
    white-space: nowrap;
  }
  .ingresos-lotes-module #tablaAgregarArticulos tbody tr:hover { background-color: #f5f9ff !important; }
  .ingresos-lotes-module #tablaAgregarArticulos tbody td { padding: 10px; vertical-align: middle; border-color: #eee; }
  .ingresos-lotes-module .btn-enviar-modulo {
    background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
    color: #fff;
    border: none;
    padding: 0.65rem 1.75rem;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(46, 125, 50, 0.4);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .ingresos-lotes-module .btn-enviar-modulo:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(46, 125, 50, 0.45); }
  .ingresos-lotes-module .badge-obligatorio { font-size: 0.7rem; vertical-align: middle; }
  .ingresos-lotes-module .alert-lotes { border-radius: 10px; border-left: 4px solid #ff9800; }
  .ingresos-lotes-module .btn-instructions { border-radius: 8px; }
  .ingresos-lotes-module .btn-container { display: flex; justify-content: center; align-items: center; }
  .ingresos-lotes-module .input-container { display: flex; flex-direction: column; align-items: center; }
  .ingresos-lotes-module .loader-container { justify-content: center; align-items: center; height: 180px; }
  /* Asegurar que SweetAlert2 aparezca sobre todo */
  .swal2-container { z-index: 99999 !important; }
</style>

        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Table Start -->
          
            <script>
  // Mensajes de carga aleatorios
  const loadingMessages = [
    'Cargando...',
    'Por favor, espera...',
    'Procesando...',
    'Cargando datos...',
    'Cargando contenido...',
    '¡Casi listo!',
    'Estamos preparando todo...',
  ];

  function getRandomMessage() {
            var mensajesCarga = [
                "Consultando ventas...",
                "Estamos realizando la búsqueda...",
                "Cargando datos...",
                "Procesando la información...",
                "Espere un momento...",
                // more messages...
            ];
            return mensajesCarga[Math.floor(Math.random() * mensajesCarga.length)];
        }
        
        window.addEventListener('load', function() { try { Swal.close(); } catch(e){} $('#loading-overlay').hide(); });
     
        
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si el elemento 'proveedoresSelect' existe antes de agregar el event listener
        var selectElement = document.getElementById('proveedoresSelect');
        if (selectElement) {
            selectElement.addEventListener('change', toggleCodigoEscaneado);
        } else {
            console.error('El elemento proveedoresSelect no existe.');
        }

        // Verificar si el elemento 'numerofactura' existe antes de agregar el event listener
        var facturaElement = document.getElementById('numerofactura');
        if (facturaElement) {
            facturaElement.addEventListener('input', toggleCodigoEscaneado);
        } else {
            console.error('El elemento numerofactura no existe.');
        }

        // Llamar a la función para establecer el estado inicial del input
        toggleCodigoEscaneado();
    });

    // Función para deshabilitar o habilitar el input según el valor del select
    function toggleCodigoEscaneado() {
        var selectElement = document.getElementById('proveedoresSelect');
        var inputElement = document.getElementById('codigoEscaneado');
        var facturaElement = document.getElementById('numerofactura');
        if (selectElement && facturaElement && inputElement) {
            if (selectElement.value === "" || facturaElement.value.trim() === "") {
                inputElement.disabled = true;
            } else {
                inputElement.disabled = false;
            }
        }
    }
</script>

<style>
 
 .loader-container {
    
    justify-content: center;
    align-items: center;
    height: 180px;
  }


  .absCenter {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  .loaderPill {
    text-align: center;
  }

  .loaderPill-anim {
    height: 160px;
  }

  .loaderPill-anim-bounce {
    animation: loaderPillBounce 1800ms linear infinite;
  }

  .loaderPill-anim-flop {
    transform-origin: 50% 50%;
    animation: loaderPillFlop 1800ms linear infinite;
  }

  .loaderPill-pill {
    display: inline-block;
    box-sizing: border-box;
    width: 80px;
    height: 30px;
    border-radius: 15px;
    border: 1px solid #237db5;
    background-image: linear-gradient(to right, #C80096 50%, #ffffff 50%);
  }

  .loaderPill-floor {
    display: block;
    text-align: center;
  }

  .loaderPill-floor-shadow {
    display: inline-block;
    width: 70px;
    height: 7px;
    border-radius: 50%;
    background-color: rgba(35, 125, 181, 0.26);
    transform: translateY(-15px);
    animation: loaderPillScale 1800ms linear infinite;
  }

  .loaderPill-text {
    font-weight: bold;
    color: #C80096;
    text-transform: uppercase;
  }

  @keyframes loaderPillBounce {
    0% {
      transform: translateY(123px);
      animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    25% {
      transform: translateY(40px);
      animation-timing-function: cubic-bezier(0.55, 0.085, 0.68, 0.53);
    }
    50% {
      transform: translateY(120px);
      animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    75% {
      transform: translateY(20px);
      animation-timing-function: cubic-bezier(0.55, 0.085, 0.68, 0.53);
    }
    100% {
      transform: translateY(120px);
    }
  }

  @keyframes loaderPillFlop {
    0% {
      transform: rotate(0);
    }
    25% {
      transform: rotate(90deg);
    }
    50% {
      transform: rotate(180deg);
    }
    75% {
      transform: rotate(450deg);
    }
    100% {
      transform: rotate(720deg);
    }
  }

  @keyframes loaderPillScale {
    0% {
      transform: translateY(-15px) scale(1, 1);
      animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    25% {
      transform: translateY(-15px) scale(0.7, 1);
      animation-timing-function: cubic-bezier(0.55, 0.085, 0.68, 0.53);
    }
    50% {
      transform: translateY(-15px) scale(1, 1);
      animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    75% {
      transform: translateY(-15px) scale(0.6, 1);
      animation-timing-function: cubic-bezier(0.55, 0.085, 0.68, 0.53);
    }
    100% {
      transform: translateY(-15px) scale(1, 1);
    }
  }
</style>

<!-- Main content - Interfaz mejorada -->
<div class="container-fluid py-3">
  <input type="hidden" id="nombreUsuario" value="<?php echo htmlspecialchars($row['Nombre_Apellidos'] ?? ''); ?>">

  <!-- Hero / Título del módulo -->
  <div class="page-hero">
    <h1><i class="fas fa-boxes-stacked"></i> Ingresos con Lotes y Caducidad</h1>
    <p>Registra productos con lote y fecha de caducidad para actualizar stock e historial automáticamente.</p>
  </div>

  <form action="javascript:void(0)" method="post" id="VentasAlmomento">
    <!-- Card: Datos del ingreso -->
    <div class="card card-modulo">
      <div class="card-header"><i class="fas fa-file-invoice mr-2"></i>Datos del ingreso</div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-6 mb-3">
            <label class="small font-weight-bold text-secondary">Proveedor</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text ico"><i class="fas fa-truck"></i></span></div>
              <select id="proveedoresSelect" class="form-control">
                <option value="">Seleccionar proveedor</option>
              </select>
            </div>
          </div>
          <div class="col-md-2 col-6 mb-3">
            <label class="small font-weight-bold text-secondary">Fecha</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text ico"><i class="fas fa-calendar-alt"></i></span></div>
              <input type="text" class="form-control" readonly value="<?php echo $fechaActual ?>">
            </div>
          </div>
          <div class="col-md-2 col-6 mb-3">
            <label class="small font-weight-bold text-secondary">Nº Factura</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text ico"><i class="fas fa-receipt"></i></span></div>
              <input type="text" class="form-control" id="numerofactura" placeholder="Ej. 001">
            </div>
          </div>
          <div class="col-md-2 col-6 mb-3">
            <label class="small font-weight-bold text-secondary"># Solicitud</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text ico"><i class="fas fa-hashtag"></i></span></div>
              <input type="text" class="form-control" readonly value="<?php echo $totalmonto ?>">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card: Escanear producto -->
    <div class="card card-modulo">
      <div class="card-header"><i class="fas fa-barcode mr-2"></i>Escanear o buscar producto</div>
      <div class="card-body">
        <div class="collapse mb-2" id="instruccionesIngresosLotes">
          <div class="alert alert-lotes alert-info small py-2">
            <strong><i class="fas fa-info-circle"></i> Instrucciones:</strong>
            <ol class="mb-0 pl-3 mt-1">
              <li>Selecciona <b>proveedor</b> e ingresa <b>número de factura</b> para habilitar el escáner.</li>
              <li>Escanea o escribe y elige el producto; completa en cada fila: <b>cantidad</b>, <b>fecha de caducidad</b>, <b>lote</b> y <b>precio máximo</b>.</li>
              <li><b>Lote</b> y <b>fecha de caducidad</b> son obligatorios para actualizar Stock e Historial de lotes.</li>
            </ol>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-primary btn-instructions mb-2" type="button" data-toggle="collapse" data-target="#instruccionesIngresosLotes"><i class="fas fa-question-circle"></i> Ver instrucciones</button>
        <div class="scanner-zone">
          <label class="small font-weight-bold text-secondary d-block mb-1">Código de barras o nombre del producto</label>
          <input type="text" class="form-control producto" name="codigoEscaneado" id="codigoEscaneado" placeholder="Escanea o escribe para buscar..." autocomplete="off" onchange="buscarArticulo();">
        </div>
      </div>
    </div>

    <!-- Card: Lista de productos -->
    <div class="card card-modulo">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <span><i class="fas fa-list-ul mr-2"></i>Lista de productos</span>
        <button type="submit" class="btn btn-enviar-modulo btn-sm"><i class="fas fa-paper-plane mr-1"></i> Guardar ingresos</button>
      </div>
      <div class="card-body">
        <div class="alert alert-warning alert-lotes small py-2 mb-3">
          <i class="fas fa-exclamation-triangle"></i> Completa <b>Lote</b> y <b>Fecha de caducidad</b> en cada fila antes de guardar.
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0" id="tablaAgregarArticulos" style="width:100%; table-layout: fixed;">
            <thead>
              <tr>
                <th>Código</th>
                <th style="width:22%">Producto</th>
                <th style="width:8%">Cantidad</th>
                <th>Fecha caducidad <span class="badge badge-danger badge-obligatorio">*</span></th>
                <th>Lote <span class="badge badge-danger badge-obligatorio">*</span></th>
                <th>Precio máx.</th>
                <th style="width:80px">Eliminar</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </form>
</div>
<!-- function actualizarSumaTotal  -->
<script>

  
  function actualizarSumaTotal() {
  var iptTarjeta = parseFloat(document.getElementById("iptTarjeta").value);
  var iptEfectivo = parseFloat(document.getElementById("iptEfectivoRecibido").value);
  var cambio;

  if (iptTarjeta > 0) {
    cambio = 0; // Si se ingresa un valor en el campo de tarjeta, el cambio se establece en cero
  } else {
    cambio = iptEfectivo; // Si no se ingresa un valor en el campo de tarjeta, el cambio se calcula como el efectivo recibido
  }

  // Actualizar el valor del elemento <span> con el cambio
  document.getElementById("Vuelto").textContent = cambio.toFixed(2);
}








</script>

<script>
  $(document).ready(function() {
    // Bloquear el botón al cargar la página
    $('#btnIniciarVenta').prop('disabled', true);

    // Agregar un controlador de eventos al input
    $('#iptEfectivoRecibido').on('input', function() {
      var valorInput = $(this).val();
      var miBoton = $('#btnIniciarVenta');

      if (valorInput.length > 0) {
        // Desbloquear el botón si el input contiene datos
        miBoton.prop('disabled', false);
      } else {
        // Bloquear el botón si el input está vacío
        miBoton.prop('disabled', true);
      }
    });
  });


  $(document).ready(function() {
    $("#chkEfectivoExacto").change(function() {
      if ($(this).is(":checked")) {
        var boletaTotal = parseFloat($("#boleta_total").text());
        $("#Vuelto").text("0.00");
        $("#iptEfectivoRecibido").val(boletaTotal.toFixed(2));
      }
    });

    $("#iptEfectivoRecibido").change(function() {
      var boletaTotal = parseFloat($("#boleta_total").text());
      var efectivoRecibido = parseFloat($(this).val());

      if ($("#chkEfectivoExacto").is(":checked") && boletaTotal >= efectivoRecibido) {
        $("#Vuelto").text("0.00");
        $("#boleta_total").text(efectivoRecibido.toFixed(2));
      } else {
        var vuelto = efectivoRecibido - boletaTotal;
        $("#Vuelto").text(vuelto.toFixed(2));
        $("#cambiorecibidocliente").val(vuelto.toFixed(2));
        
      }
    });
  });
</script>

<script>
  $("#btnVaciarListado").click(function() {
    console.log("Click en el botón");
    $("#tablaAgregarArticulos tbody").empty();
    actualizarImporte($('#tablaAgregarArticulos tbody tr:last-child'));
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
    mostrarSubTotal();
    mostrarIvaTotal()
  });


  function actualizarEfectivoEntregado() {
  var inputEfectivo = document.getElementById("iptEfectivoRecibido");
  var spanEfectivoEntregado = document.getElementById("EfectivoEntregado");
  var inputEfectivoOculto = document.getElementById("iptEfectivoOculto");

  spanEfectivoEntregado.innerText = inputEfectivo.value;
  inputEfectivoOculto.value = inputEfectivo.value;
}

</script>

<script>
       let selectedAdjustment = "";

document.getElementById('proveedoresSelect').addEventListener('change', function() {
    selectedAdjustment = this.value;
});

    </script>


<script>
       let selectedfactura = "";

document.getElementById('numerofactura').addEventListener('change', function() {
  selectedfactura = this.value;
});

    </script>

<script>
  table = $('#tablaAgregarArticulos').DataTable({
    searching: false, // Deshabilitar la funcionalidad de búsqueda
    paging: false, // Deshabilitar el paginador
    "columns": [{
        "data": "id"
      },
      {
        "data": "codigo"
      },
      {
        "data": "descripcion"
      },
      {
        "data": "cantidad"
      },
      {
        "data": "stockactual"
      },{
        "data": "diferencia"
      },
      {
        "data": "precio"
      },
      // {
      //     "data": "importesiniva"
      // },
      // {
      //     "data": "ivatotal"
      // },
      // {
      //     "data": "ieps"
      // },
      {
        "data": "eliminar"
      },
      {
        "data": "descuentos"

      },
      {
        "data": "descuentos2"
      },
    ],

    "order": [
      [0, 'desc']

    ],
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    //para usar los botones   
    responsive: "true",

  });

 

  

  function buscarArticulo(codigoEscaneado) {
  var formData = new FormData();
  formData.append('codigoEscaneado', codigoEscaneado);

  $.ajax({
    url: "Controladores/BusquedaPorEscaner.php",
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (data) {
      if (data.length === 0) {
        // msjError('No Encontrado');
      } else if (data.codigo) {
        agregarArticulo(data);
      }

      limpiarCampo();
    },
    error: function (data) {
      
    }
  });
}

function limpiarCampo() {
  $('#codigoEscaneado').val('');
  $('#codigoEscaneado').focus();
}

var isScannerInput = false;

// Escucha el evento keyup en el campo de búsqueda
$('#codigoEscaneado').keyup(function (event) {
  if (event.which === 13) { // Verifica si la tecla presionada es "Enter"
    if (!isScannerInput) { // Verifica si el evento no viene del escáner
      var codigoEscaneado = $('#codigoEscaneado').val();
      buscarArticulo(codigoEscaneado);
      event.preventDefault(); // Evita que el formulario se envíe al presionar "Enter"
    }
    isScannerInput = false; // Restablece la bandera del escáner
  }
});

// Agrega el autocompletado al campo de búsqueda
$('#codigoEscaneado').autocomplete({
  source: function (request, response) {
    // Realiza una solicitud AJAX para obtener los resultados de autocompletado
    $.ajax({
      url: 'Controladores/DespliegaAutoComplete.php',
      type: 'GET',
      dataType: 'json',
      data: {
        term: request.term
      },
      success: function (data) {
        response(data);
      }
    });
  },
  minLength: 3, // Especifica la cantidad mínima de caracteres para activar el autocompletado
  select: function (event, ui) {
    // Cuando se selecciona un resultado del autocompletado, llamar a la función buscarArticulo() con el código seleccionado
    var codigoEscaneado = ui.item.value;
    isScannerInput = true; // Establece la bandera del escáner
    $('#codigoEscaneado').val(codigoEscaneado);
    buscarArticulo(codigoEscaneado);
  }
});
  

// En este módulo NO hay "diferencia" ni existencias BD en la fila: Cantidad, Fecha caducidad, Lote y Precio máx.
// son independientes. No actualizar Lote ni Precio máx. al cambiar Cantidad.



  var tablaArticulos = ''; // Variable para almacenar el contenido de la tabla



  // Variable para almacenar el total del IVA
  var totalIVA = 0;

  // Función para agregar un artículo
  function agregarArticulo(articulo) {
    if (!articulo || !articulo.id) {
      mostrarMensaje('El artículo no es válido');
    } else if ($('#detIdModal' + articulo.id).length) {
      mostrarMensaje('El artículo ya se encuentra incluido');
    } else {
      var row = $('#tablaAgregarArticulos tbody').find('tr[data-id="' + articulo.id + '"]');
      if (row.length) {
        var cantidadActual = parseInt(row.find('.cantidad input').val());
        var nuevaCantidad = cantidadActual + parseInt(articulo.cantidad);
        if (nuevaCantidad < 0) {
          mostrarMensaje('La cantidad no puede ser negativa');
          return;
        }
        row.find('.cantidad input').val(nuevaCantidad);
        
    
     
        
      } else {
       
        var tr = '';
        var btnEliminar = '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button>';
      

        var inputId = '<input type="hidden" name="detIdModal[' + articulo.id + ']" value="' + articulo.id + '" />';
        var inputCantidad = '<input class="form-control" type="hidden" name="detCantidadModal[' + articulo.id + ']" value="' + articulo.cantidad + '" />';

        tr += '<tr data-id="' + articulo.id + '">';

        tr += '<td class="codigo"><input class="form-control codigo-barras-input" id="codBarrasInput" style="font-size: 0.75rem !important;" type="text" value="' + articulo.codigo + '" name="CodBarras[]" /></td>';
        tr += '<td class="descripcion"><textarea class="form-control descripcion-producto-input" id="descripcionproducto"name="NombreDelProducto[]" style="font-size: 0.75rem !important;">' + articulo.descripcion + '</textarea></td>';
        tr += '<td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem !important;" type="number" name="Contabilizado[]" value="' + articulo.cantidad + '" /></td>';
tr += '<td class="ExistenciasEnBd"><input class="form-control input-fecha-caducidad" style="font-size: 0.75rem !important;" type="date" name="FechaCaducidad[]" value="' + (articulo.fechacaducidad || articulo.existencia || '') + '" placeholder="Requerido" title="Fecha de caducidad" /></td>';
tr += '<td class="Diferenciaresultante"><input class="form-control input-lote" style="font-size: 0.75rem !important;" type="text" name="Lote[]" placeholder="Requerido" title="Número de lote" /></td>';
tr += '<td class="Preciototal"><input class="form-control input-precio-max" style="font-size: 0.75rem !important;" type="text" name="PrecioMaximo[]" placeholder="Precio máx." title="Precio máximo" /></td>';
tr += '<td style="visibility:collapse; display:none;" class="Proveedor"><input class="form-control proveedor-input" style="font-size: 0.75rem !important;" id="proveedor" type="text" name="Proveedor[]" /></td>';
tr += '<td   style="visibility:collapse; display:none;"class="factura"><input class="form-control factura-input" style="font-size: 0.75rem !important;" id="facturanumber" type="text" name="FacturaNumber[]" /></td>';
tr += '<td   style="visibility:collapse; display:none;"class="numerorden"><input class="form-control" style="font-size: 0.75rem !important;" value="<?php echo  $totalmonto?>" type="text" name="NumberOrden[]" /></td>';

        tr += '<td style="visibility:collapse; display:none;" class="preciofijo"><input class="form-control preciou-input" style="font-size: 0.75rem !important;" type="number" name="PrecioVenta[]" value="' + articulo.precio + '"  /></td>';
        tr += '<td style="visibility:collapse; display:none;"class="preciodecompra"><input class="form-control preciocompra-input" style="font-size: 0.75rem !important;" type="number"  name="PrecioCompra[]" value="' + articulo.preciocompra + '"  /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="precio"><input hidden id="precio_' + articulo.id + '"class="form-control precio" style="font-size: 0.75rem !important;" type="number" name="PrecioVentaProd[]" value="' + articulo.precio + '" onchange="actualizarImporte($(this).parent().parent());" /></td>';
        tr += '<tdstyle="visibility:collapse; display:none;"><input id="importe_' + articulo.id + '" class="form-control importe" name="ImporteGenerado[]"style="font-size: 0.75rem !important;" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="importe_siniva_' + articulo.id + '" class="form-control importe_siniva" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="valordelniva_' + articulo.id + '" class="form-control valordelniva" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="ieps_' + articulo.id + '" class="form-control ieps" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"class="idbd"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="' + articulo.id + '" name="IdBasedatos[]" /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="NumberSucursal"><input type="hidden" id="estatussolicitud" class="form-control" name="Estatusdesolicitud[]" readonly value="Pendiente" /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="NumberSucursal"><input type="hidden" id="sucursal" class="form-control" name="FkSucursal[]" readonly value="<?php echo isset($row['Fk_Sucursal']) ? htmlspecialchars((string)$row['Fk_Sucursal'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="ResponsableInventario"><input type="hidden" id="VendedorFarma" class="form-control" name="AgregoElVendedor[]" readonly value="<?php echo htmlspecialchars((string)($row['Nombre_Apellidos'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="Fecha"><input type="hidden" class="form-control" name="FechaDeInventario[]" readonly value="<?php echo isset($fechaActual) ? htmlspecialchars((string)$fechaActual, ENT_QUOTES, 'UTF-8') : date('Y-m-d'); ?>" /></td>';
        tr += '<td><div class="btn-container">' + btnEliminar + '</div><div class="input-container"></td>';
      

        tr += '</tr>';

        $('#tablaAgregarArticulos tbody').append(tr);
        $('#tablaAgregarArticulos tbody tr:last-child').find('.proveedor-input').val(selectedAdjustment);
        $('#tablaAgregarArticulos tbody tr:last-child').find('.factura-input').val(selectedfactura);
        
     
       
      }
    }

    $('#codigoEscaneado').val('');
    $('#codigoEscaneado').focus();
  }

  






  
 
 
// Modificar la función eliminarFila() para llamar a las funciones necesarias después de eliminar la fila
function eliminarFila(element) {
  var fila = $(element).closest('tr'); // Obtener la fila más cercana al elemento
  fila.remove(); // Eliminar la fila

 
}


</script>

<script src="js/RealizaIngresoConLotes.js"></script>

<script src="js/ConectaProveedores.js"></script>
<!-- Control Sidebar -->

<!-- Main Footer -->
<?php

include("Modales/Error.php");
include("Modales/Exito.php");
            
include "Footer.php";?>


  

  <!-- Bootstrap -->


  <!-- PAGE PLUGINS -->

 

<?php

function fechaCastellano($fecha)
{
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
  $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
}
?>
<script>




</script>


           
</body>

</html>
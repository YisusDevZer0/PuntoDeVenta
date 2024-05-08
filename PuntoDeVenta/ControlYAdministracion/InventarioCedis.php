<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos en cedis <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>
 
  <style>
    .error {
      color: red;
      margin-left: 5px;

    }

    #Tarjeta2 {
      background-color: #e83e8c !important;
      color: white;
    }
    .btn-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.input-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

    
  </style>


</head>
<?php include_once("Menu.php") ?>
<!-- Aquí se carga la pantalla de inicio -->
<!-- Aquí se carga la pantalla de inicio -->

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

  // Función para obtener un mensaje aleatorio de carga
  function getRandomMessage() {
    return loadingMessages[Math.floor(Math.random() * loadingMessages.length)];
  }

  // Mostrar SweetAlert2 de carga al iniciar la página
  Swal.fire({
    title: 'Cargando',
    html: '<div class="loader-container">' +
            '<div class="absCenter">' +
              '<div class="loaderPill">' +
                '<div class="loaderPill-anim">' +
                  '<div class="loaderPill-anim-bounce">' +
                    '<div class="loaderPill-anim-flop">' +
                      '<div class="loaderPill-pill"></div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="loaderPill-floor">' +
                  '<div class="loaderPill-floor-shadow"></div>' +
                '</div>' +
                `<div class="loaderPill-text" style="color: #C80096">${getRandomMessage()}</div>` +
              '</div>' +
            '</div>' +
          '</div>',
    showCancelButton: false,
    showConfirmButton: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
    onBeforeOpen: () => {
      Swal.showLoading();
    },
    customClass: {
      container: 'animated fadeInDown'
    }
  });

  // Actualizar mensaje aleatoriamente cada 2 segundos
  setInterval(() => {
    const messageElement = document.querySelector('.loaderPill-text');
    if (messageElement) {
      messageElement.textContent = getRandomMessage();
    }
  }, 2000);

  // Ocultar SweetAlert2 de carga cuando la página se haya cargado por completo
  window.addEventListener('load', function() {
    Swal.close();
  });
</script>

<style>
  .loader-container {
    display: flex;
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

<!-- Main content -->
<div class="content">


  <div class="container-fluid">

    <div class="row mb-3">

  

          <div class="card-body p-3">
            
            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="row">
                  <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                  <div class="col-md-12 mb-3">

                    <div class="form-group mb-2">
                      <div class="row">
                        <input hidden type="text" class="form-control " readonly value="<?php echo $row['Nombre_Apellidos'] ?>">

                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Sucursal</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
                            </div>
                            <input type="text" class="form-control " style="font-size: 0.75rem !important;" readonly value="<?php echo $row['Nombre_Sucursal'] ?>">
                           
                          </div>
                        </div>

                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Tipo de ajuste</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-clock"></i></span>
                            </div>
                            <select class="form-control" style="font-size: 0.75rem !important;">
                            <option value="">Seleccione un tipo de ajuste </option>
                  <option value="Ajuste positivo">Ajuste de inventario</option>
              <option value="Inventario inicial">Inventario inicial</option>
                 <option value="Ajuste por daño">Ajuste por daño</option>
              <option value="Ajuste por caducidad">Ajuste por caducidad</option>
</select>


                          </div>
                        </div>
                        


                      </div>

                      <label class="col-form-label" for="iptCodigoVenta">
                        <i class="fas fa-barcode fs-6"></i>
                        <span class="small">Productos</span>
                      </label>

                      <div class="input-group">

                        <input type="text" class="form-control producto" name="codigoEscaneado" id="codigoEscaneado" style="position: relative;" onchange="buscarArticulo();">
                      </div>
                    </div>

                  </div>

                  <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
               

                  <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->
                  <!-- <div class="col-md-5 text-right">

                    <button class="btn btn-danger btn-sm" id="btnVaciarListado">
                      <i class="far fa-trash-alt"></i> Vaciar Listado
                    </button>
                  </div> -->

                  <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                  <div class="table-responsive">
                    <div class="col-md-12">
                      <style>
                        #tablaAgregarArticulos {
                          width: 100%;
                          table-layout: fixed;
                        }

                        #tablaAgregarArticulos td,
                        #tablaAgregarArticulos th {
                          white-space: nowrap;
                          overflow: hidden;
                          text-overflow: visible;


                        }

                        .smaller-button {
                          padding: 0.25rem 0.5rem;
                          font-size: 0.8rem;
                        }


                        div.card-body p-2 {
                          margin-top: -10px !important;
                          margin-bottom: 5px !important;
                        }
                      </style>
                      <form action="javascript:void(0)" target="print_popup" method="post" id="VentasAlmomento">
                        <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                          <thead>
                            <tr>
                              <th>Codigo</th>
                              <th style="width:20%">Producto</th>
                              <th style="width:6%">Contabilizado</th>
                              <th style="width:6%">Stock actual</th>
                              <th style="width:6%">Diferencia</th>
                              <th>Precio</th>
                              <th>Importe</th>
                              <!-- <th>importe_Sin_Iva</th>
            <th>Iva</th>
            <th>valorieps</th> -->
                              <th>Eliminar</th>
                            
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                        </table>
                        <!-- / table -->
                        
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                
              </div>
            
             

              

            </div>

          </div> <!-- ./ end card-body -->
        </div>

      </div>
     
</div>
</div>
</div>
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

  function mostrarTotalVenta() {
    var totalVenta = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
        var importe = parseFloat($(this).find('.importe').val().replace(/[^\d.-]/g, ''));
        if (!isNaN(importe)) {
            totalVenta += importe;
        }
    });

 
    $('#totalVenta').text(totalVenta.toFixed(2));
    $('#boleta_total').text(totalVenta.toFixed(2));
    $("#totaldeventacliente").val(totalVenta.toFixed(2));
    
}



  function mostrarSubTotal() {
    var subtotal = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.importe_siniva').val().replace(/[^\d.-]/g, ''));
      if (!isNaN(importeSinIVA)) {
        subtotal += importeSinIVA;
      }
    });

    $('#boleta_subtotal').text(subtotal.toFixed(2));
  }


  function mostrarIvaTotal() {
    var subtotal = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.valordelniva').val().replace(/[^\d.-]/g, ''));
      if (!isNaN(importeSinIVA)) {
        subtotal += importeSinIVA;
      }
    });

    $('#ivatotal').text(subtotal.toFixed(2));
  }

  function buscarArticulo(codigoEscaneado) {
  var formData = new FormData();
  formData.append('codigoEscaneado', codigoEscaneado);

  $.ajax({
    url: "Consultas/escaner_articulo.php",
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
      url: 'Consultas/autocompletado.php',
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
  

// Agregar evento change al input de cantidad vendida
$(document).on('change', '.cantidad-vendida-input', function() {
    // Obtener la fila actual
    var fila = $(this).closest('tr');
    
    // Obtener el valor del input de cantidad vendida
    var cantidadVendida = parseInt($(this).val());
    
    // Obtener el valor del input de existencias en la base de datos
    var existenciasBd = parseInt(fila.find('.cantidad-existencias-input').val());
    
    // Calcular la diferencia
    var diferencia = cantidadVendida - existenciasBd;
    
    // Actualizar el valor del input de diferencia
    fila.find('.cantidad-diferencia-input').val(diferencia);
});
// Función para calcular la diferencia entre la cantidad vendida y las existencias en la base de datos
function calcularDiferencia(fila) {
    // Obtener la cantidad vendida y las existencias de la fila actual
    var cantidadVendida = parseInt(fila.find('.cantidad-vendida-input').val());
    var existenciasBd = parseInt(fila.find('.cantidad-existencias-input').val());

    // Calcular la diferencia
    var diferencia = cantidadVendida - existenciasBd;

    // Actualizar el valor del input de diferencia en la fila actual
    fila.find('.cantidad-diferencia-input').val(diferencia);
}


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
        actualizarImporte(row);
        calcularDiferencia($('#tablaAgregarArticulos tbody tr:last-child'));
        calcularIVA();
        actualizarSuma();
        mostrarTotalVenta();
        mostrarSubTotal();
        mostrarIvaTotal();
        
      } else {
       
        var tr = '';
        var btnEliminar = '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button>';
      

        var inputId = '<input type="hidden" name="detIdModal[' + articulo.id + ']" value="' + articulo.id + '" />';
        var inputCantidad = '<input class="form-control" type="hidden" name="detCantidadModal[' + articulo.id + ']" value="' + articulo.cantidad + '" />';

        tr += '<tr data-id="' + articulo.id + '">';
        tr += '<td class="codigo"><input class="form-control codigo-barras-input" id="codBarrasInput" style="font-size: 0.75rem !important;" type="text" value="' + articulo.codigo + '" name="CodBarras[]" /></td>';
        tr += '<td class="descripcion"><textarea class="form-control descripcion-producto-input" id="descripcionproducto"name="NombreDelProducto[]" style="font-size: 0.75rem !important;">' + articulo.descripcion + '</textarea></td>';
        tr += '<td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem !important;" type="number" name="CantidadVendida[]" value="' + articulo.cantidad + '" /></td>';
tr += '<td class="ExistenciasEnBd"><input class="form-control cantidad-existencias-input" style="font-size: 0.75rem !important;" type="number" name="StockActual[]" value="' + articulo.existencia + '" /></td>';
tr += '<td class="Diferenciaresultante"><input class="form-control cantidad-diferencia-input" style="font-size: 0.75rem !important;" type="number" name="Diferencia[]" /></td>';

        tr += '<td class="preciofijo"><input class="form-control preciou-input" style="font-size: 0.75rem !important;" type="number"  value="' + articulo.precio + '"  /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="precio"><input hidden id="precio_' + articulo.id + '"class="form-control precio" style="font-size: 0.75rem !important;" type="number" name="PrecioVentaProd[]" value="' + articulo.precio + '" onchange="actualizarImporte($(this).parent().parent());" /></td>';
        tr += '<td><input id="importe_' + articulo.id + '" class="form-control importe" name="ImporteGenerado[]"style="font-size: 0.75rem !important;" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="importe_siniva_' + articulo.id + '" class="form-control importe_siniva" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="valordelniva_' + articulo.id + '" class="form-control valordelniva" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"><input id="ieps_' + articulo.id + '" class="form-control ieps" type="number" readonly /></td>';
        tr += '<td style="visibility:collapse; display:none;"class="idbd"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="' + articulo.id + '" name="IdBasedatos[]" /></td>';
        tr += '<td style="visibility:collapse; display:none;" class="lote"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="' + articulo.lote + '" name="LoteDelProducto[]" /></td>';
        tr += '<td  style="visibility:collapse; display:none;"  class="claveess"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="' + articulo.clave + '" name="ClaveAdicional[]" /></td>';
        tr += '<td  style="visibility:collapse; display:none;" class="tiposservicios"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="' + articulo.tipo + '" name="TiposDeServicio[]" /></td>';

        tr += '<td  style="visibility:collapse; display:none;" class="ResponsableInventario"> <input hidden id="VendedorFarma" type="text" class="form-control " name="AgregoElVendedor[]"readonly value="<?php echo $row['Nombre_Apellidos'] ?>">   </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="Sucursal"> <input hidden type="text" class="form-control " name="SucursalEnVenta[]"readonly value="<?php echo $row['Fk_Sucursal'] ?>">   </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="Empresa"> <input hidden type="text" class="form-control " name="Empresa[]"readonly value="Saluda">  </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="Fecha"> <input hidden type="text" class="form-control " name="FechaDeVenta[]"readonly value="<? echo $fechaActual;?>"  </td>';
        tr += '<td   style="visibility:collapse; display:none;" class="FormaPago"> <input hidden type="text" class="form-control forma-pago-input" id="FormaPagoCliente" name="FormaDePago[]" value="Efectivo"> </td>';
        
        tr += '<td  style="visibility:collapse; display:none;" class="Descuentosugerido"> <input hidden type="text" class="form-control descuento-aplicado" id="descuentoaplicado_' + articulo.id + '" name="DescuentoAplicado[]" readonly > </td>';
        tr += '<td><div class="btn-container">' + btnEliminar + '</div><div class="input-container"></td>';
      

        tr += '</tr>';

        $('#tablaAgregarArticulos tbody').append(tr);
        actualizarImporte($('#tablaAgregarArticulos tbody tr:last-child'));
        calcularDiferencia($('#tablaAgregarArticulos tbody tr:last-child'));
        calcularIVA();
        actualizarSuma();
        mostrarTotalVenta();
        mostrarSubTotal();
        mostrarIvaTotal();
       
      }
    }

    $('#codigoEscaneado').val('');
    $('#codigoEscaneado').focus();
  }

  






  
  function actualizarImporte(row) {
  var cantidad = parseInt(row.find('.cantidad-vendida-input').val());
  var precio = parseFloat(row.find('.precio input').val());

  

  if (cantidad < 0) {
    mostrarMensaje('La cantidad no puede ser negativa');
    return;
  }

  if (precio < 0) {
    mostrarMensaje('El precio no puede ser negativo');
    return;
  }

  var importe = cantidad * precio;
  var iva = importe / 1.16 * 0.16;
  var importeSinIVA = importe - iva;
  var ieps = importe * 0.08;

  row.find('input.importe').val(importe.toFixed(2));
  row.find('input.importe_siniva').val(importeSinIVA.toFixed(2));
  row.find('input.valordelniva').val(iva.toFixed(2));
  row.find('input.ieps').val(ieps.toFixed(2));

  // Llamar a la función para recalcular la suma de importes
  actualizarSuma();
  mostrarTotalVenta();
  mostrarSubTotal();
  mostrarIvaTotal();
}



  // Función para calcular el IVA
  function calcularIVA() {
    totalIVA = 0;

    $('#tablaAgregarArticulos tbody tr').each(function() {
      var iva = parseFloat($(this).find('.valordelniva input').val());
      totalIVA += iva;
    });

    $('#totalIVA').text(totalIVA.toFixed(2));
  }

  // Función para actualizar la suma de importe sin IVA, IEPS y diferencia de IVA
  function actualizarSuma() {
    var sumaImporteSinIVA = 0;
    var totalIEPS = 0;

    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.importe_siniva input').val());
      sumaImporteSinIVA += importeSinIVA;

      var ieps = parseFloat($(this).find('.ieps input').val());
      totalIEPS += ieps;
    });

    $('#sumaImporteSinIVA').text(sumaImporteSinIVA.toFixed(2));
    $('#totalIEPS').text(totalIEPS.toFixed(2));
  }

  // Función para mostrar un mensaje
  function mostrarMensaje(mensaje) {
    // Mostrar el mensaje en una ventana emergente de alerta
    alert(mensaje);
  }
// Modificar la función eliminarFila() para llamar a las funciones necesarias después de eliminar la fila
function eliminarFila(element) {
  var fila = $(element).closest('tr'); // Obtener la fila más cercana al elemento
  fila.remove(); // Eliminar la fila

  // Llamar a las funciones necesarias después de eliminar la fila
  calcularIVA();
  actualizarSuma();
  mostrarTotalVenta();
  mostrarSubTotal();
  mostrarIvaTotal();
}


</script>




<script>
            document.addEventListener("DOMContentLoaded", function(){
                // Invocamos cada 5 segundos ;)
                const milisegundos = 600 *1000;
                setInterval(function(){
                    // No esperamos la respuesta de la petición porque no nos importa
                    fetch("./Refrescacontenido.php");
                },milisegundos);
            });
        </script>

<!-- Control Sidebar -->

<!-- Main Footer -->
<?php

include("Modales/Error.php");


include("footer.php") ?>


  <!-- ./wrapper -->



  <!-- Bootstrap -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>

  <!-- OPTIONAL SCRIPTS -->
  <script src="dist/js/demo.js"></script>

  <!-- PAGE PLUGINS -->

  </body>

</html>


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

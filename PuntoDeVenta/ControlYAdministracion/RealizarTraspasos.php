<?php
include_once "Controladores/ControladorUsuario.php";
include "Controladores/SumadeFolioTicketsNuevo.php";
include("Controladores/db_connect.php");
$primeras_tres_letras = substr($row['Nombre_Sucursal'], 0, 3);


// Concatenar las primeras 3 letras con el valor de $totalmonto
$resultado_concatenado = $primeras_tres_letras . $totalmonto;

// Convertir el resultado a mayúsculas
$resultado_en_mayusculas = strtoupper($resultado_concatenado);

// Imprimir el resultado en mayúsculas



?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Realizar traspasos de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    
        <!-- Spinner End -->
        <?php
$fechaActual = date('Y-m-d H:i:s');

?>

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

<script>
    function establecerFechaActual() {
        const hoy = new Date();
        return hoy.toISOString().split('T')[0]; // Formato YYYY-MM-DD
    }

    // Función para actualizar todos los campos de fecha dinámicamente
    function actualizarFechas() {
        // Buscar todos los inputs dinámicos con la clase 'fecha-dinamica'
        document.querySelectorAll('input.fecha-dinamica').forEach(input => {
            if (!input.value) { // Solo establecer si está vacío
                input.value = establecerFechaActual();
            }
        });

        // Sincronizar los campos ocultos con los valores seleccionados
        document.querySelectorAll('input[name="FechaDeTraspaso[]"]').forEach(inputOculto => {
            inputOculto.value = document.getElementById('fecha-apertura').value || establecerFechaActual();
        });
    }

    // Ejecutar la función al cargar la página
    document.addEventListener('DOMContentLoaded', actualizarFechas);

    // Escuchar eventos en el documento para manejar inputs dinámicos
    document.addEventListener('change', (event) => {
        if (event.target && event.target.id === 'fecha-apertura') {
            document.querySelectorAll('input[name="FechaDeTraspaso[]"]').forEach(inputOculto => {
                inputOculto.value = event.target.value;
            });
        }
    });

    // Observador de cambios en el DOM para detectar elementos agregados dinámicamente
    const observer = new MutationObserver(actualizarFechas);
    observer.observe(document.body, { childList: true, subtree: true });
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


  <div class="container-fluid">

    <div class="row mb-3">

      <div class="col-md-9">

      <div class="card card-gray shadow" style="width: 103%">

          <div class="card-body p-3">


            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true" style="font-weight: 600;">Realizar Traspasos</a>
              </li>
              
              
            </ul>
            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="row">
                  <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                  <div class="col-md-12 mb-3">

                    <div class="form-group mb-2">
                      <div class="row">
                        

                        

                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Realizado por</label>
                          <div class="input-group mb-3">
                            
                          <input  type="text" class="form-control " readonly value="<?php echo $row['Nombre_Apellidos'] ?>">

                          </div>
                        </div>
                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Sucursal origen</label>
                          <div class="input-group mb-3">
                        
                            <input type="text" class="form-control "  style="font-size: 0.75rem !important;" value="<?php echo $row['Nombre_Sucursal'] ?>" readonly>

                          </div>
                        </div>
                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;"># de ticket</label>
                          <div class="input-group mb-3">
                        
                            <input type="text" class="form-control "  style="font-size: 0.75rem !important;" value="<?php echo $resultado_en_mayusculas; ?>" readonly>

                          </div>
                        </div>

                        <div class="col">

<label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Fecha de traspaso</label>
<div class="input-group mb-3">

<input type="date" class="form-control" style="font-size: 0.75rem !important;" id="fecha-apertura" >
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener el input base
        const fechaBase = document.getElementById('fecha-apertura');
        
        // Obtener la fecha actual en formato YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];

        // Establecer la fecha actual como valor por defecto en el input base
        fechaBase.value = today;

        // Función para actualizar todos los inputs dinámicos
        function actualizarFechasDinamicas(valor) {
            const inputsDinamicos = document.querySelectorAll('.fecha-dinamica');
            inputsDinamicos.forEach(input => {
                input.value = valor;
            });
        }

        // Al cargar la página, establecer la fecha actual en los inputs dinámicos
        actualizarFechasDinamicas(today);

        // Escuchar cambios en el input base y actualizar los dinámicos
        fechaBase.addEventListener('change', function () {
            actualizarFechasDinamicas(this.value);
        });
    });
</script>
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
                  <div class="col-md-7 mb-3 rounded-3" style="background-color:#26b814;color: white;text-align:center;border:1px solid #26b814;">
                    <h2 class="fw-bold m-0" style="color:white;" >MXN <span class="fw-bold" id="totalVenta"></span></h2>
                  </div>

                  <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->
                

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
                      <form action="javascript:void(0)" target="print_popup" method="post" id="TraspasosNotasALMomento">
                        <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                          <thead>
                            <tr>
                              <th>Codigo</th>
                              <th style="width:30%">Producto</th>
                              <th style="width:6%">Cantidad</th>
                              <th>Precio</th>
                              <th>Importe</th>
                              <th>Tipo de mov</th>
                              <th>Fecha</th>
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
      <div class="col-md-3">

<div class="card card-gray shadow">

  <!-- <h5 class="card-header py-1 bg-primary text-white text-center">
            Total Venta: MXN <span id="totalVentaRegistrar">0.00</span>
        </h5> -->

  <div class="card-body p-2">

    <!-- SELECCIONAR TIPO DE DOCUMENTO -->

    <!-- SELECCIONAR TIPO DE PAGO -->
    <div class="form-group mb-2">

      <label class="col-form-label p-0" for="selCategoriaReg">

        <span class="small">Tipo de movimiento</span><span class="text-danger">*</span>
      </label>
      <div class="input-group mb-3">
        
        <select class="form-control form-select form-select-sm" aria-label=".form-select-sm example" id="selTipoPago" required >
<option value="0">Seleccione el Tipo de movimiento</option>
<option value="Traspaso">Traspaso</option>
<option value="Nota de credito">Nota de credito</option>

</select>
      </div>

<script>
  let selectedAdjustmentTipoMov = "";

// Evento para capturar el valor seleccionado del select
document.getElementById('selTipoPago').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    selectedAdjustmentTipoMov = selectedOption.value; // Obtiene el value

    // Actualiza el input correspondiente en la tabla con el value
    document.querySelectorAll('.tipo-movimiento').forEach(input => {
        input.value = selectedAdjustmentTipoMov;
    });

   
});
</script>
     
      

<div class="alert alert-danger" id="avisaselTipoPago" role="alert" style="display:none;">
¡Debes elegir una forma de pago!
</div>
</div>
</div>

      <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
        Debe Ingresar tipo de pago
      </span>

    </div>

    <!-- SERIE Y NRO DE BOLETA -->


    <div class="form-group mb-2" id="divCliente">
    <label for="clienteSelect" style="font-size: 0.75rem !important;">Sucursal destino</label>
    <div class="input-group mb-3">
        <select class="form-control form-select form-select-sm" id="sucursaldestinoelegida" >
            <option value="0">Seleccione una sucursal</option>
        </select>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Obtener el select específico para sucursales dinámicas
    const selectSucursal = document.getElementById('sucursaldestinoelegida');

    // Realizar la solicitud para obtener las sucursales
    fetch('Controladores/obtenerSucursales.php')
        .then(response => response.json())
        .then(data => {
            // Llenar solo el select para sucursales dinámicas
            if (selectSucursal) {
                data.forEach(sucursal => {
                    const option = document.createElement('option');
                    option.value = sucursal.ID_Sucursal;
                    option.textContent = sucursal.Nombre_Sucursal;
                    selectSucursal.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error al obtener las sucursales:', error);
        });
});

let selectedAdjustment = "";
let selectedText = "";

// Evento para capturar el valor seleccionado del select
document.getElementById('sucursaldestinoelegida').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    selectedAdjustment = selectedOption.value; // Obtiene el value
    selectedText = selectedOption.textContent; // Obtiene el texto visible

    // Actualiza el input correspondiente en la tabla con el value
    document.querySelectorAll('.tipoajuste-input').forEach(input => {
        input.value = selectedAdjustment;
    });

    // Actualiza el input correspondiente en la tabla con el texto
    document.querySelectorAll('.tipoajusteletras-input').forEach(input => {
        input.value = selectedText;
    });
});
</script>

<!-- MOSTRAR MONTO EFECTIVO ENTREGADO Y EL VUELTO -->
<!-- MOSTRAR MONTO EFECTIVO ENTREGADO Y EL VUELTO -->
<div class="row mt-2">



</div>

<!-- MOSTRAR EL SUBTOTAL, IGV Y TOTAL DE LA VENTA -->
<div class="row fw-bold">
<div class="col-md-5">
<span>TOTAL</span>
</div>
<div class="col-md-7 text-end">
<span class="" id="boleta_total" name="TotalGenerado" style="float: right;">MXN $0.00</span>
<input type="text" class="form-control"  value="" hidden  name="TotalVenta"  id="totaldeventacliente">
</div>
<div class="col-12 text-end">
<!-- <h6 class="text-danger fw-bold">Cambio:  <span id="Vuelto" style="float: right;">MXN $0.00</span></h6>
<input type="text" class="form-control"  value="" name="CambioDelCliente[]" hidden id="cambiorecibidocliente"> -->
</div>
</div>

      <div class="col-md-12 text-center">
        <div class="my-3">
          <button class="btn btn-primary btn-sm" id="btnIniciarVenta">
          <i class="fa-solid fa-right-left"></i> Realizar Traspaso
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</form>
</div>
</div>
</div>
</div>
<script>





function actualizarSumaTotal() {
  var totalVenta = parseFloat(document.getElementById("totalVenta").textContent); // Total de la venta
  var metodoPago = document.getElementById("selTipoPago").value; // Método de pago seleccionado
  var iptTarjeta = parseFloat(document.getElementById("iptTarjeta").value) || 0; // Pago con tarjeta (por defecto 0)
  var iptEfectivo = parseFloat(document.getElementById("iptEfectivoRecibido").value) || 0; // Pago con efectivo (por defecto 0)
  var totalCubierto = 0; // Inicializamos el total cubierto
  var cambio = 0; // Inicializamos el cambio
  switch (metodoPago) {
  case "Credito":
    iptEfectivo = totalVenta;
    document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
    $('#iptEfectivoRecibido').trigger('input');
  
    totalCubierto = iptEfectivo;
    cambio = 0;
    break;

  case "Efectivo y Tarjeta":
    if (iptTarjeta >= totalVenta) {
      iptEfectivo = 0;
    } else {
      iptEfectivo = totalVenta - iptTarjeta;
    }
    document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
    $('#iptEfectivoRecibido').trigger('input');
    totalCubierto = iptTarjeta + iptEfectivo;
    cambio = iptEfectivo - (totalVenta - iptTarjeta);
    cambio = cambio > 0 ? cambio : 0;
    break;

    case "Efectivo Y Credito":
   
    if (iptTarjeta >= totalVenta) {
      iptEfectivo = 0;
    } else {
      iptEfectivo = totalVenta - iptTarjeta;
    }
    document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
    $('#iptEfectivoRecibido').trigger('input');
    totalCubierto = iptTarjeta + iptEfectivo;
    cambio = iptEfectivo - (totalVenta - iptTarjeta);
    cambio = cambio > 0 ? cambio : 0;
    break;

}

  // Actualizar el cambio en el elemento <span>
  document.getElementById("Vuelto").textContent = cambio.toFixed(2);

  // Actualizar el total que se muestra al cliente
  if (metodoPago === "Efectivo y Tarjeta" || metodoPago === "Efectivo Y Credito") {
    document.getElementById("totaldeventacliente").value = iptEfectivo.toFixed(2);
  } else {
    document.getElementById("totaldeventacliente").value = totalVenta.toFixed(2);
  }
}

// Detectar cambios en el método de pago
document.getElementById("selTipoPago").addEventListener("change", actualizarSumaTotal);

// Detectar cambios en los campos de tarjeta y efectivo
document.getElementById("iptTarjeta").addEventListener("input", actualizarSumaTotal);
document.getElementById("iptEfectivoRecibido").addEventListener("input", actualizarSumaTotal);







</script>


<script>
 $(document).ready(function() {
  

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
  $(function() {
    $("#clienteInput").autocomplete({
      source: function(request, response) {
        $.ajax({
          url: "Controladores/clientes.php",
          dataType: "json",
          data: {
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      },
      minLength: 0
    });
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
      {
        "data": "descuento2"
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
  function mostrarToast(mensaje) {
  var toast = $('<div class="toast"></div>').text(mensaje);
  $('body').append(toast);
  toast.fadeIn(400).delay(3000).fadeOut(400, function() {
    $(this).remove();
  });
}
  function buscarArticulo(codigoEscaneado) {
  var formData = new FormData();
  formData.append('codigoEscaneado', codigoEscaneado);

  $.ajax({
    url: "Controladores/escaner_articulo_administrativo.php",
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (data) {
      if ($.isEmptyObject(data)) {
        // Manejar caso de no resultados
      } else if (data.codigo || data.descripcion) {
        agregarArticulo(data);
        if (data.esAntibiotico) {
          Swal.fire({
    title: '¡Atención!',
    text: 'El artículo escaneado es de tipo "ANTIBIOTICO". Por favor, recuerde solicitar la receta médica al paciente antes de proceder con la venta.',
    icon: 'warning',
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#3085d6',
    background: '#fff3cd', // Color de fondo amarillo claro para llamar la atención
    customClass: {
        title: 'swal-title',
        content: 'swal-content'
    },
    backdrop: `
        rgba(0,0,123,0.4)
        url("https://via.placeholder.com/150") // Puedes añadir una imagen o dejarlo en blanco
        center center
        no-repeat
    `
});

        }
      }

      limpiarCampo();
    },
    error: function (data) {
      msjError('Error en la búsqueda');
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
      url: 'Controladores/autocompletadoAdministrativo.php',
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


  var tablaArticulos = ''; // Variable para almacenar el contenido de la tabla



  // Variable para almacenar el total del IVA
  var totalIVA = 0;
  function agregarArticulo(articulo) {
  if (!articulo || (!articulo.id && !articulo.descripcion)) {
    mostrarMensaje('El artículo no es válido');
    return;
  }

  let row = articulo.id
    ? $('#tablaAgregarArticulos tbody').find('tr[data-id="' + articulo.id + '"]')
    : null;

  if (!row || !row.length) {
    $('#tablaAgregarArticulos tbody tr').each(function () {
      if ($(this).find('.descripcion-producto-input').val() === articulo.descripcion) {
        row = $(this);
        return false;
      }
    });
  }

  if (row && row.length) {
    let cantidadActual = parseInt(row.find('.cantidad input').val());
    let nuevaCantidad = cantidadActual + parseInt(articulo.cantidad);

    if (nuevaCantidad < 0) {
      mostrarMensaje('La cantidad no puede ser negativa');
      return;
    }

    row.find('.cantidad input').val(nuevaCantidad);
    mostrarToast('Cantidad actualizada para el producto: ' + articulo.descripcion);
    actualizarImporte(row);
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
    mostrarSubTotal();
    mostrarIvaTotal();
  } else {
    const btnEliminar = '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button>';

    const tr = `
      <tr data-id="${articulo.id}">
        <td class="codigo"><input class="form-control codigo-barras-input" style="font-size: 0.75rem;" type="text" value="${articulo.codigo || ''}" name="CodBarras[]" /></td>
        <td class="descripcion"><textarea class="form-control descripcion-producto-input" name="NombreDelProducto[]" style="font-size: 0.75rem;">${articulo.descripcion}</textarea></td>
        <td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem;" type="number" name="Cantidad[]" value="${articulo.cantidad}" onchange="actualizarImporte($(this).parent().parent());" /></td>
        <td class="preciofijo"><input class="form-control preciou-input" style="font-size: 0.75rem;" type="number" value="${articulo.precio}" /></td>
        <td style="display:none;" class="precio"><input hidden id="precio_${articulo.id}" class="form-control precio" style="font-size: 0.75rem;" type="number" name="Pc[]" value="${articulo.precio}" onchange="actualizarImporte($(this).parent().parent());" /></td>
        <td><input id="importe_${articulo.id}" class="form-control importe" name="ImporteGenerado[]" style="font-size: 0.75rem;" type="number" readonly /></td>
        <td style="display:none;"><input id="importe_siniva_${articulo.id}" class="form-control importe_siniva" type="number" readonly /></td>
        <td style="display:none;"><input id="valordelniva_${articulo.id}" class="form-control valordelniva" type="number" readonly /></td>
        <td style="display:none;"><input id="ieps_${articulo.id}" class="form-control ieps" type="number" readonly /></td>
        <td style="display:none;" class="idbd"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.id}" name="IdBasedatos[]" /></td>
        <td style="display:none;" class="lote"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.lote}" name="LoteDelProducto[]" /></td>
        <td style="display:none;" class="claveess"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.clave}" name="ClaveAdicional[]" /></td>
        <td style="display:none;" class="tiposservicios"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.tipo}" name="Tipo[]" /></td>
        <td style="display:none;" class="NumeroTicket"><input type="text" class="form-control" hidden id="Folio_Ticket" name="Folio_Ticket[]" value="<?php echo $resultado_en_mayusculas; ?>" readonly /></td>
        <td style="display:none;" class="Vendedor"><input hidden id="VendedorFarma" type="text" class="form-control" name="AgregadoPor[]" readonly value="<?php echo $row['Nombre_Apellidos'] ?>" /></td>
        <td class="TipoMovimiento"><input type="text" class="form-control tipo-movimiento" name="TipoDeMov[]" readonly value="" /></td>
        <td style="display:none;" class="Sucursal"><input hidden type="text" class="form-control" name="Fk_sucursal[]" readonly value="<?php echo $row['Fk_Sucursal'] ?>" /></td>
        <td style="display:none;" class="SucursalDestino"><input type="text" class="form-control tipoajuste-input" id="inputDestino" name="Fk_SucursalDestino[]" readonly /></td>
        <td style="display:none;" class="SucursalDestinoLetras"><input type="text" class="form-control tipoajusteletras-input" id="inputDestino" name="Fk_SucursalDestinoLetras[]" readonly /></td>
           <td style="display:none;" class="SucursalOrigenLetras"><input type="text" class="form-control destino-input" id="inputDestinoLetras" value="<?php echo $row['Nombre_Sucursal'] ?>" name="Fk_SucursalOrigenLetras[]" readonly /></td>
        <td style="display:none;" class="Sistema"><input hidden type="text" class="form-control" name="Sistema[]" readonly value="POSVENTAS" /></td>
        <td style="display:none;" class="Liquidado"><input hidden type="text" class="form-control" name="Liquidado[]" readonly value="N/A" /></td>
        <td style="display:none;" class="Estatus"><input hidden type="text" class="form-control" name="Estatus[]" readonly value="Generado" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="ID_H_O_D[]" readonly value="Doctor Pez" /></td>
        <td class="Fecha"> <input type="date" class="form-control fecha-dinamica" name="FechaVenta[]" id="fecha-apertura2-${articulo.id}" readonly value="" /></td>
        <td style="display:none;" class="FormaPago"><input hidden type="text" class="form-control forma-pago-input" id="FormaPagoCliente" name="FormaDePago[]" value="Efectivo" /></td>
        <td><div class="btn-container">${btnEliminar}</div></td>
      </tr>`;

    $('#tablaAgregarArticulos tbody').append(tr);

    const newRow = $('#tablaAgregarArticulos tbody tr:last-child');
    actualizarImporte(newRow);
    newRow.find('.tipoajuste-input').val(selectedAdjustment);
    newRow.find('.tipoajusteletras-input').val(selectedText);
    newRow.find('.tipo-movimiento').val(selectedAdjustmentTipoMov);
   
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
  }

  limpiarCampo();
  $('#codigoEscaneado').focus();
}



$('#fecha-apertura').on('change', function() {
    const nuevaFecha = $(this).val();
    $('input[name="FechaDeTraspaso[]"]').each(function() {
        $(this).val(nuevaFecha);
    });
});



  
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
/// INICIO CODIGO DESCUENTOS///
// Variable para rastrear si el descuento ya fue aplicado
var descuentoAplicado = false;

function aplicarDescuento() {
  // Si el descuento ya fue aplicado, no se hace nada
  if (descuentoAplicado) {
    return;
  }
}
  var importeInput = $('#importe'); // Asegúrate de que 'importe' sea el ID correcto de tu campo de importe
  var importeInput2 = $('#precio'); // Asegúrate de que 'importe' sea el ID correcto de tu campo de importe
function abrirSweetAlert(elemento) {
  // Obtener la fila correspondiente
  var fila = $(elemento).closest('tr');

  // Obtener el campo importe
  var importeInput = fila.find('.importe');
  var importeInput2 = fila.find('.precio');
  // $(elemento).hide();

  // Mostrar el SweetAlert para ingresar el descuento
  var swalInstance = Swal.fire({
    title: 'Seleccionar descuento',
    html: '<label for="customDescuento">Ingresar monto a descontar:</label>' +
      '<input type="number" class="form-control" id="customDescuento" min="0" placeholder="Ingrese monto a descontar">' +
      '<br>' +
      '<label for="porcentajeDescuento">Seleccionar porcentaje de descuento:</label>' +
      '<select class="form-control" id="porcentajeDescuento">' +
      '<option value="">Seleccionar</option>' +
      '<option value="0">0%</option>' +
      '<option value="5">5%</option>' +
        '<option value="10">10%</option>' +
      '<option value="15">15%</option>' +
      '<option value="20">20%</option>' +
      '<option value="25">25%</option>' +
      '<option value="30">30%</option>' +
      '<option value="35">35%</option>' +
      '<option value="40">40%</option>' +
      '<option value="45">45%</option>' +
      '<option value="50">50%</option>' +
      '<option value="55">55%</option>' +
      '<option value="60">60%</option>' +
         '<option value="65">65%</option>' +
         '<option value="70">70%</option>' +
         '<option value="75">75%</option>' +
         '<option value="80">80%</option>' +
         '<option value="85">85%</option>' +
         '<option value="90">90%</option>' +
         '<option value="95">95%</option>' +
         '<option value="100">100%</option>' +
      // Resto de opciones del select
      '</select>',
    showCancelButton: true,
    confirmButtonText: 'Aplicar descuento',
    cancelButtonText: 'Cancelar',
    showLoaderOnConfirm: true,
    preConfirm: () => {
      // Una vez que se aplique el descuento, marcar como descuento aplicado
      descuentoAplicado = true;
      // Deshabilitar ambos campos select e input
        $('#customDescuento').prop('disabled', true);
        $('#porcentajeDescuento').prop('disabled', true);
      // Obtener el valor del monto a descontar
      var montoDescontar = parseFloat($('#customDescuento').val());

      // Obtener el valor del porcentaje de descuento seleccionado
      var porcentajeDescuento = parseFloat($('#porcentajeDescuento').val());

      // Validar y calcular el importe con descuento
      if (!isNaN(porcentajeDescuento) && porcentajeDescuento >= 0 && porcentajeDescuento <= 100) {
        // Obtener el valor del importe actual
        var importeActual = parseFloat(importeInput.val());

        // Calcular el importe con descuento por porcentaje
        var importeDescuento = importeActual * (1 - porcentajeDescuento / 100);

        // Actualizar el valor del importe
        importeInput.val(importeDescuento.toFixed(2));
        importeInput2.val(importeDescuento.toFixed(2));
        // Deshabilitar el input de monto a descontar
        $('#customDescuento').prop('disabled', true);

        // Mostrar mensaje del tipo de descuento y monto aplicado
        var tipoDescuento = porcentajeDescuento + '% de descuento';
        var montoAplicado = (importeActual - importeDescuento).toFixed(2);
        fila.find('.descuento-aplicado').val(montoAplicado); // Aquí puedes ajustar cómo mostrar el valor
      } else if (!isNaN(montoDescontar) && montoDescontar >= 0) {
        // Obtener el valor del importe actual
        var importeActual = parseFloat(importeInput.val());

        // Restar el monto al importe actual
        var importeNuevo = importeActual - montoDescontar;

        // Actualizar el valor del importe
        importeInput.val(importeNuevo.toFixed(2));
        importeInput2.val(importeNuevo.toFixed(2));
        // Deshabilitar el select de porcentaje de descuento
        $('#porcentajeDescuento').prop('disabled', true);

        // Mostrar mensaje del tipo de descuento y monto aplicado
        var tipoDescuento = 'Descuento de monto';
        var montoAplicado = montoDescontar.toFixed(2);
        fila.find('.descuento-aplicado').val(montoAplicado); // Aquí puedes ajustar cómo mostrar el valor
      }

      Swal.fire({
        title: 'Descuento aplicado',
        html: `$${montoAplicado}`,
        icon: 'success'
      });

      // Restablecer los eventos "change" cuando se cierre el SweetAlert
      // swalInstance.then(() => {
      //   $('#customDescuento').off('change');
      //   $('#porcentajeDescuento').off('change');

      //   // Mostrar el botón cuando se presione "Cancelar"
        
      // });

      actualizarSuma();
      mostrarTotalVenta();
      mostrarSubTotal();
      mostrarIvaTotal();
      actualizarImporte();
    },
    allowOutsideClick: () => !Swal.isLoading()
  });

  // Agregar el evento "change" al input de monto a descontar
  $('#customDescuento').on('change', function() {
    // Obtener el valor del monto a descontar
    var montoDescontar = parseFloat($(this).val());

    // Obtener el elemento select
    var porcentajeSelect = $('#porcentajeDescuento');

    // Si se seleccionó un monto a descontar, deshabilitar el select
    if (!isNaN(montoDescontar) && montoDescontar >= 0) {
      porcentajeSelect.prop('disabled', true);
    } else {
      porcentajeSelect.prop('disabled', false);
    }
  });

  // Agregar el evento "change" al select de porcentaje de descuento
  $('#porcentajeDescuento').on('change', function() {
    // Obtener el valor del porcentaje de descuento seleccionado
    var porcentajeDescuento = parseFloat($(this).val());

    // Obtener el input de monto a descontar
    var montoInput = $('#customDescuento');

    // Si se seleccionó un porcentaje de descuento, deshabilitar el input de monto
    if (!isNaN(porcentajeDescuento) && porcentajeDescuento >= 0 && porcentajeDescuento <= 100) {
      montoInput.prop('disabled', true);
    } else {
      montoInput.prop('disabled', false);
    }
  });
}

// Agregar el evento click a un botón o enlace que abrirá el SweetAlert
$('#abrirSweetAlertBtn').on('click', function() {
  aplicarDescuento();
});

</script>





<!-- Control Sidebar -->

<!-- Main Footer -->
<?php

include("Modales/Error.php");
include("Modales/Exito.php");

include("Modales/AdvierteDeCaja.php");

include("Modales/ReimpresionTicketsVistaVentas.php");
include("Modales/ExitoActualiza.php"); ?>


  <!-- ./wrapper -->




  <script src="js/RealizaLosTraspasosONotass.js"></script>
  
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


            <?php 
            
            include "Footer.php";?>
</body>

</html>
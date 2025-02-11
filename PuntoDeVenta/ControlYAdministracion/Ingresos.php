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
    <title>Ingreso de medicamentos <?php echo $row['Licencia']?></title>
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
<style>
        .fish {
            animation: swim 10s linear infinite;
            width: 30%; /* Reducir el tamaño de la imagen al 50% */
            display: block; /* Asegurar que se comporte como un bloque */
            margin: 0 auto; /* Centrar la imagen */
        }
        .loader-container {
            text-align: center; /* Centrar el contenido */
        }
        .loaderPill-text {
            margin-top: 10px; /* Añadir espacio entre la imagen y el texto */
            color: #C80096;
        }
    </style>
    </style>
        <!-- Spinner End -->
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
  <style>
  /* Personalizar el diseño de la paginación con CSS */
  .dataTables_wrapper .dataTables_paginate {
    text-align: center !important; /* Centrar los botones de paginación */
    margin-top: 10px !important;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px !important;
    border: 1px solid #ef7980 !important;
    margin: 2px !important;
    cursor: pointer !important;
    font-size: 16px !important;
    color: #ef7980 !important;
    background-color: #fff !important;
  }

  /* Cambiar el color del paginado seleccionado */
  .dataTables_paginate .paginate_button.current {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  /* Cambiar el color del hover */
  .dataTables_paginate .paginate_button:hover {
    background-color: #C80096 !important;
    color: #fff !important;
    border-color: #C80096 !important;
  }
</style>

<style>
  /* Estilos personalizados para la tabla */
  #tablaAgregarArticulos th {
    font-size: 12px; /* Tamaño de letra para los encabezados */
    padding: 4px; /* Ajustar el espaciado entre los encabezados */
    white-space: nowrap; /* Evitar que los encabezados se dividan en varias líneas */
  }
</style>

<style>
  /* Estilos para la tabla */
  #tablaAgregarArticulos {
    font-size: 12px; /* Tamaño de letra para el contenido de la tabla */
    border-collapse: collapse; /* Colapsar los bordes de las celdas */
    width: 100%;
    text-align: center; /* Centrar el contenido de las celdas */
  }

  #tablaAgregarArticulos th {
    font-size: 16px; /* Tamaño de letra para los encabezados de la tabla */
    background-color: #ef7980 !important; /* Nuevo color de fondo para los encabezados */
    color: white; /* Cambiar el color del texto a blanco para contrastar */
    padding: 10px; /* Ajustar el espaciado de los encabezados */
  }

  #tablaAgregarArticulos td {
    font-size: 14px; /* Tamaño de letra para el contenido de la tabla */
    padding: 8px; /* Ajustar el espaciado de las celdas */
    border-bottom: 1px solid #ccc; /* Agregar una línea de separación entre las filas */
    color:#000000;
  }

  /* Estilos para el botón de Excel */
  .dt-buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
  }

  .dt-buttons button {
    font-size: 14px;
    margin: 0 5px;
    color: white; /* Cambiar el color del texto a blanco */
    background-color: #fff; /* Cambiar el color de fondo a blanco */
  }

 
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
        
// Function to show the instructions message
function showInstructions() {
            Swal.fire({
                title: 'Instrucciones',
                html: `
                    <ol style="text-align: left;">
                    <p>El campo escanear productos esta bloqueado, para desbloquearlo sigue las instrucciones<p/>
                        <li>Selecciona el <b>proveedor</b> del cual solicitas su ingreso</li>
                        <li>Coloca el <b>número de factura</b>.</li>
                        <li>Revisa que todos los datos sean correctos.</li>
                        <li>El campo escanear productos se desbloqueara automaticamente</li>
                        <li>Escanea tu producto, si es un encargo recuerda generar la solicitud de encargo</li>
                        <li>Recuerda llenar los campos fecha de caducidad,lote y precio maximo de venta</li>
                    </ol>
                    <div style="margin-top: 20px;">
                        <label>
                            <input type="checkbox" id="noMostrar"> No volver a mostrar esta información durante una semana
                        </label>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendido',
                customClass: {
                    container: 'animated fadeInDown'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (document.getElementById('noMostrar').checked) {
                        localStorage.setItem('noMostrarInstrucciones', Date.now());
                    }
                }
            });
        }
        // Mostrar SweetAlert2 de carga al iniciar la página
        Swal.fire({
            title: 'Cargando',
            html: '<div class="loader-container">' +
                    '<img src="https://doctorpez.mx/PuntoDeVenta/FotosMedidores/pez.gif" alt="Peces nadando" class="fish">' +
                    '<div class="loaderPill-text">' + getRandomMessage() + '</div>' +
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
           
             // Verificar si debe mostrarse el mensaje de instrucciones
             const noMostrarInstrucciones = localStorage.getItem('noMostrarInstrucciones');
            const unaSemana = 7 * 24 * 60 * 60 * 1000; // Milisegundos en una semana

            if (!noMostrarInstrucciones || (Date.now() - noMostrarInstrucciones) > unaSemana) {
                showInstructions();
            }
            
        });
     
        
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

<!-- Main content -->


<div class="container-fluid">

<div class="row mb-3">


<div class="card-body p-3">
<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#FiltroEspecifico" class="btn btn-default">
 Cambiar de sucursal <i class="fas fa-clinic-medical"></i>
</button>

            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="row">
                  <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                  <div class="col-md-12 mb-3">

                    <div class="form-group mb-2">
                    <!-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#FiltroEspecifico" class="btn btn-default">
 Cambiar de sucursal <i class="fas fa-clinic-medical"></i>
</button> -->
                      <div class="row">
                        <input hidden type="text" class="form-control " readonly value="<?php echo $row['Nombre_Apellidos'] ?>">

                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Proveedor</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
                            </div>
                            <select id="proveedoresSelect" class="form-control" style="font-size: 0.75rem !important;">
            <option value="">Proveedor</option>
        </select>
                
                           
                          </div>
                        </div>

                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Fecha</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
                            </div>
                            <input type="text" class="form-control " style="font-size: 0.75rem !important;" readonly value="<?php echo $fechaActual ?>">
                           
                          </div>
                        </div>
                        <div class="col">

<label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Factura</label>
<div class="input-group mb-3">
  <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
  </div>
  <input type="text" class="form-control " id="numerofactura" style="font-size: 0.75rem !important;" >
 
</div>
</div>
                      
<div class="col">

<label for="exampleFormControlInput1" style="font-size: 0.75rem !important;"># de solicitud
</label>
<div class="input-group mb-3">
  <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
  </div>
  <input type="text" class="form-control " style="font-size: 0.75rem !important;" readonly value="<?php echo  $totalmonto?>">
 
</div>
</div>            


                      </div>

                      <label class="col-form-label" for="iptCodigoVenta">
                        <i class="fas fa-barcode fs-6"></i>
                        <span class="small">Escanear productos</span>
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
                      <div class="text-center">
        <button type="submit" class="btn btn-primary">Enviar Información</button>
    </div>
                        <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                          <thead>
                            <tr>
                              <th>Codigo</th>
                              <th style="width:20%">Producto</th>
                              <th style="width:6%">Contabilizado</th>
                              <th >Fecha caducidad</th>
                              <th >Lote</th>
                              <th>Precio Maximo</th>
                              
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
            
              </form>

              

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
tr += '<td class="ExistenciasEnBd"><input class="form-control cantidad-existencias-input" style="font-size: 0.75rem !important;" type="date" name="FechaCaducidad[]" value="' + articulo.fechacaducidad + '" /></td>';
tr += '<td class="Diferenciaresultante"><input class="form-control cantidad-diferencia-input" style="font-size: 0.75rem !important;" type="text" name="Lote[]" /></td>';
tr += '<td class="Preciototal"><input class="form-control cantidad-diferencia-input" style="font-size: 0.75rem !important;" type="text" name="PrecioMaximo[]" /></td>';
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
        tr += '<td  style="visibility:collapse; display:none;" class="NumberSucursal"> <input hidden id="estatussolicitud" type="text" class="form-control " name="Estatusdesolicitud[]"readonly value="Pendiente"  </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="NumberSucursal"> <input hidden id="sucursal" type="text" class="form-control " name="FkSucursal[]"readonly value="<?php echo $row['Fk_Sucursal'] ?>">   </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="ResponsableInventario"> <input hidden id="VendedorFarma" type="text" class="form-control " name="AgregoElVendedor[]"readonly value="<?php echo $row['Nombre_Apellidos'] ?>">   </td>';
        tr += '<td  style="visibility:collapse; display:none;" class="Fecha"> <input hidden type="text" class="form-control " name="FechaDeInventario[]"readonly value="<?php echo $fechaActual;?>"  </td>';
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

<script src="js/RealizaIngreso.js"></script>

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
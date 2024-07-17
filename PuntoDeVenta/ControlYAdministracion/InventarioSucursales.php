<?php
include_once "Controladores/ControladorUsuario.php";
$fechaActual = date('Y-m-d'); // Esto obtiene la fecha actual en el formato 'Año-Mes-Día'
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Realizar Ventas de <?php echo $row['Licencia']?></title>
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






  <div class="container-fluid">

    <div class="row mb-3">

  

          <div class="card-body p-3">
            
            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="row">
                  <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                  <div class="col-md-12 mb-3">

                    <div class="form-group mb-2">
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#FiltroEspecifico" class="btn btn-default">
 Cambiar de sucursal <i class="fas fa-clinic-medical"></i>
</button>

<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#DescargarInventarios" class="btn btn-default">
 Descargar inventario <i class="fas fa-boxes"></i>
</button>


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

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Fecha</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
                            </div>
                            <input type="text" class="form-control " style="font-size: 0.75rem !important;" readonly value="<?php echo $fechaActual ?>">
                           
                          </div>
                        </div>
                        <div class="col">

                          <label for="exampleFormControlInput1" style="font-size: 0.75rem !important;">Tipo de ajuste</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"> <span class="input-group-text" id="Tarjeta2"><i class="fas fa-clock"></i></span>
                            </div>
                            <select class="form-control" style="font-size: 0.75rem !important;" id="Tipodeajuste">
                            <option value="">Seleccione un tipo de ajuste </option>
                  <option value="Ajuste de inventario">Ajuste de inventario</option>
              <option value="Inventario inicial">Inventario inicial</option>
                 <option value="Ajuste por daño">Ajuste por daño</option>
              <option value="Ajuste por caducidad">Ajuste por caducidad</option>
</select>


                          </div>
                          
                        </div>
                        <div class="col">
                <label for="alphabetSelect" style="font-size: 0.75rem !important;">Seleccione anaquel</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="background-color: #e83e8c !important;
    color: white;" id="Alphabet"><i class="fas fa-font"></i></span>
                    </div>
                    <select class="form-control" style="font-size: 0.75rem !important;" id="alphabetSelect"></select>
                </div>
            </div>

            <!-- Tercer Select: Números del 1 al 30 -->
            <div class="col">
                <label for="numberSelect" style="font-size: 0.75rem !important;">Seleccione repisa</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"  style="background-color: #e83e8c !important;
    color: white;" id="Number"><i class="fas fa-hashtag"></i></span>
                    </div>
                    <select class="form-control" style="font-size: 0.75rem !important;" id="numberSelect"></select>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para llenar el select con el abecedario
        function populateAlphabetSelect() {
            const alphabetSelect = document.getElementById('alphabetSelect');
            
            // Añadir la primera opción con valor vacío
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Seleccione anaquel';
            alphabetSelect.appendChild(defaultOption);
            
            // Llenar con el abecedario
            const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            alphabet.forEach(letter => {
                const option = document.createElement('option');
                option.value = letter;
                option.textContent = letter;
                alphabetSelect.appendChild(option);
            });
        }

        // Función para llenar el select con números del 1 al 30
        function populateNumberSelect() {
            const numberSelect = document.getElementById('numberSelect');
            
            // Añadir la primera opción con valor vacío
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Seleccione Repisa';
            numberSelect.appendChild(defaultOption);
            
            // Llenar con números del 1 al 30
            for (let i = 1; i <= 30; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                numberSelect.appendChild(option);
            }
        }

        // Llamar a las funciones para llenar los selects cuando la página se carga
        window.onload = function() {
            populateAlphabetSelect();
            populateNumberSelect();
        }
    </script>


                        
                       
  <script>
    document.getElementById('Tipodeajuste').addEventListener('change', function() {
        var selectElement = document.getElementById('Tipodeajuste');
        var alphabetSelect = document.getElementById('alphabetSelect');
        var numberSelect = document.getElementById('numberSelect');
        var inputElement = document.getElementById('codigoEscaneado');

        if (selectElement.value === "" || alphabetSelect.value === "" || numberSelect.value === "") {
            inputElement.disabled = true;
        } else {
            inputElement.disabled = false;
        }
    });

    document.getElementById('alphabetSelect').addEventListener('change', function() {
        var selectElement = document.getElementById('Tipodeajuste');
        var alphabetSelect = document.getElementById('alphabetSelect');
        var numberSelect = document.getElementById('numberSelect');
        var inputElement = document.getElementById('codigoEscaneado');

        if (selectElement.value === "" || alphabetSelect.value === "" || numberSelect.value === "") {
            inputElement.disabled = true;
        } else {
            inputElement.disabled = false;
        }
    });

    document.getElementById('numberSelect').addEventListener('change', function() {
        var selectElement = document.getElementById('Tipodeajuste');
        var alphabetSelect = document.getElementById('alphabetSelect');
        var numberSelect = document.getElementById('numberSelect');
        var inputElement = document.getElementById('codigoEscaneado');

        if (selectElement.value === "" || alphabetSelect.value === "" || numberSelect.value === "") {
            inputElement.disabled = true;
        } else {
            inputElement.disabled = false;
        }
    });


        // Inicializar el estado del input basado en el valor inicial del select
        document.addEventListener('DOMContentLoaded', function() {
            var selectElement = document.getElementById('Tipodeajuste');
            var inputElement = document.getElementById('codigoEscaneado');
            if (selectElement.value === "") {
                inputElement.disabled = true;
                // Mostrar alerta de SweetAlert2
                Swal.fire({
                    title: '¡Campo Bloqueado!',
                    html: '<p>El campo <b>"Código Escaneado"</b> está bloqueado.</p>' +
                          '<p>Para desbloquearlo, por favor seleccione un tipo de ajuste,el anaquel y la repisa con la que estaras realizando el conteo.</p>' +
                          '<p><b>Instrucciones:</b></p>' +
                          '<ul>' +
                          '<li>Haz clic en el menú desplegable "Tipo de ajuste".</li>' +
                          '<li>Selecciona una de las opciones disponibles.</li>' +
                          '<li>Haz clic en el menú desplegable "Anaquel".</li>' +
                          '<li>Selecciona una de las opciones disponibles.</li>' +
                          '<li>Haz clic en el menú desplegable "Repisa".</li>' +
                          '<li>Selecciona una de las opciones disponibles.</li>' +
                          '<li>El campo se desbloqueará automáticamente.</li>' +
                          '</ul>',
                    icon: 'info',
                    confirmButtonText: 'Entendido',
                    customClass: {
                        popup: 'animated tada' // Clase de animación opcional para llamar más la atención
                    }
                });
            } else {
                inputElement.disabled = false;
            }
        });
    </script>

                     <!-- Importa SweetAlert 2 -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    var fkSucursal = <?php echo $row['Fk_Sucursal'] ?>;
    var namedesucursal = "<?php echo addslashes($row['Nombre_Sucursal']); ?>"; // Escapa las comillas para nombres de sucursal

    document.getElementById("Tipodeajuste").addEventListener("change", function() {
        var selectedOption = this.value;
        if (selectedOption === "Inventario inicial") {
            // Aquí hacemos la llamada AJAX para verificar el estado del inventario inicial
            $.ajax({
                url: 'Consultas/VerificaEstadoInventario.php', // Nuevo archivo PHP que maneja la verificación
                method: 'POST',
                data: {
                    fkSucursal: fkSucursal // Incluye el valor PHP aquí
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if(res.success) {
                        Swal.fire({
                            title: 'Inventario ya establecido',
                            text: 'El inventario inicial ya ha sido establecido el ' + res.fecha_establecido,
                            icon: 'info'
                        });
                    } else {
                        Swal.fire({
                            title: '¿Estás seguro que deseas establecer en 0 las existencias de la sucursal ' + namedesucursal + '?',
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Sí, establecer stock en 0"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Aquí hacemos la llamada AJAX para actualizar la base de datos
                                $.ajax({
                                    url: 'Consultas/EstableceStock.php', // URL del archivo PHP que maneja la actualización
                                    method: 'POST',
                                    data: {
                                        tipoAjuste: selectedOption,
                                        fkSucursal: fkSucursal // Incluye el valor PHP aquí
                                    },
                                    success: function(response) {
                                        var res = JSON.parse(response);
                                        if(res.success) {
                                            Swal.fire({
                                                title: 'Éxito',
                                                text: 'El stock de la sucursal ' + namedesucursal + ' se ha ajustado a 0',
                                                icon: 'success'
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Error',
                                                text: res.message || 'Hubo un problema al establecer el stock en 0 de la sucursal ' + namedesucursal,
                                                icon: 'error'
                                            });
                                        }
                                    },
                                    error: function() {
                                        // Maneja los errores de la llamada AJAX
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'No se pudo conectar con el servidor. Inténtalo de nuevo más tarde.',
                                            icon: 'error'
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Acción cancelada',
                                    text: 'No se realizaron cambios.',
                                    icon: 'info'
                                });
                            }
                        });
                    }
                },
                error: function() {
                    // Maneja los errores de la llamada AJAX
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor. Inténtalo de nuevo más tarde.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});

</script>

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
                      
                      <form action="javascript:void(0)"  method="post" id="VentasAlmomento">
                      <div class="text-center">
        <button type="submit" class="btn btn-primary">Guardar datos</button>
    </div>
               
                        <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                          <thead>
                            <tr>
                              <th>Codigo</th>
                              <th style="width:20%">Producto</th>
                              <th style="width:5%">Contado</th>
                              <th style="width:3%">Actual</th>
                              <th style="width:5%">Diferencia</th>
                              <th style="width:5%">Precio</th>
                              <th>Tipo de ajuste</th>
                              <th style="width:3%">Anaquel</th>
                              <th style="width:3%">Repisa</th>
                              <!-- <th>Precio compra</th>
                              <th>Importe</th> -->
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


<script>
       let selectedAdjustment = "";

document.getElementById('Tipodeajuste').addEventListener('change', function() {
    selectedAdjustment = this.value;
});


let selectAnaquel = "";

document.getElementById('alphabetSelect').addEventListener('change', function() {
  selectAnaquel = this.value;
});

let selectRepisa = "";

document.getElementById('numberSelect').addEventListener('change', function() {
  selectRepisa = this.value;
});
    </script>
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
  $("#btnVaciarListado").click(function() {
    console.log("Click en el botón");
    $("#tablaAgregarArticulos tbody").empty();
    actualizarImporte($('#tablaAgregarArticulos tbody tr:last-child'));
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();

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
      {
        "data": "tipodeajuste"
      },
      {
        "data": "anaquel"
      },
      {
        "data": "repisa"
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
     
    ],

    "order": [
      [0, 'desc']

    ],
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    //para usar los botones   
    responsive: "false",

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



var Fk_sucursal = <?php echo json_encode($row['Fk_Sucursal']); ?>;
var scanBuffer = "";
var scanInterval = 5; // Milisegundos

function procesarBuffer() {
  // Buscar el carácter delimitador
  var delimiter = "\n";
  var delimiterIndex = scanBuffer.indexOf(delimiter);

  while (delimiterIndex !== -1) {
    // Extraer el código hasta el delimitador
    var codigoEscaneado = scanBuffer.slice(0, delimiterIndex);
    scanBuffer = scanBuffer.slice(delimiterIndex + 1);

    if (esCodigoBarrasValido(codigoEscaneado)) {
      buscarArticulo(codigoEscaneado);
    } else {
      console.warn('Código de barras inválido:', codigoEscaneado);
    }

    // Buscar el siguiente delimitador
    delimiterIndex = scanBuffer.indexOf(delimiter);
  }
}

function agregarEscaneo(escaneo) {
  scanBuffer += escaneo;
}

function esCodigoBarrasValido(codigoEscaneado) {
  // Verificar si el código de barras tiene una longitud válida
  var longitud = codigoEscaneado.length;
  return longitud >= 2 && longitud <= 13; // Ajusta el rango según sea necesario
}

function buscarArticulo(codigoEscaneado) {
  if (!codigoEscaneado.trim()) return; // No hacer nada si el código está vacío

  $.ajax({
    url: "Controladores/BusquedaPorEscaner.php",
    type: 'POST',
    data: { codigoEscaneado: codigoEscaneado },
    dataType: 'json',
    success: function (data) {
      if (!data || !data.codigo) {
        Swal.fire({
          icon: 'warning',
          title: 'No encontramos coincidencias',
          text: 'Al parecer el código no está asignado en la sucursal ¿deseas asignarlo?',
          showCancelButton: true,
          confirmButtonText: 'Agregar producto a la sucursal'
        }).then((result) => {
          if (result.isConfirmed) {
            agregarCodigoInexistente(codigoEscaneado, Fk_sucursal);
          }
        });
      } else {
        agregarArticulo(data);
        calcularDiferencia($('#tablaAgregarArticulos tbody tr:last-child'));
      }

      limpiarCampo();
    },
    error: function (data) {
      console.error('Error en la solicitud AJAX', data);
    }
  });
}


  function agregarCodigoInexistente(codigo, sucursal) {
    if (codigo.trim() === "" || sucursal.trim() === "") {
        return; // No hacer nada si el código o la sucursal están vacíos
    }
    // Enviar el código y la sucursal al backend para insertarlo en la tabla de la base de datos
    $.ajax({
        url: "https://saludapos.com/AdminPOS/Consultas/codigosinexistir.php",
        type: 'POST',
        data: { codigo: codigo, sucursal: sucursal },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Mostrar mensaje de éxito con SweetAlert2, incluyendo el nombre del producto
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    text: 'Producto "' + response.nombreProducto + '" agregado con éxito'
                }).then(() => {
                    // Ejecutar la función buscarArticulo con el código escaneado después de cerrar la alerta
                    buscarArticulo(codigo);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al agregar el producto: ' + response.message
                });
            }
        },
        error: function (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al agregar el producto'
            });
        }
    });
}


function limpiarCampo() {
  $('#codigoEscaneado').val('');
  $('#codigoEscaneado').focus();
}

var isScannerInput = false;

$('#codigoEscaneado').keyup(function (event) {
  if (event.which === 13) { // Verificar si la tecla presionada es "Enter"
    if (!isScannerInput) { // Verificar si el evento no viene del escáner
      var codigoEscaneado = $('#codigoEscaneado').val();
      agregarEscaneo(codigoEscaneado + '\n'); // Agregar el código escaneado al buffer de escaneo con un delimitador
      event.preventDefault(); // Evitar que el formulario se envíe al presionar "Enter"
    }
    isScannerInput = false; // Restablecer la bandera del escáner
  }
});

// Configurar un intervalo para procesar el buffer de escaneo a intervalos regulares
setInterval(procesarBuffer, scanInterval);
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

  function agregarArticulo(articulo) {
  if (!articulo || !articulo.id) {
    mostrarMensaje('El artículo no es válido');
    return;
  }

  let row = $('#tablaAgregarArticulos tbody').find('tr[data-id="' + articulo.id + '"]');
  if (row.length) {
    let cantidadActual = parseInt(row.find('.cantidad input').val());
    let nuevaCantidad = cantidadActual + parseInt(articulo.cantidad);
    if (nuevaCantidad < 0) {
      mostrarMensaje('La cantidad no puede ser negativa');
      return;
    }
    row.find('.cantidad input').val(nuevaCantidad);
    mostrarToast('Cantidad actualizada para el producto: ' + articulo.descripcion);
    actualizarImporte(row);
    calcularDiferencia(row);
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
  } else {
    let tr = `
      <tr data-id="${articulo.id}">
        <td class="codigo"><input class="form-control codigo-barras-input" readonly style="font-size: 0.75rem !important;" type="text" value="${articulo.codigo}" name="CodBarras[]" /></td>
        <td class="descripcion"><textarea class="form-control descripcion-producto-input" readonly style="font-size: 0.75rem !important;" name="NombreDelProducto[]">${articulo.descripcion}</textarea></td>
        <td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem !important;" type="number" name="Contabilizado[]" value="${articulo.cantidad}" onchange="calcularDiferencia(this)" /></td>
        <td class="ExistenciasEnBd"><input class="form-control cantidad-existencias-input" readonly style="font-size: 0.75rem !important;" type="number" name="StockActual[]" value="${articulo.existencia}" /></td>
        <td class="Diferenciaresultante"><input class="form-control cantidad-diferencia-input" style="font-size: 0.75rem !important;" type="number" name="Diferencia[]" /></td>
        <td class="preciofijo"><input class="form-control preciou-input" readonly style="font-size: 0.75rem !important;" type="number" value="${articulo.precio}" /></td>
        <td class="tipoajuste"><input class="form-control tipoajuste-input" readonly style="font-size: 0.75rem !important;" name="Tipodeajusteaplicado[]" type="text" /></td>
          <td class="anaquel"><input class="form-control anaquel-input" readonly style="font-size: 0.75rem !important;" name="AnaquelSeleccionado[]" type="text" /></td>
          <td class="repisa"><input class="form-control repisa-input" readonly style="font-size: 0.75rem !important;" name="RepisaSeleccionada[]" type="text" /></td>
        <td style="display:none;" class="preciodecompra"><input class="form-control preciocompra-input" style="font-size: 0.75rem !important;" name="PrecioCompra[]" value="${articulo.preciocompra}" /></td>
        <td style="display:none;" class="precio"><input hidden id="precio_${articulo.id}" class="form-control precio" style="font-size: 0.75rem !important;" type="number" name="PrecioVenta[]" value="${articulo.precio}" onchange="actualizarImporte($(this).parent().parent());" /></td>
        <td style="display:none;"><input id="importe_${articulo.id}" class="form-control importe" name="ImporteGenerado[]" style="font-size: 0.75rem !important;" type="number" readonly /></td>
        <td style="display:none;" class="idbd"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="${articulo.id}" name="IdBasedatos[]" /></td>
        <td style="display:none;" class="ResponsableInventario"><input hidden id="VendedorFarma" type="text" class="form-control" name="AgregoElVendedor[]" readonly value="<?php echo $row['Nombre_Apellidos']; ?>" /></td>
        <td style="display:none;" class="Sucursal"><input hidden type="text" class="form-control" name="Fk_sucursal[]" readonly value="<?php echo $row['Fk_Sucursal']; ?>" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="Sistema[]" readonly value="POS" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="ID_H_O_D[]" readonly value="Saluda" /></td>
        <td style="display:none;" class="Fecha"><input hidden type="text" class="form-control" name="FechaInv[]" readonly value="<?php echo $fechaActual; ?>" /></td>
        <td><div class="btn-container"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button></div></td>
      </tr>`;

    $('#tablaAgregarArticulos tbody').prepend(tr);
    let newRow = $('#tablaAgregarArticulos tbody tr:first-child');
    actualizarImporte(newRow);
    calcularDiferencia(newRow);
    newRow.find('.tipoajuste-input').val(selectedAdjustment);
    newRow.find('.anaquel-input').val(selectAnaquel);
    newRow.find('.repisa-input').val(selectRepisa);
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
  }

  limpiarCampo();
  $('#codigoEscaneado').focus();
}

  






  function mostrarToast(mensaje) {
  var toast = $('<div class="toast"></div>').text(mensaje);
  $('body').append(toast);
  toast.fadeIn(400).delay(3000).fadeOut(400, function() {
    $(this).remove();
  });
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
<style>
.toast {
  position: fixed;
  bottom: 10px;
  right: 10px;
  background-color: #333;
  color: #fff;
  padding: 10px 20px;
  border-radius: 5px;
  opacity: 0.9;
  z-index: 1000;
  display: none;
}
</style>
<!-- Control Sidebar -->

<!-- Main Footer -->
<?php

include("Modales/Error.php");
include ("Modales/FiltraEspecificamenteInventarios.php");
include ("Modales/DescargaInventariosPorExcel.php");
include "Footer.php";?>


  <!-- ./wrapper -->


  <script src="js/RealizaCambioDeSucursalPorFiltro.js"></script>
  <script src="js/RegistraInventariosEnModoConteo.js"></script>
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
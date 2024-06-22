<?php
$SucursalDestino = $_POST['SucursalConOrdenDestino'];
$SucursalDestinoLetras = $_POST['sucursalLetras'];
$ProveedorFijo = $_POST['NombreProveedor'];
$NumeroOrdenTraspaso = $_POST['NumOrden'];


if ($ProveedorFijo === "CEDIS") {
    // Tomar solo las primeras 4 letras de $SucursalDestinoLetras
    $primerasCuatroLetras = substr($SucursalDestinoLetras, 0, 4);

    // Combinar $primerasCuatroLetras con $NumeroDeFacturaTrapaso
    $valorCombinado = $primerasCuatroLetras . $NumeroOrdenTraspaso;
} else {
    // Utilizar solo el valor original de $NumeroDeFacturaTrapaso
    $valorCombinado = $NumeroDeFacturaTrapaso;
}

include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>Generar traspasos de cedis <?php echo $row['Licencia']?> </title>

<?php include "header.php"?>

 <style>
        .error {
  color: red;
  margin-left: 5px; 
  
  
}
table td {
  word-wrap: break-word;
  max-width: 400px;
}

    </style>

<style>
        .error {
  color: red;
  margin-left: 5px; 
  
}
#Tarjeta2{
  background-color: #2bbbad !important;
  color: white;
}
    </style>
</head>

<?php include_once "Menu.php" ?>


<?php include "navbar.php";?>

<div class="tab-content" id="pills-tabContent">


  <div class="card text-center">
  <div class="card-header" style="background-color:#2b73bb !important;color: white;">
 Traspasos de cedis de <?php echo $row['ID_H_O_D']?>  
  </div>
 
  <div >
 
                
</div>
</div>




  <div class="container">
<div class="row">


    <div class="col">
   
    <label for="exampleFormControlInput1">Sucursal destino</label> 
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
  <input type="text" name="" hidden id="" readonly class="form-control" value="<?php echo $SucursalDestino?>">
  <input type="text" name="" id=""  readonly class="form-control" value="<?php echo $SucursalDestinoLetras?>">
  </div>
  
    </div>  </div>
        
   
            
 
    <div class="col">
      
    <label for="exampleFormControlInput1"># de factura</label> 
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-clock"></i></span>
  </div>
  <input type="text" class="form-control" readonly value="<?php echo ($ProveedorFijo === 'CEDIS') ? $valorCombinado : $NumeroDeFacturaTrapaso; ?>">

  
  
    </div>  </div>
      
    <div class="col">
      
    <label for="exampleFormControlInput1">Fecha de entrega</label> 
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-clock"></i></span>
  </div>
  <input type="date" class="form-control "   value="<?php echo date("Y-m-d")?>"  > 
  
  
    </div>  </div>


<div style="display: none;">  <div class="col">
      
  <label for="exampleFormControlInput1">Total de venta </label>
      <div class="input-group mb-3">
    <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-money-check-alt"></i></span>
    </div>
    <input type="number" class="form-control " id="resultadoventas" name="resultadoventas[]" readonly  >
   
      </div>
  
      </div>
      </div>
      <div style="display: none;"> 
      <div class="col">
      
  <label for="exampleFormControlInput1">Total de compra </label> 
      <div class="input-group mb-3">
    <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-money-check-alt"></i></span>
    </div>
    <input type="number" class="form-control " id="resultadocompras" name="resultadocompras[]" readonly  >
   
      </div>
      </div>
      </div>
      <div class="col">
      
  <label for="exampleFormControlInput1">Total de Piezas </label> 
      <div class="input-group mb-3">
    <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fa-solid fa-puzzle-piece"></i></span>
    </div>
    <input type="number" class="form-control " id="resultadopiezas" name="resultadepiezas[]" readonly  >
   
      </div>
  
      </div>
      <!-- <div class="col">
      
  <label for="exampleFormControlInput1">Total de compra </label> 
      <div class="input-group mb-3">
    <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta2"><i class="fas fa-money-check-alt"></i></span>
    </div>
    <input type="number" class="form-control " id="resultadocompras" name="resultadocompras[]" readonly  >
   
      </div>
  
      </div> -->
</div>
</div>
</div>

<div class="content">


  <div class="container-fluid">

    <div class="row mb-3">

  

          <div class="card-body p-3"></div>
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
                      
                      <form action="javascript:void(0)"  method="post" id="Generamelostraspasos">
                      <div class="text-center">
        <button type="submit" class="btn btn-primary">Guardar datos</button>
    </div>
               
                        <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                          <thead>
                            <tr>
                              <th>Codigo</th>
                              <th >Producto</th>
                              <th>Precio compra</th>
                              <th >Precio Venta</th>
                              <th >Piezas</th>
                             
                            
                              
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
    url: "Consultas/escaner_articulo.php",
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
      
<td  class="preciodecompra"><input class="form-control preciocompra-input" style="font-size: 0.75rem !important;" name="PrecioCompra[]" value="${articulo.preciocompra}" /></td>
        <td class="preciofijo"><input class="form-control preciou-input" readonly style="font-size: 0.75rem !important;" type="number" value="${articulo.precio}" /></td>
        
        <td style="display:none;" class="precio"><input hidden id="precio_${articulo.id}" class="form-control precio" style="font-size: 0.75rem !important;" type="number" name="PrecioVenta[]" value="${articulo.precio}" onchange="actualizarImporte($(this).parent().parent());" /></td>
          <td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem !important;" type="number" name="Contabilizado[]" value="${articulo.cantidad}" onchange="calcularDiferencia(this)" /></td>
        <td style="display:none;"><input id="importe_${articulo.id}" class="form-control importe" name="ImporteGenerado[]" style="font-size: 0.75rem !important;" type="number" readonly /></td>
        <td style="display:none;" class="idbd"><input class="form-control" style="font-size: 0.75rem !important;" type="text" value="${articulo.id}" name="IdBasedatos[]" /></td>
        <td style="display:none;" class="ResponsableInventario"><input hidden id="VendedorFarma" type="text" class="form-control" name="AgregoElVendedor[]" readonly value="<?php echo $row['Nombre_Apellidos']; ?>" /></td>
        <td style="display:none;" class="Sucursal"><input hidden type="text" class="form-control" name="Fk_sucursal[]" readonly value="<?php echo $row['Fk_Sucursal']; ?>" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="Sistema[]" readonly value="POS" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="ID_H_O_D[]" readonly value="Saluda" /></td>
        <td style="display:none;" class="Fecha"><input hidden type="text" class="form-control" name="FechaAprox[]" readonly value="<?php echo $fechaActual; ?>" /></td>
        <td><div class="btn-container"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button></div></td>
       <td style="display:none;"> <input type="text" name="SucursalTraspasa[]" hidden value="21" class="form-control" ></td>
     <td style="display:none;"><input type="text" class="form-control "  hidden name="GeneradoPor[]" value="<?php echo $row['Nombre_Apellidos']?>"readonly  ></td>
       <td style="display:none;"> <input type="text" class="form-control " hidden  name="Empresa[]" value="<?php echo $row['ID_H_O_D']?>"readonly  ></td>
        <td style="display:none;"><input type="text"  hidden name="Proveedor1[]" id="proveedor1" class="form-control" ></td>
        <td style="display:none;"><input type="text" hidden name="Proveedor2[]" id="proveedor2" class="form-control" ></td>
        <td style="display:none;"><input type="text" hidden name="Estatus[]" value="Generado" class="form-control" ></td>
        <td style="display:none;"><input type="text" hidden name="Existencia1[]" value="0" class="form-control" ></td>
        <td style="display:none;"><input type="text" hidden name="Existencia2[]" value="0" class="form-control" ></td>
        <td style="display:none;"><input type="text" hidden name="Recibio[]" value="" class="form-control" ></td>
        <td style="display:none;"><input type="text" class="form-control " hidden name="NumeroDelTraspaso[]" readonly  value="<?php echo $NumeroOrdenTraspaso?>"  > </td>
        <td style="display:none;"><input type="text" class="form-control " hidden  name="ProveedorDelTraspaso[]" readonly  value="<?php echo $ProveedorFijo?>"  ></td>
        <td style="display:none;"><input type="text" class="form-control " hidden name="NumeroDeFacturaTraspaso[]" readonly  value="<?php echo $NumeroDeFacturaTrapaso?>"  > </td>
     
     
        </tr>`;

    $('#tablaAgregarArticulos tbody').prepend(tr);
    let newRow = $('#tablaAgregarArticulos tbody tr:first-child');
    actualizarImporte(newRow);
  
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
        document.addEventListener('DOMContentLoaded', function() {
            // Función para actualizar el total
            function updateTotal() {
                var total = 0;
                var inputs = document.querySelectorAll('.cantidad-vendida-input');
                console.log('Inputs actuales:', inputs);

                // Recorre todos los inputs y suma sus valores
                inputs.forEach(function(input) {
                    var value = parseFloat(input.value);
                    console.log('Valor del input:', value);
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                // Actualiza el input con id "resultadopiezas" con el total calculado
                console.log('Total calculado:', total);
                document.getElementById('resultadopiezas').value = total;
            }

            // Añade un event listener a cada input para escuchar cambios
            function addInputListeners() {
                var inputs = document.querySelectorAll('.cantidad-vendida-input');
                inputs.forEach(function(input) {
                    input.addEventListener('input', updateTotal);
                });
            }

            // Observer para detectar cambios en el DOM y agregar event listeners a nuevos inputs
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.querySelectorAll) {
                            var newInputs = node.querySelectorAll('.cantidad-vendida-input');
                            newInputs.forEach(function(input) {
                                input.addEventListener('input', updateTotal);
                            });
                        }
                    });
                    updateTotal(); // Actualizar el total si se agrega un nuevo nodo
                });
            });

            // Configura el observer para observar cambios en el body
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // Añade event listeners a los inputs existentes al cargar la página
            addInputListeners();

            // Llama a updateTotal al cargar la página por si ya hay valores predefinidos
            updateTotal();
        });
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


      <!-- Fin Contenido --> 
<!-- POR CADUCAR -->
  

  <!-- Control Sidebar -->
 
  <!-- Main Footer -->
<?php

  include ("Modales/Error.php");
  include ("Modales/Exito.php");
  include ("Modales/ExitoActualiza.php");
  include ("Modales/ActualizaProductosRegistrados.php");
  include "Footer.php";?>

<!-- ./wrapper -->
<script src="js/RealizaTraspasosV2.js"></script>

<script src="datatables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>  
    <script src="datatables/JSZip-2.5.0/jszip.min.js"></script>    
    <script src="datatables/pdfmake-0.1.36/pdfmake.min.js"></script>    
    <script src="datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
    <script src="datatables/Buttons-1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>


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

function fechaCastellano ($fecha) {
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
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}
?>
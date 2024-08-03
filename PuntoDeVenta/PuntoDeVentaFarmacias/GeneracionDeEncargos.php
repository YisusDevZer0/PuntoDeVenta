<?php
include('header.php');
include('navbar.php');
include('ControladorUsuario.php');
include('Menu.php');
?>

<div class="container-fluid">
  <div class="row mb-3">
    <div class="card-body p-3">
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
          <div class="row">
            <!-- FORMULARIO DE ENCARGO -->
            <div class="col-md-12 mb-3">
              <h3>Nuevo Encargo</h3>
              <form id="formEncargo" method="post" action="Controladores/GuardarEncargo.php">
                <div class="row">
                  <!-- Selección del Proveedor -->
                  <div class="col-md-4 mb-3">
                    <label for="proveedoresSelect">Proveedor</label>
                    <select id="proveedoresSelect" name="proveedor" class="form-control" required>
                      <?php
                      // Cargar proveedores desde la base de datos
                      $proveedores = obtenerProveedores();
                      foreach ($proveedores as $proveedor) {
                        echo "<option value='{$proveedor['id']}'>{$proveedor['nombre']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <!-- Fecha del Encargo -->
                  <div class="col-md-4 mb-3">
                    <label for="fechaEncargo">Fecha</label>
                    <input type="date" id="fechaEncargo" name="fecha" class="form-control" required>
                  </div>

                  <!-- Número de Factura -->
                  <div class="col-md-4 mb-3">
                    <label for="numeroFactura">Número de Factura</label>
                    <input type="text" id="numeroFactura" name="numeroFactura" class="form-control" required>
                  </div>

                  <!-- Número de Solicitud -->
                  <div class="col-md-4 mb-3">
                    <label for="numeroSolicitud">Número de Solicitud</label>
                    <input type="text" id="numeroSolicitud" name="numeroSolicitud" class="form-control" readonly value="<?php echo generarNumeroSolicitud(); ?>">
                  </div>
                </div>

                <!-- Campo para Escanear Productos -->
                <div class="form-group mb-3">
                  <label for="codigoEscaneado">Escanear Código de Barras</label>
                  <input type="text" class="form-control" id="codigoEscaneado" placeholder="Escanee o ingrese el código aquí">
                </div>

                <!-- Estado del Encargo -->
                <div class="form-group mb-3">
                  <label for="estadoEncargo">Estado del Encargo</label>
                  <select id="estadoEncargo" name="estado" class="form-control" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completado">Completado</option>
                  </select>
                </div>

                <!-- Botón para Enviar -->
                <button type="submit" class="btn btn-primary">Enviar Encargo</button>
              </form>
            </div>

            <!-- Tabla de Productos Encargados -->
            <div class="col-md-12 mt-4">
              <h3>Productos en el Encargo</h3>
              <div class="table-responsive">
                <table class="table table-striped" id="tablaEncargo">
                  <thead>
                    <tr>
                      <th>Codigo</th>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Fecha de Caducidad</th>
                      <th>Lote</th>
                      <th>Precio</th>
                      <th>Eliminar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Las filas se agregarán dinámicamente aquí -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include('Footer.php');
?>
<?php
function obtenerProveedores() {
    // Conectar a la base de datos
    require_once "db_connect.php";

    $sql = "SELECT id, nombre FROM proveedores";
    $resultado = $conn->query($sql);

    $proveedores = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $proveedores[] = $row;
        }
    }

    $conn->close();
    return $proveedores;
}

function generarNumeroSolicitud() {
    // Generar un número de solicitud único
    return uniqid('sol_');
}

function guardarEncargo($proveedor, $fecha, $numeroFactura, $numeroSolicitud, $estado, $productos) {
    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'usuario', 'contraseña', 'basededatos');

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar encargo
    $sql = "INSERT INTO encargos (proveedor, fecha, numeroFactura, numeroSolicitud, estado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $proveedor, $fecha, $numeroFactura, $numeroSolicitud, $estado);
    $resultado = $stmt->execute();

    if ($resultado) {
        $encargoId = $conn->insert_id;

        // Insertar productos
        $sqlProducto = "INSERT INTO productos_encargo (encargo_id, codigo, nombre, cantidad, fechaCaducidad, lote, precio) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtProducto = $conn->prepare($sqlProducto);

        foreach ($productos as $producto) {
            $stmtProducto->bind_param('issssss', $encargoId, $producto['codigo'], $producto['nombre'], $producto['cantidad'], $producto['fechaCaducidad'], $producto['lote'], $producto['precio']);
            $stmtProducto->execute();
        }

        $stmtProducto->close();
    }

    $stmt->close();
    $conn->close();

    return $resultado;
}
?>

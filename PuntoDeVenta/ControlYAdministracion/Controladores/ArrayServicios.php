<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT * FROM Servicios_POS WHERE Licencia = ?";
$licencia = "'".$row['Licencia']."'"; // Aquí debes definir el valor de la licencia que estás buscando

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetro
$stmt->bind_param("s", $licencia);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Construir el array de datos
    // Aquí puedes seguir con la lógica que ya tienes para construir los datos de salida
    
    $data[] = [
        "ServicioID" => $fila["ID_Prod_POS"],
        "Nombre_Servicio" => $fila["Cod_Barra"],
        "Estado" => $fila["Nombre_Prod"],
        "AgregadoPor" => $fila["Clave_adicional"],
        "FechaAgregado" => $fila["Clave_Levic"],
        "Sistema" => $fila["Precio_C"],
        "Licencia" => $fila["Precio_Venta"],
       
        // "Acciones" => "<button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'><a href=https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStock?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar en sucursales <i class='fas fa-clinic-medical'></i></a><a href=https://controlfarmacia.com/AdminPOS/DistribucionSucursales?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-VerDistribucion  dropdown-item' >Consultar distribución <i class='fas fa-table'></i> </a><a href=https://controlfarmacia.com/AdminPOS/EdicionDatosProducto?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-editProd dropdown-item' >Editar datos <i class='fas fa-pencil-alt'></i></a><a href=https://controlfarmacia.com/AdminPOS/HistorialProducto?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-History dropdown-item' >Ver movimientos <i class='fas fa-history'></i></a><a href=https://controlfarmacia.com/AdminPOS/MaximoYMinimo?Disid=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Actualiza minimo y maximo <i class='fas fa-list-ol'></i></a><a href=https://controlfarmacia.com/AdminPOS/CambiaProveedor?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-Delete dropdown-item' >Cambio de proveedores <i class='fas fa-truck-loading'></i></a></div>",
        // "AccionesEnfermeria" => "<button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-th-list fa-1x'></i></button><div class='dropdown-menu'><a href=https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStockEnfermeria?idProd=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar a enfermeria <i class='fas fa-user-nurse'></i></a><a href=https://controlfarmacia.com/AdminPOS/CrearCodEnfermeria?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Editar datos  <i class='fas fa-edit'></i></a><a href=https://controlfarmacia.com/AdminPOS/AsignaProcedimiento?editprod=".base64_encode($fila["ID_Prod_POS"])." class='btn-edit  dropdown-item' >Asignar procedimiento  <i class='fas fa-edit'></i></a></div>"
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>

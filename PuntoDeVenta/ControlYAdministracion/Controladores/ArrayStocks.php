<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Stock_POS.Folio_Prod_Stock,Stock_POS.Clave_adicional,Stock_POS.ID_Prod_POS,Stock_POS.AgregadoEl,Stock_POS.Clave_adicional,Stock_POS.Clave_Levic,
Stock_POS.Cod_Barra,Stock_POS.Nombre_Prod,Stock_POS.Tipo_Servicio,Stock_POS.Tipo,Stock_POS.Fk_sucursal,
Stock_POS.Max_Existencia,Stock_POS.Min_Existencia, Stock_POS.Existencias_R,Stock_POS.Proveedor1,
Stock_POS.Proveedor2,Stock_POS.Estatus,Stock_POS.ID_H_O_D, Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal,Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv, Productos_POS.ID_Prod_POS,
Productos_POS.Precio_Venta,Productos_POS.Precio_C 
FROM Stock_POS
INNER JOIN Sucursales ON Stock_POS.Fk_sucursal = Sucursales.ID_Sucursal
INNER JOIN Servicios_POS ON Stock_POS.Tipo_Servicio= Servicios_POS.Servicio_ID
INNER JOIN Productos_POS ON Productos_POS.ID_Prod_POS =Stock_POS.ID_Prod_POS";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Verificar si las existencias están por debajo del mínimo
    $mostrarBotonOrden = ($fila['Existencias_R'] <= $fila['Min_Existencia']);
    
    // Construir el array de datos
    $data[] = [
        "Cod_Barra" => $fila["Cod_Barra"],
        "Clave_adicional" => $fila["Clave_adicional"],
        "Clave_Levic" => $fila["Clave_Levic"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Nom_Serv" => $fila["Nom_Serv"],
        "Tipo" => $fila["Tipo"],
        "Proveedor1" => $fila["Proveedor1"],
        "Proveedor2" => $fila["Proveedor2"],
        "Sucursal" => $fila["Nombre_Sucursal"],
        "UltimoMovimiento" => $fila["AgregadoEl"],
        "Existencias_R" => $fila["Existencias_R"],
        "Min_Existencia" => $fila["Min_Existencia"],
        "Max_Existencia" => $fila["Max_Existencia"],
        'Editar' => "<div class='btn-group'>
            <button type='button' class='btn btn-info btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                Selecciona por favor<i class='fa-solid fa-chevron-down'></i>
            </button>
            <ul class='dropdown-menu'>
                <li>
                    <a class='dropdown-item btn-minimomaximo' data-id='$fila[Folio_Prod_Stock]'>
                        Editar minimo y maximo
                    </a>
                </li>
                <li>
                    <a class='dropdown-item btn-editproducto' data-id='$fila[Folio_Prod_Stock]'>
                        Editar datos del producto
                    </a>
                </li>
                <li>
                    <a class='dropdown-item btn-AjustInvetario' data-id='$fila[Folio_Prod_Stock]'>
                        Ajuste de inventario
                    </a>
                </li>
                " . ($mostrarBotonOrden ? "
                <li>
                    <a class='dropdown-item btn-GeneraOrdenCompra' 
                       data-id='$fila[Folio_Prod_Stock]'
                       data-nombre='$fila[Nombre_Prod]'
                       data-codigo='$fila[Cod_Barra]'
                       data-existencias='$fila[Existencias_R]'
                       data-minimo='$fila[Min_Existencia]'
                       data-maximo='$fila[Max_Existencia]'
                       data-sucursal='$fila[Fk_sucursal]'
                       data-proveedor='$fila[Proveedor1]'
                       data-hod='$fila[ID_H_O_D]'>
                        Generar orden de compra
                    </a>
                </li>
                " : "") . "
                <li>
                    <a class='dropdown-item eliminarprod' data-id='$fila[Folio_Prod_Stock]'>
                        Eliminar producto
                    </a>
                </li>
            </ul>
        </div>",
        "Eliminar" => "<a href='https://saludapos.com/AdminPOS/ActualizaOne?idProd=" . base64_encode($fila["Folio_Prod_Stock"]) . "' type='button' class='btn btn-info btn-sm'><i class='fas fa-capsules'></i></a>",
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

<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Plantilla_Maximos_Minimos.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Conexión a la base de datos
include_once "Controladores/db_connect.php"; // Ajusta la ruta según tu estructura

// Consulta SQL para obtener los datos
$sql = "
    SELECT 
        s.Folio_Prod_Stock,
        s.ID_Prod_POS,
        s.Cod_Barra,
        s.Nombre_Prod,
        s.Fk_sucursal,
        su.Nombre_Sucursal,
        s.Max_Existencia,
        s.Min_Existencia
    FROM 
        Stock_POS s
    INNER JOIN 
        Sucursales su ON s.Fk_sucursal = su.ID_Sucursal
    -- WHERE s.Fk_sucursal = 1  -- (Ejemplo de filtro opcional)
    ORDER BY 
        su.Nombre_Sucursal, s.Nombre_Prod;
";

$result = $conn->query($sql);

// Generar el contenido del archivo Excel
echo "Folio Producto\tID Producto\tCódigo de Barra\tNombre Producto\tID Sucursal\tNombre Sucursal\tMáximo\tMínimo\n";

echo "12345\t1\t1234567890123\tProducto A\t1\tSucursal A\t100\t10\n";
echo "12346\t2\t1234567890124\tProducto B\t1\tSucursal A\t200\t20\n";

$conn->close();
?>
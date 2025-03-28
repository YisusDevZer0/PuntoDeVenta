<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include_once "Controladores/db_connect.php";

if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

$sql = "SELECT 
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
    ORDER BY 
        su.Nombre_Sucursal, s.Nombre_Prod;";

$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Folio Producto');
$sheet->setCellValue('B1', 'ID Producto');
$sheet->setCellValue('C1', 'Código de Barra');
$sheet->setCellValue('D1', 'Nombre Producto');
$sheet->setCellValue('E1', 'ID Sucursal');
$sheet->setCellValue('F1', 'Nombre Sucursal');
$sheet->setCellValue('G1', 'Máximo');
$sheet->setCellValue('H1', 'Mínimo');

$row = 2;
if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['Folio_Prod_Stock']);
        $sheet->setCellValue('B' . $row, $data['ID_Prod_POS']);
        $sheet->setCellValue('C' . $row, $data['Cod_Barra']);
        $sheet->setCellValue('D' . $row, $data['Nombre_Prod']);
        $sheet->setCellValue('E' . $row, $data['Fk_sucursal']);
        $sheet->setCellValue('F' . $row, $data['Nombre_Sucursal']);
        $sheet->setCellValue('G' . $row, $data['Max_Existencia']);
        $sheet->setCellValue('H' . $row, $data['Min_Existencia']);
        $row++;
    }
}

$conn->close();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Plantilla_Maximos_Minimos.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
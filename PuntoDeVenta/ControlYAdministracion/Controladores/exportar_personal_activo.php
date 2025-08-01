<?php
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include("db_connect.php");
include "ControladorUsuario.php";

// Obtener parámetros de filtro
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT 
    u.Id_PvUser,
    u.Nombre_Apellidos,
    u.Correo_Electronico,
    u.Telefono,
    u.Fecha_Nacimiento,
    t.TipoUsuario,
    s.Nombre_Sucursal,
    u.AgregadoEl,
    u.Estatus,
    u.AgregadoPor
FROM Usuarios_PV u
INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
INNER JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
WHERE u.Estatus = 'Activo'";

// Aplicar filtros
if (!empty($tipo)) {
    $sql .= " AND t.TipoUsuario = '" . mysqli_real_escape_string($conn, $tipo) . "'";
}
if (!empty($sucursal)) {
    $sql .= " AND s.ID_Sucursal = '" . mysqli_real_escape_string($conn, $sucursal) . "'";
}
if (!empty($estado)) {
    $sql .= " AND u.Estatus = '" . mysqli_real_escape_string($conn, $estado) . "'";
}

$sql .= " ORDER BY u.Id_PvUser DESC";

$result = mysqli_query($conn, $sql);

// Crear nuevo documento Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configurar título
$sheet->setCellValue('A1', 'REPORTE DE PERSONAL ACTIVO');
$sheet->mergeCells('A1:J1');

// Configurar encabezados
$headers = [
    'ID Empleado',
    'Nombre Completo',
    'Correo Electrónico',
    'Teléfono',
    'Fecha de Nacimiento',
    'Tipo de Usuario',
    'Sucursal',
    'Fecha de Creación',
    'Estado',
    'Creado por'
];

$col = 'A';
$row = 3;
foreach ($headers as $header) {
    $sheet->setCellValue($col . $row, $header);
    $col++;
}

// Estilo para encabezados
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0172B6'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$sheet->getStyle('A3:J3')->applyFromArray($headerStyle);

// Llenar datos
$row = 4;
while ($fila = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $fila['Id_PvUser']);
    $sheet->setCellValue('B' . $row, $fila['Nombre_Apellidos']);
    $sheet->setCellValue('C' . $row, $fila['Correo_Electronico']);
    $sheet->setCellValue('D' . $row, $fila['Telefono']);
    $sheet->setCellValue('E' . $row, $fila['Fecha_Nacimiento']);
    $sheet->setCellValue('F' . $row, $fila['TipoUsuario']);
    $sheet->setCellValue('G' . $row, $fila['Nombre_Sucursal']);
    $sheet->setCellValue('H' . $row, $fila['AgregadoEl']);
    $sheet->setCellValue('I' . $row, $fila['Estatus']);
    $sheet->setCellValue('J' . $row, $fila['AgregadoPor']);
    $row++;
}

// Ajustar ancho de columnas
$sheet->getColumnDimension('A')->setWidth(12);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(18);
$sheet->getColumnDimension('G')->setWidth(20);
$sheet->getColumnDimension('H')->setWidth(18);
$sheet->getColumnDimension('I')->setWidth(12);
$sheet->getColumnDimension('J')->setWidth(20);

// Estilo para el título
$titleStyle = [
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '0172B6'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

$sheet->getStyle('A1')->applyFromArray($titleStyle);

// Estilo para los datos
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];

$lastRow = $row - 1;
if ($lastRow >= 4) {
    $sheet->getStyle('A4:J' . $lastRow)->applyFromArray($dataStyle);
}

// Configurar el archivo para descarga
$filename = 'Personal_Activo_' . date('Y-m-d_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?> 
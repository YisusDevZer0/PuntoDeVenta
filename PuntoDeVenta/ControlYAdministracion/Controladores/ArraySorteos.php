<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM Sorteo_Participaciones sp WHERE sp.Fk_Sorteo = s.ID_Sorteo AND sp.Participa = 1) as TotalParticipaciones,
        CASE 
            WHEN s.Activo = 1 AND CURDATE() BETWEEN s.Fecha_Inicio AND s.Fecha_Fin THEN 'Activo'
            WHEN s.Activo = 1 AND CURDATE() < s.Fecha_Inicio THEN 'Programado'
            WHEN s.Activo = 0 THEN 'Inactivo'
            ELSE 'Finalizado'
        END as Estado
        FROM Sorteos s 
        ORDER BY s.ID_Sorteo DESC";

$result = @mysqli_query($conn, $sql);

$c = 0;
$data = [];

if ($result) {
while ($fila = $result->fetch_assoc()) {
    $data[$c]["ID"] = $fila["ID_Sorteo"];
    $data[$c]["Nombre"] = $fila["Nombre_Sorteo"];
    $data[$c]["Descripcion"] = $fila["Descripcion"] ? $fila["Descripcion"] : '-';
    $data[$c]["FechaInicio"] = $fila["Fecha_Inicio"];
    $data[$c]["FechaFin"] = $fila["Fecha_Fin"];
    
    // Estado con badge
    $estadoClass = '';
    switch ($fila['Estado']) {
        case 'Activo': $estadoClass = 'background-color:#28a745;color:white;'; break;
        case 'Inactivo': $estadoClass = 'background-color:#dc3545;color:white;'; break;
        case 'Programado': $estadoClass = 'background-color:#ffc107;color:#333;'; break;
        default: $estadoClass = 'background-color:#6c757d;color:white;'; break;
    }
    $data[$c]["Estado"] = '<span style="'.$estadoClass.'padding:4px 10px;border-radius:12px;font-size:12px;">'.$fila["Estado"].'</span>';
    
    $data[$c]["Participaciones"] = '<span class="badge bg-info">'.$fila["TotalParticipaciones"].'</span>';
    
    // Sucursales
    if ($fila["Aplica_Todas_Sucursales"] == 1) {
        $data[$c]["Sucursales"] = '<span class="badge bg-primary">Todas</span>';
    } else {
        $sqlSuc = "SELECT su.Nombre_Sucursal FROM Sorteo_Sucursales ss 
                   LEFT JOIN Sucursales su ON su.ID_Sucursal = ss.Fk_Sucursal 
                   WHERE ss.Fk_Sorteo = " . intval($fila['ID_Sorteo']);
        $resSuc = mysqli_query($conn, $sqlSuc);
        $nombres = [];
        while ($suc = mysqli_fetch_assoc($resSuc)) {
            $nombres[] = $suc['Nombre_Sucursal'];
        }
        $data[$c]["Sucursales"] = count($nombres) > 0 ? implode(', ', $nombres) : '-';
    }
    
    $data[$c]["CreadoPor"] = $fila["CreadoPor"];
    
    // Botones de acciones
    $btnToggle = '';
    if ($fila['Activo'] == 1) {
        $btnToggle = '<button class="btn btn-warning btn-sm btn-toggle-sorteo" data-id="'.$fila["ID_Sorteo"].'" title="Desactivar"><i class="fa-solid fa-pause"></i></button>';
    } else {
        $btnToggle = '<button class="btn btn-success btn-sm btn-toggle-sorteo" data-id="'.$fila["ID_Sorteo"].'" title="Activar"><i class="fa-solid fa-play"></i></button>';
    }
    
    $acciones = '
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownSorteo'.$fila["ID_Sorteo"].'" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-th-list fa-1x"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownSorteo'.$fila["ID_Sorteo"].'">
                <li><a class="dropdown-item btn-editar-sorteo" href="#" data-id="'.$fila["ID_Sorteo"].'"><i class="fa-solid fa-edit"></i> Editar</a></li>
                <li><a class="dropdown-item btn-toggle-sorteo" href="#" data-id="'.$fila["ID_Sorteo"].'"><i class="fa-solid fa-power-off"></i> '.($fila['Activo'] == 1 ? 'Desactivar' : 'Activar').'</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item btn-eliminar-sorteo text-danger" href="#" data-id="'.$fila["ID_Sorteo"].'"><i class="fa-solid fa-trash"></i> Eliminar</a></li>
            </ul>
        </div>
    ';
    
    $data[$c]["Acciones"] = $acciones;
    
    $c++;
}
} // end if ($result)

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>

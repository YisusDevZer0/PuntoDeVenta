<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error_message = null;

try {
    include "../Controladores/db_connect.php";
    include "../Controladores/ControladorUsuario.php";

    $fcha = date("Y-m-d");
    $user_id = null;

    $fk_caja = isset($_POST['id']) ? $_POST['id'] : null;
    $fk_sucursal = isset($_POST['fk_sucursal']) ? $_POST['fk_sucursal'] : null;
    $id_h_o_d = isset($_POST['id_h_o_d']) ? $_POST['id_h_o_d'] : null;

    // Verificar que las variables POST están definidas
    if (!$fk_caja || !$fk_sucursal || !$id_h_o_d) {
        $error_message = "Faltan parámetros necesarios: id, fk_sucursal, id_h_o_d";
    } else {
        // Sanitizar las variables para evitar SQL injection
        $fk_caja = mysqli_real_escape_string($conn, $fk_caja);
        $fk_sucursal = mysqli_real_escape_string($conn, $fk_sucursal);
        $id_h_o_d = mysqli_real_escape_string($conn, $id_h_o_d);

        // CONSULTA 1
        $sql1 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
                 FROM Ventas_POS 
                 WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
                 ORDER BY Venta_POS_ID ASC LIMIT 1";
        $query1 = $conn->query($sql1);
        $Especialistas = null;
        if ($query1 && $query1->num_rows > 0) {
            $Especialistas = $query1->fetch_object();
        }

        // CONSULTA 2
        $sql2 = "SELECT Venta_POS_ID, Folio_Ticket, Fk_Caja, Fk_sucursal, ID_H_O_D 
                 FROM Ventas_POS 
                 WHERE Fk_Caja = '$fk_caja' AND Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' 
                 ORDER BY Venta_POS_ID DESC LIMIT 1";
        $query2 = $conn->query($sql2);
        $Especialistas2 = null;
        if ($query2 && $query2->num_rows > 0) {
            $Especialistas2 = $query2->fetch_object();
        }

        // CONSULTA 3
        $sql3 = "SELECT Venta_POS_ID, Fk_Caja, Turno, Fecha_venta, Fk_sucursal, AgregadoPor, Turno, ID_H_O_D,
                        COUNT(DISTINCT Folio_Ticket) AS Total_tickets, 
                        COUNT(DISTINCT FolioSignoVital) AS Total_Folios, 
                        SUM(Importe) AS VentaTotal  
                 FROM Ventas_POS 
                 WHERE Fk_sucursal = '$fk_sucursal' AND ID_H_O_D = '$id_h_o_d' AND Fk_Caja = '$fk_caja'";
        $query3 = $conn->query($sql3);
        $Especialistas3 = null;
        if ($query3 && $query3->num_rows > 0) {
            $Especialistas3 = $query3->fetch_object();
        } else {
            // Inicializar valores por defecto si no hay resultados
            $Especialistas3 = (object)[
                'VentaTotal' => 0,
                'Total_tickets' => 0,
                'AgregadoPor' => 'N/A',
                'Turno' => ''
            ];
        }

        // CONSULTA 14
        $sql14 = "SELECT 
                    IFNULL(Servicios_POS.Servicio_ID, '0000') AS Servicio_ID, 
                    IFNULL(Servicios_POS.Nom_Serv, 'No tiene servicio especificado') AS Nom_Serv, 
                    Ventas_POS.Fk_sucursal, 
                    Ventas_POS.ID_H_O_D, 
                    Ventas_POS.Fecha_venta,
                    Ventas_POS.AgregadoPor, 
                    Ventas_POS.Fk_Caja, 
                    Ventas_POS.Turno, 
                    Ventas_POS.AgregadoEl, 
                    Sucursales.ID_Sucursal, 
                    Sucursales.Nombre_Sucursal, 
                    SUM(Ventas_POS.Importe) AS totaldeservicios 
                 FROM Ventas_POS
                 LEFT JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
                 INNER JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
                 WHERE Ventas_POS.Fk_Caja = '$fk_caja' 
                 AND Ventas_POS.ID_H_O_D = '$id_h_o_d'
                 GROUP BY Servicio_ID, Nom_Serv";

        $query14 = $conn->query($sql14);
        $Especialistas14 = [];
        if ($query14 && $query14->num_rows > 0) {
            while ($r = $query14->fetch_object()) {
                $Especialistas14[] = $r;
            }
        }

        // Obtener el nombre de la sucursal de manera segura
        $nombre_sucursal = 'Sucursal no especificada';
        if (!empty($Especialistas14)) {
            $nombre_sucursal = $Especialistas14[0]->Nombre_Sucursal ?? 'Sucursal no especificada';
        } else {
            // Si no hay datos de ventas, obtener el nombre de la sucursal directamente
            $sql_sucursal = "SELECT Nombre_Sucursal FROM Sucursales WHERE ID_Sucursal = '$fk_sucursal'";
            $query_sucursal = $conn->query($sql_sucursal);
            if ($query_sucursal && $query_sucursal->num_rows > 0) {
                $sucursal_data = $query_sucursal->fetch_object();
                $nombre_sucursal = $sucursal_data->Nombre_Sucursal;
            }
        }

        // ==================================================
        // AQUÍ VA EL NUEVO CÓDIGO
        // ==================================================

        // Inicializar $servicios con un arreglo vacío o un valor predeterminado
        $servicios = [];

        if (!empty($Especialistas14)) {
            foreach ($Especialistas14 as $especialista) {
                $servicios[] = [
                    'nombre' => $especialista->Nom_Serv,
                    'total' => $especialista->totaldeservicios ?? 0,
                ];
            }
        } else {
            // Si no hay servicios, asignar un valor predeterminado
            $servicios = [
                [
                    'nombre' => 'Sin servicios registrados',
                    'total' => 0
                ]
            ];
        }

        // Consulta 6: Total de venta por crédito en enfermería
        $sql6 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCredito 
                 FROM Ventas_POS 
                 WHERE FormaDePago = 'Crédito Enfermería' 
                 AND Fk_sucursal = '$fk_sucursal' 
                 AND ID_H_O_D = '$id_h_o_d' 
                 AND Fk_Caja = '$fk_caja'";
        $query6 = $conn->query($sql6);
        $Especialistas6 = null;
        if ($query6 && $query6->num_rows > 0) {
            $Especialistas6 = $query6->fetch_object();
        } else {
            $Especialistas6 = (object)['VentaTotalCredito' => 0];
        }

        // Consulta 7: Total de venta por crédito en limpieza
        $sql7 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoLimpieza 
                 FROM Ventas_POS 
                 WHERE FormaDePago = 'Crédito Limpieza' 
                 AND Fk_sucursal = '$fk_sucursal' 
                 AND ID_H_O_D = '$id_h_o_d' 
                 AND Fk_Caja = '$fk_caja'";
        $query7 = $conn->query($sql7);
        $Especialistas7 = null;
        if ($query7 && $query7->num_rows > 0) {
            $Especialistas7 = $query7->fetch_object();
        } else {
            $Especialistas7 = (object)['VentaTotalCreditoLimpieza' => 0];
        }

        // Consulta 11: Total de venta por crédito farmacéutico
        $sql11 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoFarmaceutico 
                  FROM Ventas_POS 
                  WHERE FormaDePago = 'Crédito Farmacéutico' 
                  AND Fk_sucursal = '$fk_sucursal' 
                  AND ID_H_O_D = '$id_h_o_d' 
                  AND Fk_Caja = '$fk_caja'";
        $query11 = $conn->query($sql11);
        $Especialistas11 = null;
        if ($query11 && $query11->num_rows > 0) {
            $Especialistas11 = $query11->fetch_object();
        } else {
            $Especialistas11 = (object)['VentaTotalCreditoFarmaceutico' => 0];
        }

        // Consulta 12: Total de venta por crédito médico
        $sql12 = "SELECT Venta_POS_ID, Fk_Caja, Fk_sucursal, Turno, ID_H_O_D, COUNT(DISTINCT Folio_Ticket) AS Total_tickets, SUM(Importe) AS VentaTotalCreditoMedicos 
                  FROM Ventas_POS 
                  WHERE FormaDePago = 'Crédito Médico' 
                  AND Fk_sucursal = '$fk_sucursal' 
                  AND ID_H_O_D = '$id_h_o_d' 
                  AND Fk_Caja = '$fk_caja'";
        $query12 = $conn->query($sql12);
        $Especialistas12 = null;
        if ($query12 && $query12->num_rows > 0) {
            $Especialistas12 = $query12->fetch_object();
        } else {
            $Especialistas12 = (object)['VentaTotalCreditoMedicos' => 0];
        }

        // Consulta 13: Cortes de cajas POS
        $sql13 = "SELECT * FROM Cortes_Cajas_POS 
                  WHERE Sucursal = '$fk_sucursal' 
                  AND ID_H_O_D = '$id_h_o_d' 
                  AND Fk_Caja = '$fk_caja'";
        $query13 = $conn->query($sql13);
        $Especialistas13 = null;
        if ($query13 && $query13->num_rows > 0) {
            $Especialistas13 = $query13->fetch_object();
        }

        // Consulta de totales
        $sql_totales = "SELECT 
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe 
                ELSE 0 
            END) AS totalesdepagoEfectivo,
            
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe 
                ELSE 0 
            END) AS totalesdepagotarjeta,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoTarjeta,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoEfectivo,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoEfectivoCredito,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoCredito,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoEfectivoTransferencia,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) AS complementoTransferencia,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe 
                ELSE 0 
            END) AS totalCredito,

            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Transferencia' THEN Ventas_POS.Importe 
                ELSE 0 
            END) AS totalTransferencia,

            (SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END)
            ) AS totalPagosEnEfectivo,

            (SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END)) AS totalPagosEnTarjeta,

            (SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END)) AS totalPagosEnCreditos,

            (SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Transferencia' THEN Ventas_POS.Importe 
                ELSE 0 
            END) +
            SUM(CASE 
                WHEN Ventas_POS.FormaDePago = 'Efectivo y Transferencia' THEN Ventas_POS.Pagos_tarjeta 
                ELSE 0 
            END)) AS totalPagosEnTransferencia,

            SUM(Ventas_POS.Importe) AS TotalCantidad
        FROM Ventas_POS 
        WHERE Ventas_POS.Fk_Caja = '$fk_caja' AND Ventas_POS.Fk_sucursal = '$fk_sucursal' AND Ventas_POS.ID_H_O_D = '$id_h_o_d'";

        $result_totales = $conn->query($sql_totales);

        if ($result_totales) {
            if ($result_totales->num_rows > 0) {
                $row_totales = $result_totales->fetch_assoc();
            } else {
                $row_totales = [
                    'totalesdepagoEfectivo' => 0,
                    'totalesdepagotarjeta' => 0,
                    'complementoTarjeta' => 0,
                    'complementoEfectivo' => 0,
                    'complementoEfectivoCredito' => 0,
                    'complementoCredito' => 0,
                    'complementoCreditoEfectivo' => 0,
                    'totalCredito' => 0,
                    'totalTransferencia' => 0,
                    'complementoEfectivoTransferencia' => 0,
                    'complementoTransferencia' => 0,
                    'totalPagosEnEfectivo' => 0,
                    'totalPagosEnTarjeta' => 0,
                    'totalPagosEnCreditos' => 0,
                    'totalPagosEnTransferencia' => 0,
                    'TotalCantidad' => 0
                ];
            }
        } else {
            echo '<p class="alert alert-danger">Error en la consulta: ' . $conn->error . '</p>';
        }

        // Asignar valores con defaults
        $totalesdepagoEfectivo = $row_totales['totalesdepagoEfectivo'] ?? 0;
        $totalesdepagotarjeta = $row_totales['totalesdepagotarjeta'] ?? 0;
        $complementoTarjeta = $row_totales['complementoTarjeta'] ?? 0;
        $complementoEfectivo = $row_totales['complementoEfectivo'] ?? 0;
        $complementoEfectivoCredito = $row_totales['complementoEfectivoCredito'] ?? 0;
        $complementoCredito = $row_totales['complementoCredito'] ?? 0;
        $complementoCreditoEfectivo = $row_totales['complementoCreditoEfectivo'] ?? 0;
        $totalCredito = $row_totales['totalCredito'] ?? 0;
        $totalTransferencia = $row_totales['totalTransferencia'] ?? 0;
        $complementoEfectivoTransferencia = $row_totales['complementoEfectivoTransferencia'] ?? 0;
        $complementoTransferencia = $row_totales['complementoTransferencia'] ?? 0;
        
        // Los totales de formas de pago se calcularán más abajo incluyendo abonos, encargos y pagos de servicios
        // Inicializar valores base desde Ventas_POS
        $totalPagosEnEfectivo_base = $row_totales['totalPagosEnEfectivo'] ?? 0;
        $totalPagosEnTarjeta_base = $row_totales['totalPagosEnTarjeta'] ?? 0;
        $totalPagosEnCreditos_base = $row_totales['totalPagosEnCreditos'] ?? 0;
        $totalPagosEnTransferencia_base = $row_totales['totalPagosEnTransferencia'] ?? 0;
        $TotalCantidad_base = $row_totales['TotalCantidad'] ?? 0;

        // Nueva consulta para gastos
        $sql_gastos = "SELECT 
            gp.ID_Gastos,
            gp.Concepto_Categoria,
            gp.Importe_Total,
            gp.Fk_sucursal,
            s.Nombre_Sucursal,
            gp.Fk_Caja,
            c.ID_Caja,
            gp.Recibe,
            gp.FechaConcepto
        FROM 
            GastosPOS gp
        INNER JOIN 
            Sucursales s ON gp.Fk_sucursal = s.ID_Sucursal
        INNER JOIN 
            Cajas c ON gp.Fk_Caja = c.ID_Caja
        WHERE 
            c.ID_Caja = '$fk_caja'";

        $query_gastos = $conn->query($sql_gastos);
        $gastos = [];
        if ($query_gastos && $query_gastos->num_rows > 0) {
            while ($row = $query_gastos->fetch_assoc()) {
                $gastos[] = $row;
            }
        }

        // Calcular el total de gastos
        $total_gastos = 0;
        foreach ($gastos as $gasto) {
            $total_gastos += $gasto['Importe_Total'];
        }

        // Preparar el array de gastos para el campo oculto
        $gastos_array = [
            'detalle' => [],
            'total' => $total_gastos
        ];

        if (!empty($gastos)) {
            foreach ($gastos as $gasto) {
                $gastos_array['detalle'][] = [
                    'concepto' => $gasto['Concepto_Categoria'],
                    'importe' => $gasto['Importe_Total'],
                    'recibe' => $gasto['Recibe'],
                    'fecha' => $gasto['FechaConcepto']
                ];
            }
        }

        // ================= TABLA DE ABONOS DE ENCARGOS DEL DÍA ===================
        // Consulta de abonos de encargos del día
        $abonos_dia = [];
        $fecha_hoy = date('Y-m-d');

        // Verificar si las tablas existen antes de hacer las consultas
        $sql_check_abonos = "SHOW TABLES LIKE 'historial_abonos_encargos'";
        $result_check_abonos = $conn->query($sql_check_abonos);

        if ($result_check_abonos && $result_check_abonos->num_rows > 0) {
            $sql_abonos = "SELECT h.encargo_id, h.monto_abonado, h.forma_pago, h.efectivo_recibido, h.observaciones, h.fecha_abono, h.empleado, h.sucursal, e.nombre_paciente, e.medicamento, e.cantidad, e.precioventa
            FROM historial_abonos_encargos h
            LEFT JOIN encargos e ON h.encargo_id = e.id
            WHERE DATE(h.fecha_abono) = ? AND h.sucursal = ? AND e.Fk_Caja = ?
            ORDER BY h.fecha_abono DESC";
            
            if ($stmt_abonos = $conn->prepare($sql_abonos)) {
                $stmt_abonos->bind_param("sss", $fecha_hoy, $fk_sucursal, $fk_caja);
                $stmt_abonos->execute();
                $result_abonos = $stmt_abonos->get_result();
                while ($abono = $result_abonos->fetch_assoc()) {
                    $abonos_dia[] = $abono;
                }
                $stmt_abonos->close();
            }
        }

        // ================= TABLA DE ENCARGOS DEL DÍA ===================
        $encargos_dia = [];

        // Verificar si la tabla encargos existe
        $sql_check_encargos = "SHOW TABLES LIKE 'encargos'";
        $result_check_encargos = $conn->query($sql_check_encargos);

        if ($result_check_encargos && $result_check_encargos->num_rows > 0) {
            $sql_encargos = "SELECT id, nombre_paciente, medicamento, cantidad, precioventa, abono_parcial, fecha_encargo, estado, FormaDePago
            FROM encargos
            WHERE DATE(fecha_encargo) = ? AND Fk_Sucursal = ? AND Fk_Caja = ?
            ORDER BY fecha_encargo DESC";
            
            if ($stmt_encargos = $conn->prepare($sql_encargos)) {
                $stmt_encargos->bind_param("sss", $fecha_hoy, $fk_sucursal, $fk_caja);
                $stmt_encargos->execute();
                $result_encargos = $stmt_encargos->get_result();
                while ($encargo = $result_encargos->fetch_assoc()) {
                    $encargos_dia[] = $encargo;
                }
                $stmt_encargos->close();
            }
        }

        // ================= TABLA DE PAGOS DE SERVICIOS ===================
        // Consulta de pagos de servicios agrupados por servicio
        $Especialistas15 = [];
        $pagosServicios = [];

        // Verificar si la tabla PagosServicios existe
        $sql_check_pagos = "SHOW TABLES LIKE 'PagosServicios'";
        $result_check_pagos = $conn->query($sql_check_pagos);

        if ($result_check_pagos && $result_check_pagos->num_rows > 0) {
            // Consulta de pagos de servicios (sin filtro de fecha ya que la tabla no tiene campo de fecha)
            $sql15 = "SELECT 
                        ps.Servicio,
                        ls.Comision,
                        COUNT(ps.id) AS Total_Pagos,
                        SUM(ps.costo) AS Total_Costo,
                        SUM(IFNULL(ls.Comision, 0)) AS Total_Comision,
                        SUM(ps.costo + IFNULL(ls.Comision, 0)) AS Total_Con_Comision
                     FROM 
                        PagosServicios ps
                     LEFT JOIN 
                        ListadoServicios ls ON ps.Servicio = ls.Servicio
                     WHERE 
                        ps.Fk_Caja = '$fk_caja' 
                        AND ps.Fk_Sucursal = '$fk_sucursal'
                     GROUP BY 
                        ps.Servicio, ls.Comision";

            $query15 = $conn->query($sql15);
            if ($query15 && $query15->num_rows > 0) {
                while ($r = $query15->fetch_object()) {
                    $Especialistas15[] = $r;
                    // Agregar pago de servicio al array
                    $pagosServicios[] = [
                        'servicio' => $r->Servicio,
                        'cantidad' => $r->Total_Pagos,
                        'total_costo' => $r->Total_Costo,
                        'total_comision' => $r->Total_Comision,
                        'total_con_comision' => $r->Total_Con_Comision,
                    ];
                }
            }
        }

        // ================= CALCULAR TOTALES ADICIONALES POR FORMA DE PAGO ===================
        // Inicializar variables para totales adicionales
        $totales_abonos_efectivo = 0;
        $totales_abonos_tarjeta = 0;
        $totales_abonos_credito = 0;
        $totales_abonos_transferencia = 0;
        
        $totales_encargos_efectivo = 0;
        $totales_encargos_tarjeta = 0;
        $totales_encargos_credito = 0;
        $totales_encargos_transferencia = 0;
        
        $totales_pagos_servicios_efectivo = 0;
        $totales_pagos_servicios_tarjeta = 0;
        $totales_pagos_servicios_credito = 0;
        $totales_pagos_servicios_transferencia = 0;
        
        // Variables para comisiones de pagos de servicios por forma de pago
        $comisiones_pagos_servicios_efectivo = 0;
        $comisiones_pagos_servicios_tarjeta = 0;
        $comisiones_pagos_servicios_credito = 0;
        $comisiones_pagos_servicios_transferencia = 0;

        // Calcular totales de abonos por forma de pago
        if (!empty($abonos_dia)) {
            foreach ($abonos_dia as $abono) {
                $monto = floatval($abono['monto_abonado'] ?? 0);
                $forma_pago = strtolower(trim($abono['forma_pago'] ?? ''));
                
                if (strpos($forma_pago, 'efectivo') !== false || $forma_pago == 'efectivo') {
                    $totales_abonos_efectivo += $monto;
                } elseif (strpos($forma_pago, 'tarjeta') !== false || $forma_pago == 'tarjeta' || $forma_pago == 'tarjeta de credito' || $forma_pago == 'tarjeta de debito') {
                    $totales_abonos_tarjeta += $monto;
                } elseif (strpos($forma_pago, 'crédito') !== false || strpos($forma_pago, 'credito') !== false) {
                    $totales_abonos_credito += $monto;
                } elseif (strpos($forma_pago, 'transferencia') !== false || $forma_pago == 'transferencia') {
                    $totales_abonos_transferencia += $monto;
                } else {
                    // Si no coincide con ninguna, por defecto sumar a efectivo
                    $totales_abonos_efectivo += $monto;
                }
            }
        }

        // Calcular totales de encargos por forma de pago
        if (!empty($encargos_dia)) {
            foreach ($encargos_dia as $encargo) {
                $monto = floatval($encargo['abono_parcial'] ?? 0);
                $forma_pago = strtolower(trim($encargo['FormaDePago'] ?? ''));
                
                if (strpos($forma_pago, 'efectivo') !== false || $forma_pago == 'efectivo') {
                    $totales_encargos_efectivo += $monto;
                } elseif (strpos($forma_pago, 'tarjeta') !== false || $forma_pago == 'tarjeta' || $forma_pago == 'tarjeta de credito' || $forma_pago == 'tarjeta de debito') {
                    $totales_encargos_tarjeta += $monto;
                } elseif (strpos($forma_pago, 'crédito') !== false || strpos($forma_pago, 'credito') !== false) {
                    $totales_encargos_credito += $monto;
                } elseif (strpos($forma_pago, 'transferencia') !== false || $forma_pago == 'transferencia') {
                    $totales_encargos_transferencia += $monto;
                } else {
                    // Si no coincide con ninguna, por defecto sumar a efectivo
                    $totales_encargos_efectivo += $monto;
                }
            }
        }

        // Calcular totales de pagos de servicios por forma de pago (incluyendo comisiones)
        // Necesitamos una consulta adicional para obtener FormaDePago de PagosServicios con comisiones
        if ($result_check_pagos && $result_check_pagos->num_rows > 0) {
            $sql_pagos_servicios_totales = "SELECT 
                ps.FormaDePago,
                SUM(ps.costo) AS total_costo,
                SUM(IFNULL(ls.Comision, 0)) AS total_comision
            FROM PagosServicios ps
            LEFT JOIN ListadoServicios ls ON ps.Servicio = ls.Servicio
            WHERE ps.Fk_Caja = '$fk_caja' 
            AND ps.Fk_Sucursal = '$fk_sucursal'
            GROUP BY ps.FormaDePago";
            
            $query_pagos_totales = $conn->query($sql_pagos_servicios_totales);
            if ($query_pagos_totales && $query_pagos_totales->num_rows > 0) {
                while ($row_ps = $query_pagos_totales->fetch_assoc()) {
                    $monto_costo = floatval($row_ps['total_costo'] ?? 0);
                    $monto_comision = floatval($row_ps['total_comision'] ?? 0);
                    $forma_pago = strtolower(trim($row_ps['FormaDePago'] ?? ''));
                    
                    if (strpos($forma_pago, 'efectivo') !== false || $forma_pago == 'efectivo') {
                        $totales_pagos_servicios_efectivo += $monto_costo;
                        $comisiones_pagos_servicios_efectivo += $monto_comision;
                    } elseif (strpos($forma_pago, 'tarjeta') !== false || $forma_pago == 'tarjeta' || $forma_pago == 'tarjeta de credito' || $forma_pago == 'tarjeta de debito') {
                        $totales_pagos_servicios_tarjeta += $monto_costo;
                        $comisiones_pagos_servicios_tarjeta += $monto_comision;
                    } elseif (strpos($forma_pago, 'crédito') !== false || strpos($forma_pago, 'credito') !== false) {
                        $totales_pagos_servicios_credito += $monto_costo;
                        $comisiones_pagos_servicios_credito += $monto_comision;
                    } elseif (strpos($forma_pago, 'transferencia') !== false || $forma_pago == 'transferencia') {
                        $totales_pagos_servicios_transferencia += $monto_costo;
                        $comisiones_pagos_servicios_transferencia += $monto_comision;
                    } else {
                        // Si no coincide con ninguna, por defecto sumar a efectivo
                        $totales_pagos_servicios_efectivo += $monto_costo;
                        $comisiones_pagos_servicios_efectivo += $monto_comision;
                    }
                }
            }
        }

        // Sumar los totales adicionales a los totales principales
        $totalPagosEnEfectivo = $totalPagosEnEfectivo_base + $totales_abonos_efectivo + $totales_encargos_efectivo + $totales_pagos_servicios_efectivo;
        $totalPagosEnTarjeta = $totalPagosEnTarjeta_base + $totales_abonos_tarjeta + $totales_encargos_tarjeta + $totales_pagos_servicios_tarjeta;
        $totalPagosEnCreditos = $totalPagosEnCreditos_base + $totales_abonos_credito + $totales_encargos_credito + $totales_pagos_servicios_credito;
        $totalPagosEnTransferencia = $totalPagosEnTransferencia_base + $totales_abonos_transferencia + $totales_encargos_transferencia + $totales_pagos_servicios_transferencia;

        // Actualizar TotalCantidad sumando los montos de abonos, encargos y pagos de servicios
        $TotalCantidad = $TotalCantidad_base;
        
        // Sumar totales de abonos
        foreach ($abonos_dia as $abono) {
            $TotalCantidad += floatval($abono['monto_abonado'] ?? 0);
        }
        
        // Sumar abonos parciales de encargos del día
        foreach ($encargos_dia as $encargo) {
            $TotalCantidad += floatval($encargo['abono_parcial'] ?? 0);
        }
        
        // Sumar totales de pagos de servicios (costo + comisión)
        foreach ($pagosServicios as $pago) {
            $TotalCantidad += floatval($pago['total_con_comision'] ?? $pago['total_costo'] ?? 0);
        }

        // Calcular el total de venta completo (incluyendo pagos de servicios con comisión, abonos y encargos)
        $VentaTotalCompleto = floatval($Especialistas3->VentaTotal ?? 0);
        
        // Sumar totales de abonos
        foreach ($abonos_dia as $abono) {
            $VentaTotalCompleto += floatval($abono['monto_abonado'] ?? 0);
        }
        
        // Sumar abonos parciales de encargos del día
        foreach ($encargos_dia as $encargo) {
            $VentaTotalCompleto += floatval($encargo['abono_parcial'] ?? 0);
        }
        
        // Sumar totales de pagos de servicios (costo + comisión)
        foreach ($pagosServicios as $pago) {
            $VentaTotalCompleto += floatval($pago['total_con_comision'] ?? $pago['total_costo'] ?? 0);
        }
        
        // Calcular comisiones totales por forma de pago
        $comision_total_efectivo = $comisiones_pagos_servicios_efectivo;
        $comision_total_tarjeta = $comisiones_pagos_servicios_tarjeta;
        $comision_total_creditos = $comisiones_pagos_servicios_credito;
        $comision_total_transferencia = $comisiones_pagos_servicios_transferencia;
        
        // Calcular totales con comisión por forma de pago
        $totalPagosEnEfectivoConComision = $totalPagosEnEfectivo + $comision_total_efectivo;
        $totalPagosEnTarjetaConComision = $totalPagosEnTarjeta + $comision_total_tarjeta;
        $totalPagosEnCreditosConComision = $totalPagosEnCreditos + $comision_total_creditos;
        $totalPagosEnTransferenciaConComision = $totalPagosEnTransferencia + $comision_total_transferencia;

        ?>

        <!-- Manejo de errores -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Mantener todo el HTML original -->
        <form action="javascript:void(0)" method="post" id="FormDeCortes">
            <div class="text-center">
                <h5 class="text-center mt-3">Datos de caja</h5> 
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="exampleFormControlInput1">Sucursal</label>
                            <input type="text" class="form-control" id="cantidadtotalventasss" step="any" readonly 
                                   value="<?= htmlspecialchars($nombre_sucursal) ?>" 
                                   aria-describedby="basic-addon1">
                            <input type="text" hidden name="Fk_Caja" value="<?= $fk_caja ?>">
                            <input type="text" hidden name="Sucursal" value="<?= $fk_sucursal ?>">
                            <input type="text" hidden name="Turno" value="<?= $Especialistas3->Turno ?? '' ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="exampleFormControlInput1">Cajero</label>
                            <input type="text" class="form-control" id="cantidadtotalventassss" name="Cajero" step="any" readonly 
                                   value="<?= htmlspecialchars($Especialistas3->AgregadoPor ?? 'N/A') ?>" aria-describedby="basic-addon1">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="exampleFormControlInput1">Total de venta</label>
                            <input type="number" class="form-control" id="cantidadtotalventassss" name="VentaTotal" step="any" readonly 
                                   value="<?= $VentaTotalCompleto ?>" aria-describedby="basic-addon1">
                        </div>

                        <div class="col-md-6">
                            <label for="exampleFormControlInput1">Total de tickets</label>
                            <input type="text" class="form-control" id="cantidadtotalventassss" name="TicketVentasTotal" step="any" readonly 
                                   value="<?= $Especialistas3->Total_tickets ?? 0 ?>" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>

              <!-- Mostrar la tabla de servicios si hay datos -->
        <?php if (!empty($Especialistas14)) : ?>
        <div class="table-responsive">
            <table id="TotalesGeneralesCortes" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre Servicio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Especialistas14 as $especialista) : ?>
                    <tr>
                        <td><input type="text" class="form-control" readonly value="<?= htmlspecialchars($especialista->Nom_Serv) ?>"></td>
                        <td><input type="text" class="form-control" readonly value="<?= $especialista->totaldeservicios ?? 0 ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="alert alert-info">
            <?php if (!empty($Especialistas15)): ?>
                No se encontraron servicios de ventas POS para mostrar, pero hay <?= count($Especialistas15) ?> tipo(s) de pago(s) de servicios registrado(s). 
                Revisa la sección "Pagos de servicios" en el acordeón para ver el detalle.
            <?php else: ?>
                No se encontraron servicios de ventas POS para mostrar.
            <?php endif; ?>
        </p>
        <?php endif; ?>

        <!-- Campo oculto con el valor de servicios -->
        <input type="hidden" name="servicios" value='<?= json_encode($servicios) ?>'>
        <!-- Campo oculto con el valor de gastos -->
        <input type="hidden" name="gastos" value='<?= json_encode($gastos_array) ?>'>
        <!-- Campo oculto con el valor de abonos -->
        <input type="hidden" name="abonos" value='<?= json_encode($abonos_dia) ?>'>
        <!-- Campo oculto con el valor de encargos -->
        <input type="hidden" name="encargos" value='<?= json_encode($encargos_dia) ?>'>
        <!-- Campo oculto con el valor de pagos de servicios -->
        <input type="hidden" name="pagos_servicios" value='<?= json_encode($pagosServicios) ?>'>

            <!-- Tabla de totales -->
            <div class="text-center">
                <div class="table-responsive">
                    <table id="TotalesFormaPagoCortes" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Forma de pago</th>
                                <th>Total</th>
                                <th>Comisión</th>
                                <th>Total con Comisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Efectivo"></td>
                                <td><input type="text" class="form-control" name="EfectivoTotal" readonly value="<?= number_format($totalPagosEnEfectivo, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($comision_total_efectivo, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($totalPagosEnEfectivoConComision, 2) ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Tarjeta"></td>
                                <td><input type="text" class="form-control" name="TarjetaTotal" readonly value="<?= number_format($totalPagosEnTarjeta, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($comision_total_tarjeta, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($totalPagosEnTarjetaConComision, 2) ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Créditos"></td>
                                <td><input type="text" class="form-control" name="CreditosTotales" readonly value="<?= number_format($totalPagosEnCreditos, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($comision_total_creditos, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($totalPagosEnCreditosConComision, 2) ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" readonly value="Transferencia"></td>
                                <td><input type="text" class="form-control" name="TotalTransferencias" readonly value="<?= number_format($totalPagosEnTransferencia, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($comision_total_transferencia, 2) ?>"></td>
                                <td><input type="text" class="form-control" readonly value="<?= number_format($totalPagosEnTransferenciaConComision, 2) ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mensaje para el usuario sobre el acordeón -->
            <div class="alert alert-info text-center" role="alert">
              Puedes hacer <strong>clic en cada sección</strong> para mostrar u ocultar el desglose de información.
            </div>

            <div class="accordion mb-4" id="acordeonCorte">
              <!-- Sección de Gastos -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingGastos">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGastos" aria-expanded="true" aria-controls="collapseGastos">
                    Gastos del día
                  </button>
                </h2>
                <div id="collapseGastos" class="accordion-collapse collapse show" aria-labelledby="headingGastos" data-bs-parent="#acordeonCorte">
                  <div class="accordion-body">
                    <!-- Tabla de gastos -->
                    <div class="table-responsive">
                      <table id="TablaGastos" class="table table-hover">
                        <thead>
                          <tr>
                            <th>Concepto</th>
                            <th>Importe</th>
                            <th>Recibe</th>
                            <th>Fecha</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($gastos)): ?>
                            <?php foreach ($gastos as $gasto): ?>
                              <tr>
                                <td><?= htmlspecialchars($gasto['Concepto_Categoria']) ?></td>
                                <td>$<?= number_format($gasto['Importe_Total'], 2) ?></td>
                                <td><?= htmlspecialchars($gasto['Recibe']) ?></td>
                                <td><?= date('d/m/Y', strtotime($gasto['FechaConcepto'])) ?></td>
                              </tr>
                            <?php endforeach; ?>
                            <tr class="table-info">
                              <td colspan="1"><strong>Total Gastos:</strong></td>
                              <td colspan="3"><strong>$<?= number_format($total_gastos, 2) ?></strong></td>
                            </tr>
                          <?php else: ?>
                            <tr>
                              <td colspan="4" class="text-center">No hay gastos registrados</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Sección de Abonos -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingAbonos">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAbonos" aria-expanded="false" aria-controls="collapseAbonos">
                    Abonos a encargos realizados hoy
                  </button>
                </h2>
                <div id="collapseAbonos" class="accordion-collapse collapse" aria-labelledby="headingAbonos" data-bs-parent="#acordeonCorte">
                  <div class="accordion-body">
                    <!-- Tabla de abonos de encargos del día -->
                    <div class="table-responsive">
                      <table id="TablaAbonosEncargos" class="table table-hover">
                        <thead>
                          <tr>
                            <th>Paciente</th>
                            <th>Medicamento</th>
                            <th>Cantidad</th>
                            <th>Monto abonado</th>
                            <th>Forma de pago</th>
                            <th>Empleado</th>
                            <th>Fecha y hora</th>
                            <th>Observaciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($abonos_dia)): ?>
                            <?php foreach ($abonos_dia as $abono): ?>
                              <tr>
                                <td><?= htmlspecialchars($abono['nombre_paciente']) ?></td>
                                <td><?= htmlspecialchars($abono['medicamento']) ?></td>
                                <td><?= htmlspecialchars($abono['cantidad']) ?></td>
                                <td>$<?= number_format($abono['monto_abonado'], 2) ?></td>
                                <td><?= htmlspecialchars($abono['forma_pago']) ?></td>
                                <td><?= htmlspecialchars($abono['empleado']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($abono['fecha_abono'])) ?></td>
                                <td><?= htmlspecialchars($abono['observaciones']) ?></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr><td colspan="8" class="text-center">No hay abonos registrados hoy.</td></tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Sección de Encargos del día -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingEncargosDia">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEncargosDia" aria-expanded="false" aria-controls="collapseEncargosDia">
                    Encargos realizados hoy
                  </button>
                </h2>
                <div id="collapseEncargosDia" class="accordion-collapse collapse" aria-labelledby="headingEncargosDia" data-bs-parent="#acordeonCorte">
                  <div class="accordion-body">
                    <!-- Tabla de encargos hechos hoy -->
                    <div class="table-responsive">
                      <table id="TablaEncargosDia" class="table table-hover">
                        <thead>
                          <tr>
                            <th>Paciente</th>
                            <th>Medicamento</th>
                            <th>Cantidad</th>
                            <th>Precio venta</th>
                            <th>Abono realizado</th>
                            <th>Estado</th>
                            <th>Fecha y hora</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($encargos_dia)): ?>
                            <?php foreach ($encargos_dia as $encargo): ?>
                              <tr>
                                <td><?= htmlspecialchars($encargo['nombre_paciente']) ?></td>
                                <td><?= htmlspecialchars($encargo['medicamento']) ?></td>
                                <td><?= htmlspecialchars($encargo['cantidad']) ?></td>
                                <td>$<?= number_format($encargo['precioventa'], 2) ?></td>
                                <td>$<?= number_format($encargo['abono_parcial'], 2) ?></td>
                                <td><?= htmlspecialchars($encargo['estado']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($encargo['fecha_encargo'])) ?></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr><td colspan="7" class="text-center">No hay encargos registrados hoy.</td></tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Sección de Pagos de Servicios -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingPagosServicios">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagosServicios" aria-expanded="false" aria-controls="collapsePagosServicios">
                    Pagos de servicios
                  </button>
                </h2>
                <div id="collapsePagosServicios" class="accordion-collapse collapse" aria-labelledby="headingPagosServicios" data-bs-parent="#acordeonCorte">
                  <div class="accordion-body">
                    <!-- Tabla de pagos de servicios -->
                    <div class="table-responsive">
                      <table id="TablaPagosServicios" class="table table-hover">
                        <thead>
                          <tr>
                            <th>Servicio</th>
                            <th>Cantidad</th>
                            <th>Total Costo</th>
                            <th>Total Comisión</th>
                            <th>Total (Costo + Comisión)</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($Especialistas15)): ?>
                            <?php 
                            $total_general_costo = 0;
                            $total_general_comision = 0;
                            $total_general_con_comision = 0;
                            foreach ($Especialistas15 as $pagoServicio): 
                              $total_general_costo += $pagoServicio->Total_Costo;
                              $total_general_comision += $pagoServicio->Total_Comision;
                              $total_general_con_comision += $pagoServicio->Total_Con_Comision;
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($pagoServicio->Servicio) ?></td>
                                <td><?= $pagoServicio->Total_Pagos ?></td>
                                <td>$<?= number_format($pagoServicio->Total_Costo, 2) ?></td>
                                <td>$<?= number_format($pagoServicio->Total_Comision, 2) ?></td>
                                <td>$<?= number_format($pagoServicio->Total_Con_Comision, 2) ?></td>
                              </tr>
                            <?php endforeach; ?>
                            <tr class="table-info">
                              <td colspan="2"><strong>Total General:</strong></td>
                              <td><strong>$<?= number_format($total_general_costo, 2) ?></strong></td>
                              <td><strong>$<?= number_format($total_general_comision, 2) ?></strong></td>
                              <td><strong>$<?= number_format($total_general_con_comision, 2) ?></strong></td>
                            </tr>
                          <?php else: ?>
                            <tr><td colspan="5" class="text-center">No hay pagos de servicios registrados.</td></tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Campos ocultos y observaciones -->
            <input type="hidden" name="Sistema" value="Ventas">
            <input type="hidden" name="ID_H_O_D" value="DoctorPez">
            <input type="hidden" name="total_gastos" value="<?= $total_gastos ?>">
            <input type="hidden" name="FechaDelCorte" value="<?= date('Y-m-d') ?>">
            
            <label for="comentarios">Observaciones:</label>
            <textarea class="form-control" id="comentarios" name="comentarios" rows="4" cols="50" placeholder="Escribe tu comentario aquí..."></textarea>
            <br>
            
            <!-- Botón de realizar corte -->
            <button type="submit" id="submit" class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
        </div>
        </form>

        <script src="js/RealizaCorteDeCaja.js"></script>
        <script>
        document.getElementById('FormDeCortes').addEventListener('submit', function(e) {
            const total = <?= $TotalCantidad ?>;
            
            if (total <= 0) {
                if (!confirm('¡ADVERTENCIA! Estás realizando un corte con $0 en ventas. ¿Deseas continuar?')) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Aquí iría la lógica original de envío
        });
        </script>

        <?php
            } // Cierre del else
        } catch (Exception $e) {
            $error_message = "Error en el procesamiento: " . $e->getMessage();
        }
        ?>
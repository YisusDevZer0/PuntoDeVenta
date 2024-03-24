<?php
    include_once 'db_connect.php';

    $CodBarraP = mysqli_real_escape_string($conn, $_POST['CodBarraP']);
    echo "CodBarraP: " . $CodBarraP . "<br>";

    $Clav = mysqli_real_escape_string($conn, $_POST['Clav']);
    echo "Clav: " . $Clav . "<br>";

    $NombreProd = mysqli_real_escape_string($conn, $_POST['NombreProd']);
    echo "NombreProd: " . $NombreProd . "<br>";

    $ComponenteActivo = mysqli_real_escape_string($conn, $_POST['ComponenteActivo']);
    echo "ComponenteActivo: " . $ComponenteActivo . "<br>";

    $PV = mysqli_real_escape_string($conn, $_POST['PV']);
    echo "PV: " . $PV . "<br>";

    $PC = mysqli_real_escape_string($conn, $_POST['PC']);
    echo "PC: " . $PC . "<br>";

    $TipoServicio = mysqli_real_escape_string($conn, $_POST['TipoServicio']);
    echo "TipoServicio: " . $TipoServicio . "<br>";

    $Receta = mysqli_real_escape_string($conn, $_POST['Receta']);
    echo "Receta: " . $Receta . "<br>";

    $Tip = mysqli_real_escape_string($conn, $_POST['Tip']);
    echo "Tip: " . $Tip . "<br>";

    $Categoria = mysqli_real_escape_string($conn, $_POST['Categoria']);
    echo "Categoria: " . $Categoria . "<br>";

    $Marca = mysqli_real_escape_string($conn, $_POST['Marca']);
    echo "Marca: " . $Marca . "<br>";

    $Presentacion = mysqli_real_escape_string($conn, $_POST['Presentacion']);
    echo "Presentacion: " . $Presentacion . "<br>";

    $Proveedor = mysqli_real_escape_string($conn, $_POST['Proveedor']);
    echo "Proveedor: " . $Proveedor . "<br>";

    $Prov2 = mysqli_real_escape_string($conn, $_POST['Prov2']);
    echo "Prov2: " . $Prov2 . "<br>";

    $EmpresaProductos = mysqli_real_escape_string($conn, $_POST['EmpresaProductos']);
    echo "EmpresaProductos: " . $EmpresaProductos . "<br>";

    $AgregaProductosBy = mysqli_real_escape_string($conn, $_POST['AgregaProductosBy']);
    echo "AgregaProductosBy: " . $AgregaProductosBy . "<br>";

    $SistemaProductos = mysqli_real_escape_string($conn, $_POST['SistemaProductos']);
    echo "SistemaProductos: " . $SistemaProductos . "<br>";



?>

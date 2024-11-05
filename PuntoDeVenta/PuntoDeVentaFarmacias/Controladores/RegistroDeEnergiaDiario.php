<?php
include_once 'db_connect.php';

if (!empty($_POST['name']) || !empty($_FILES['file']['name'])) {
    $uploadedFile = '';

    if (isset($_FILES["file"]["type"])) {
        $fileName = '';
        if (isset($_FILES['file']['name'])) {
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $valid_extensions = array("jpeg", "jpg", "png");
            $temporary = explode(".", $_FILES["file"]["name"]);
            $file_extension = end($temporary);

            // Verifica si el tipo y la extensión del archivo son válidos
            if (
                (
                    ($_FILES["file"]["type"] == "image/png") ||
                    ($_FILES["file"]["type"] == "image/jpg") ||
                    ($_FILES["file"]["type"] == "image/jpeg")
                ) && in_array($file_extension, $valid_extensions)
            ) {
                // Ruta de destino en el servidor
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/PuntoDeVenta/FotosMedidores/";
                $targetPath = $targetDir . $fileName;
                
                // Verifica que la carpeta de destino tenga permisos de escritura
                if (!is_dir($targetDir) || !is_writable($targetDir)) {
                    echo json_encode(array("statusCode" => 500, "message" => "Directorio de destino no tiene permisos de escritura."));
                    exit();
                }

                // Intenta mover el archivo
                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                    $uploadedFile = $fileName;
                } else {
                    echo json_encode(array("statusCode" => 500, "message" => "Error al subir el archivo."));
                    exit();
                }
            } else {
                echo json_encode(array("statusCode" => 400, "message" => "Tipo de archivo no permitido."));
                exit();
            }
        }

        // Escapa y limpia los datos recibidos
        $Registro_Watts = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['RegistroEnergia']))));
        $Fecha_registro = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Fecha']))));
        $Sucursal = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Sucursal']))));
        $Comentario = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Comentario']))));
        $Registro = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Registro']))));
        $Licencia = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Empresa']))));

        // Consulta para comprobar si ya existe un registro similar
        $sql = "SELECT Registro_Watts, Fecha_registro, Registro FROM Registros_Energia 
                WHERE Registro_Watts='$Registro_Watts' AND Fecha_registro='$Fecha_registro' AND Registro='$Registro'";
        $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
        $row = mysqli_fetch_assoc($resultset);

        if ($row) {
            echo json_encode(array("statusCode" => 250));
        } else {
            $sql = "INSERT INTO `Registros_Energia`(`Registro_Watts`, `Fecha_registro`, `Sucursal`, `Comentario`, `Registro`, `Licencia`, `file_name`) 
                    VALUES ('$Registro_Watts', '$Fecha_registro', '$Sucursal', '$Comentario', '$Registro', '$Licencia', '$uploadedFile')";

            if (mysqli_query($conn, $sql)) {
                echo json_encode(array("statusCode" => 200));
            } else {
                echo json_encode(array("statusCode" => 201));
            }
        }

        mysqli_close($conn);
    }
}
?>

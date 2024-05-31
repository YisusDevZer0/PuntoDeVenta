<?php

function buscarArticulo($codigoEscaneado) {
    $url = "https://www.levicventas.mx/busqueda?codigo=" . urlencode($codigoEscaneado); // Reemplaza esto con la URL correcta y los parámetros de búsqueda

    // Inicializar cURL
    $ch = curl_init();

    // Establecer opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36");

    // Ejecutar la solicitud cURL
    $response = curl_exec($ch);

    // Manejar errores
    if ($response === false) {
        echo "Error de cURL: " . curl_error($ch);
        return false;
    }

    // Analizar el HTML de la respuesta y extraer la información necesaria
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $xpath = new DOMXPath($dom);

    // Aquí debes escribir la lógica para extraer los datos específicos que necesitas del HTML
    // Ejemplo de cómo extraer todos los enlaces
    $articulos = [];
    foreach ($xpath->query("//a") as $node) {
        $articulos[] = $node->getAttribute("href");
    }

    return $articulos;
}

// Ejemplo de uso
$codigoEscaneado = "123456";
$response = buscarArticulo($codigoEscaneado);

echo "<pre>";
print_r($response);
echo "</pre>";

?>

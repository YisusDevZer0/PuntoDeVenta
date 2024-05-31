<?php

function buscarArticulo($codigoEscaneado) {
    $url = "https://www.levicventas.mx/frm_ProductoDetalle.aspx?codigo=" . urlencode($codigoEscaneado);

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
    // Por ejemplo, si quieres extraer el nombre del producto:
    $producto = [];
    $nombreNodo = $xpath->query("//span[@id='nombreProducto']"); // Cambia esto por el selector adecuado
    if ($nombreNodo->length > 0) {
        $producto['nombre'] = $nombreNodo->item(0)->nodeValue;
    }

    $precioNodo = $xpath->query("//span[@id='precioProducto']"); // Cambia esto por el selector adecuado
    if ($precioNodo->length > 0) {
        $producto['precio'] = $precioNodo->item(0)->nodeValue;
    }

    // Agrega más selectores y lógica según lo que necesites extraer
    // ...

    return $producto;
}

// Ejemplo de uso
$codigoEscaneado = "BEA364";
$producto = buscarArticulo($codigoEscaneado);

echo "<pre>";
print_r($producto);
echo "</pre>";

?>

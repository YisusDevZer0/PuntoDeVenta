<?php
// Test simple de la API
$query = 'test';
$formData = new FormData();
$formData->append('query', $query);

echo '<h1>Test de API de Búsqueda</h1>';
echo '<p>Query: ' . $query . '</p>';

// Simular la llamada
$_POST['query'] = $query;

include 'api/buscar_productos_simple.php';
?>

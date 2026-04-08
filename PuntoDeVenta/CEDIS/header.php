<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/app.php';
}
?>
<script>window.__FDP_BASE_URL__=<?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="../js/fdp-url.js"></script>
<!-- Favicon -->
<link href="img/favicon.ico" rel="icon">

<!-- Google Web Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Icon Fonts -->
<script src="https://kit.fontawesome.com/a337b4cc32.js" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Libraries Stylesheets -->
<link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
<link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">

<!-- Bootstrap Stylesheet -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="css/style.css" rel="stylesheet">

<!-- Other Stylesheets -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- Scripts --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="js/validation.min.js"></script>
<link rel="stylesheet" href="styles.css"> <!-- Si tienes un archivo CSS -->
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>favicon.svg" />
    <link rel="shortcut icon" href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Punto de venta" />
    <script>
  // Función para detectar si es un dispositivo móvil
  function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  }

  // Si es un dispositivo móvil, agregar el Manifest dinámicamente
  if (isMobileDevice()) {
    const link = document.createElement('link');
    link.rel = 'manifest';
    link.href = <?= json_encode(BASE_PATH . 'PuntoDeVentaFarmacias/manifest.json', JSON_UNESCAPED_SLASHES) ?>;
    document.head.appendChild(link);
  }
</script>
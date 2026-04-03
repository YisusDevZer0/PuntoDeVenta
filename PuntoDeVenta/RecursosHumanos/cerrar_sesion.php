<?php

require_once __DIR__ . '/../config/app.php';
session_start();
setcookie ("mostrarModal", "", time() - 3600);
session_unset();
session_destroy();


header('Location: ' . fdp_url(''));
?>
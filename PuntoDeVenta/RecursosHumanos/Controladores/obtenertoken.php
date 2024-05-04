<?php
function obtener_token_csrf() {
    return bin2hex(random_bytes(32));
}
?>
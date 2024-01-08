<?
# Iniciando la variable de control que permitirá mostrar o no el modal
$exibirModal = false;
# Verificando si existe o no la cookie
if(!isset($_COOKIE["Servicios"]))
{
  # Caso no exista la cookie entra aquí
  # Creamos la cookie con la duración que queramos
   
  //$expirar = 3600; // muestra cada 1 hora
  //$expirar = 10800; // muestra cada 3 horas
  //$expirar = 21600; //muestra cada 6 horas
  $expirar = 43200; //muestra cada 12 horas
  //$expirar = 86400;  // muestra cada 24 horas
  setcookie('Servicios', 'SI', (time() + $expirar)); // mostrará cada 12 horas.
  # Ahora nuestra variable de control pasará a tener el valor TRUE (Verdadero)
  $MostrarMensaje = true;
}
?>
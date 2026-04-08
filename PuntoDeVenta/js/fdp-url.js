/**
 * Usar con window.__FDP_BASE_URL__ (definido en header.php vía PHP).
 * Evita errores al concatenar rutas con el subpath del sitio (p. ej. /PuntoDeVenta/).
 */
(function (w) {
  w.fdpUrl = function (path) {
    var p = String(path || '').replace(/^\//, '');
    var b = (w.__FDP_BASE_URL__ || '').replace(/\/?$/, '/');
    return b ? b + p : p;
  };
})(window);

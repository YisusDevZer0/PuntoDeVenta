// Función para cargar datos de usuarios (mantenida para compatibilidad)
function CargaServicios(){
    // Esta función se mantiene para compatibilidad con el sistema anterior
    // El nuevo sistema usa DataTables directamente
    console.log("Función CargaServicios llamada - usando nuevo sistema DataTables");
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    console.log("ControlDeUsuarios.js inicializado");
    
    // Si estamos en la página de PersonalActivo.php, no hacer nada aquí
    // ya que el nuevo sistema se maneja en PersonalActivo.js
    if (window.location.pathname.includes('PersonalActivo.php')) {
        console.log("En PersonalActivo.php - usando nuevo sistema");
        return;
    }
    
    // Para otras páginas que usen el sistema anterior
    CargaServicios();
});

  
  
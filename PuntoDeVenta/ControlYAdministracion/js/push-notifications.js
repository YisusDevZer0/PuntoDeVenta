// Variables globales
let swRegistration = null;

// Comprobar si el navegador soporta notificaciones push
function verificarSoportePush() {
  // Detectar navegador y dispositivo
  const userAgent = navigator.userAgent || navigator.vendor || window.opera;
  const isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
  const isSafari = /^((?!chrome|android).)*safari/i.test(userAgent);
  const isMac = /Macintosh/.test(userAgent);
  
  // Verificar soporte nativo
  const soportado = 'serviceWorker' in navigator && 'PushManager' in window;
  
  if (!soportado) {
    if (isIOS) {
      console.warn('Las notificaciones push web no son soportadas en iOS por Safari. Considera usar una PWA o app nativa.');
      mostrarMensajeNoSoportado('iOS no soporta notificaciones push web en Safari. Instala nuestra app para recibir notificaciones.');
    } else if (isSafari && isMac) {
      console.warn('Safari en macOS puede tener soporte limitado para notificaciones push.');
      mostrarMensajeNoSoportado('Safari tiene soporte limitado para notificaciones push. Considera usar Chrome o Firefox.');
    } else {
      console.warn('Este navegador no soporta notificaciones push');
      mostrarMensajeNoSoportado('Tu navegador no soporta notificaciones push. Actualiza o usa un navegador moderno.');
    }
    return false;
  }
  
  console.log('El navegador soporta notificaciones push');
  return true;
}

// Mostrar mensaje cuando no hay soporte
function mostrarMensajeNoSoportado(mensaje) {
  // Crear un elemento div para el mensaje
  const div = document.createElement('div');
  div.style.position = 'fixed';
  div.style.bottom = '20px';
  div.style.left = '20px';
  div.style.right = '20px';
  div.style.padding = '10px';
  div.style.backgroundColor = '#f8d7da';
  div.style.color = '#721c24';
  div.style.borderRadius = '5px';
  div.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
  div.style.zIndex = '9999';
  div.style.textAlign = 'center';
  div.textContent = mensaje;
  
  // Botón de cerrar
  const close = document.createElement('button');
  close.textContent = '×';
  close.style.position = 'absolute';
  close.style.right = '10px';
  close.style.top = '5px';
  close.style.background = 'none';
  close.style.border = 'none';
  close.style.fontSize = '20px';
  close.style.cursor = 'pointer';
  close.onclick = function() { div.remove(); };
  
  div.appendChild(close);
  document.body.appendChild(div);
  
  // Ocultar después de 10 segundos
  setTimeout(() => div.remove(), 10000);
}

// Registrar el Service Worker
async function registrarServiceWorker() {
  try {
    // La ruta debe ser relativa a la raíz del sitio - ajustada para asegurar que funcione
    const scope = '/'; 
    swRegistration = await navigator.serviceWorker.register('/service-worker.js', { scope });
    console.log('Service Worker registrado correctamente', swRegistration);
    return swRegistration;
  } catch (error) {
    console.error('Error al registrar el Service Worker:', error);
    
    // Intentar con una ruta alternativa
    try {
      swRegistration = await navigator.serviceWorker.register('./service-worker.js');
      console.log('Service Worker registrado en ruta alternativa', swRegistration);
      return swRegistration;
    } catch (secondError) {
      console.error('Error al registrar el Service Worker (segundo intento):', secondError);
      mostrarMensajeNoSoportado('No se pudo registrar el servicio de notificaciones. Intenta actualizar la página.');
      return null;
    }
  }
}

// Solicitar permiso para notificaciones
async function solicitarPermisoNotificaciones() {
  try {
    // Verificar si ya tenemos permiso
    if (Notification.permission === 'granted') {
      return true;
    }
    
    if (Notification.permission === 'denied') {
      mostrarMensajeNoSoportado('Has bloqueado las notificaciones. Debes cambiar la configuración de tu navegador para recibir alertas.');
      return false;
    }
    
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      console.log('Permiso para notificaciones concedido');
      return true;
    } else {
      console.warn('Permiso para notificaciones denegado');
      mostrarMensajeNoSoportado('Sin permiso para notificaciones. No podrás recibir alertas importantes.');
      return false;
    }
  } catch (error) {
    console.error('Error al solicitar permiso:', error);
    return false;
  }
}

// Suscribir al usuario a notificaciones push
async function suscribirUsuario() {
  try {
    // Verificar si ya estamos suscritos
    const subscription = await swRegistration.pushManager.getSubscription();
    if (subscription) {
      console.log('Usuario ya suscrito', subscription);
      // Actualizar la suscripción en el servidor
      await guardarSuscripcion(subscription);
      return subscription;
    }
    
    // Obtener la clave pública desde el servidor
    const response = await fetch('./api/get_vapid_public_key.php');
    const data = await response.json();
    
    if (!data.success || !data.publicKey) {
      throw new Error('No se pudo obtener la clave pública: ' + (data.message || 'Error desconocido'));
    }
    
    const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
    
    // Intentar suscribir al usuario
    const newSubscription = await swRegistration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: applicationServerKey
    });
    
    console.log('Usuario suscrito:', newSubscription);
    
    // Enviar la suscripción al servidor
    await guardarSuscripcion(newSubscription);
    
    // Mostrar mensaje de éxito
    mostrarMensajeExito('Te has suscrito correctamente a las notificaciones.');
    
    return newSubscription;
  } catch (error) {
    console.error('Error al suscribir al usuario:', error);
    mostrarMensajeNoSoportado('Error al suscribir a notificaciones: ' + error.message);
    return null;
  }
}

// Mostrar mensaje de éxito
function mostrarMensajeExito(mensaje) {
  const div = document.createElement('div');
  div.style.position = 'fixed';
  div.style.bottom = '20px';
  div.style.left = '20px';
  div.style.right = '20px';
  div.style.padding = '10px';
  div.style.backgroundColor = '#d4edda';
  div.style.color = '#155724';
  div.style.borderRadius = '5px';
  div.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
  div.style.zIndex = '9999';
  div.style.textAlign = 'center';
  div.textContent = mensaje;
  
  // Botón de cerrar
  const close = document.createElement('button');
  close.textContent = '×';
  close.style.position = 'absolute';
  close.style.right = '10px';
  close.style.top = '5px';
  close.style.background = 'none';
  close.style.border = 'none';
  close.style.fontSize = '20px';
  close.style.cursor = 'pointer';
  close.onclick = function() { div.remove(); };
  
  div.appendChild(close);
  document.body.appendChild(div);
  
  // Ocultar después de 5 segundos
  setTimeout(() => div.remove(), 5000);
}

// Guardar la suscripción en el servidor
async function guardarSuscripcion(subscription) {
  try {
    const response = await fetch('./api/guardar_suscripcion.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        subscription: subscription,
        user_id: getUserID() // Función que obtiene el ID del usuario actual
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Suscripción guardada correctamente');
      return true;
    } else {
      console.error('Error al guardar la suscripción:', data.message);
      return false;
    }
  } catch (error) {
    console.error('Error al guardar la suscripción:', error);
    return false;
  }
}

// Utilidad para convertir una clave base64 a Uint8Array
function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

// Función para obtener el ID del usuario actual
function getUserID() {
  // Intentar obtener el ID de usuario de diferentes fuentes
  return localStorage.getItem('user_id') || 
         sessionStorage.getItem('user_id') || 
         document.getElementById('user-id')?.value || 
         '1';
}

// Inicializar el sistema de notificaciones push
async function inicializarNotificacionesPush() {
  // Verificar soporte antes de intentar registrar
  if (!verificarSoportePush()) {
    return false;
  }
  
  // Registrar service worker
  const swReg = await registrarServiceWorker();
  if (!swReg) {
    return false;
  }
  
  // Solicitar permiso
  const permisoOtorgado = await solicitarPermisoNotificaciones();
  if (!permisoOtorgado) {
    return false;
  }
  
  // Suscribir al usuario
  return await suscribirUsuario();
}

// Configurar botón de suscripción (para móviles)
document.addEventListener('DOMContentLoaded', function() {
  const botonSuscribir = document.getElementById('boton-suscribir-notificaciones');
  
  if (botonSuscribir) {
    botonSuscribir.addEventListener('click', function() {
      inicializarNotificacionesPush()
        .then(result => {
          if (result) {
            // Ocultar botón después de suscripción exitosa
            document.getElementById('contenedor-boton-push').style.display = 'none';
          }
        });
    });
  }
});

// Verificar automáticamente en carga de página sin solicitar permisos
document.addEventListener('DOMContentLoaded', function() {
  verificarSoportePush();
});

// Exponer funciones
window.inicializarNotificacionesPush = inicializarNotificacionesPush;
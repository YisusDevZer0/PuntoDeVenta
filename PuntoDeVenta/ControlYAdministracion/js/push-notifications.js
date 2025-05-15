// Variables globales
let swRegistration = null;

// Comprobar si el navegador soporta notificaciones push
function verificarSoportePush() {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    console.log('El navegador soporta notificaciones push');
    return true;
  } else {
    console.warn('El navegador NO soporta notificaciones push');
    return false;
  }
}

// Registrar el Service Worker
async function registrarServiceWorker() {
  try {
    // La ruta debe ser relativa a la raíz del sitio
    swRegistration = await navigator.serviceWorker.register('./service-worker.js');
    console.log('Service Worker registrado correctamente', swRegistration);
    return swRegistration;
  } catch (error) {
    console.error('Error al registrar el Service Worker:', error);
    return null;
  }
}

// Solicitar permiso para notificaciones
async function solicitarPermisoNotificaciones() {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      console.log('Permiso para notificaciones concedido');
      return true;
    } else {
      console.warn('Permiso para notificaciones denegado');
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
    // Aquí se obtendría la clave pública desde el servidor
    const response = await fetch('./api/get_vapid_public_key.php');
    const data = await response.json();
    
    if (!data.publicKey) {
      throw new Error('No se pudo obtener la clave pública');
    }
    
    const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
    
    const subscription = await swRegistration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: applicationServerKey
    });
    
    console.log('Usuario suscrito:', subscription);
    
    // Enviar la suscripción al servidor
    await guardarSuscripcion(subscription);
    
    return subscription;
  } catch (error) {
    console.error('Error al suscribir al usuario:', error);
    return null;
  }
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
  // Implementación según tu sistema de autenticación
  return localStorage.getItem('user_id') || sessionStorage.getItem('user_id') || '1';
}

// Inicializar el sistema de notificaciones push
async function inicializarNotificacionesPush() {
  if (!verificarSoportePush()) {
    return false;
  }
  
  const swReg = await registrarServiceWorker();
  if (!swReg) {
    return false;
  }
  
  const permisoOtorgado = await solicitarPermisoNotificaciones();
  if (!permisoOtorgado) {
    return false;
  }
  
  return await suscribirUsuario();
}

// Exponer funciones
window.inicializarNotificacionesPush = inicializarNotificacionesPush;
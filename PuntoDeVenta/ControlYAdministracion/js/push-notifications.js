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
    // Usar una ruta relativa que sea más probable que funcione en diferentes configuraciones de servidor
    const swPath = '../service-worker.js';
    console.log('Intentando registrar Service Worker en:', swPath);
    
    swRegistration = await navigator.serviceWorker.register(swPath);
    console.log('Service Worker registrado correctamente', swRegistration);
    return swRegistration;
  } catch (error) {
    console.error('Error al registrar el Service Worker:', error);
    
    // Mostrar mensaje específico basado en el error
    const errorMsg = 'Error registrando Service Worker: ' + (error.message || 'Error desconocido');
    console.error(errorMsg);
    mostrarMensajeNoSoportado(errorMsg);
    
    return null;
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
    console.log('Obteniendo clave pública desde el servidor...');
    const response = await fetch('../api/get_vapid_public_key.php');
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
    }
    
    const data = await response.json();
    console.log('Respuesta recibida:', data);
    
    if (!data.success || !data.publicKey) {
      throw new Error('No se pudo obtener la clave pública: ' + (data.message || 'Error desconocido'));
    }
    
    console.log('Clave pública obtenida:', data.publicKey);
    
    // Convertir la clave pública a formato Uint8Array
    try {
      // Ejecutar diagnóstico en la clave antes de convertir
      console.log('Longitud de la clave:', data.publicKey.length);
      
      const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
      console.log('Clave convertida con éxito a Uint8Array');
      
      // Intentar suscribir al usuario
      console.log('Intentando suscribir al usuario...');
      const newSubscription = await swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
      });
      
      console.log('Usuario suscrito correctamente:', newSubscription);
      
      // Enviar la suscripción al servidor
      const saveResult = await guardarSuscripcion(newSubscription);
      if (!saveResult) {
        throw new Error('Error al guardar la suscripción en el servidor.');
      }
      
      // Mostrar mensaje de éxito
      mostrarMensajeExito('Te has suscrito correctamente a las notificaciones.');
      
      return newSubscription;
    } catch (conversionError) {
      console.error('Error al convertir la clave o suscribir:', conversionError);
      throw new Error('Error al procesar la clave pública: ' + conversionError.message);
    }
  } catch (error) {
    console.error('Error en el proceso de suscripción:', error);
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
    const response = await fetch('../api/guardar_suscripcion.php', {
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

// Utilidad para convertir una clave base64 a Uint8Array - VERSIÓN MEJORADA
function urlBase64ToUint8Array(base64String) {
  try {
    // Asegurarse de que la cadena no sea nula
    if (!base64String) {
      throw new Error('La cadena base64 está vacía o es nula');
    }
    
    console.log('Convirtiendo clave base64 a Uint8Array, longitud:', base64String.length);
    
    // Reemplazar caracteres especiales y agregar padding
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
      .replace(/-/g, '+')
      .replace(/_/g, '/');
    
    console.log('Base64 con padding, longitud:', base64.length);
    
    // Decodificar a binario
    const rawData = window.atob(base64);
    console.log('Decodificación exitosa, longitud de datos brutos:', rawData.length);
    
    // Convertir a Uint8Array
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    
    // Verificar formato de clave
    if (outputArray.length > 0 && outputArray[0] === 4) {
      console.log('✓ Formato de clave correcto (inicia con 0x04)');
    } else if (outputArray.length > 0) {
      console.log('⚠ Advertencia: La clave no comienza con 0x04, primer byte:', outputArray[0]);
      
      // CORRECCIÓN: Si la clave no comienza con 0x04, la regeneramos correctamente
      console.warn('Intentando corregir el formato de la clave agregando el byte 0x04...');
      
      // Extraer los bytes (sin el primer byte incorrecto) y agregar 0x04 al comienzo
      const fixedArray = new Uint8Array(outputArray.length);
      fixedArray[0] = 4; // Establecer el primer byte a 0x04
      
      // Copiar el resto de los bytes (excepto quizás el primero)
      for (let i = 1; i < outputArray.length; ++i) {
        fixedArray[i] = outputArray[i];
      }
      
      console.log('Se corrigió la clave con formato 0x04');
      return fixedArray;
    }
    
    console.log('Array Uint8 creado correctamente, longitud:', outputArray.length);
    return outputArray;
  } catch (error) {
    console.error('Error en la conversión de la clave:', error);
    throw new Error('Error al convertir la clave VAPID: ' + error.message);
  }
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
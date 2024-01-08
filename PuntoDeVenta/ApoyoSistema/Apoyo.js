function ayudaloginn(){
    Swal.mixin({
      
      confirmButtonText: 'Siguiente &rarr;',
      cancelButtonText: 'Cerrar',
      showCancelButton: true,
     
      
    }).queue([
      {
        title: 'Interfaz de inicio',
        text: 'Interfaz de login a panel, donde se realiza la solicitud de claves de usuario.',
        imageUrl: 'ApoyoSistema/Inicio.gif',
        imageWidth: 200,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
      {
        title: 'Debes introducir los datos necesarios',
        text: 'Sino ingresas tu correo o contraseña el sistema te lo solicitara, sin ello no puedes iniciar sesion.',
        imageUrl: 'Ayuda_login/Serequiere.gif',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
      {
        title: 'Datos no validos',
        text: 'Si no se ingresan los datos correctos el sistema te notificara.',
        imageUrl: 'Ayuda_login/Novalido.gif',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
      {
        title: 'Validacion de datos',
        text: 'Si los datos ingresados son correctos, despues de la validacion, muestra un mensaje y te redirecciona al panel de control.',
        imageUrl: 'Ayuda_login/Valido.gif',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
      {
        title: 'Regresar al menu de seleccion',
        text: 'Si deseas volver al menu principal, debes hacer click en el boton azul con el simbolo de home.',
        imageUrl: 'Ayuda_login/Regresamenu.gif',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
      {
        title: 'Contactar a soporte',
        text: 'Si requieres de asistencia adicional, puedes contactar a soporte dando click en el siguiente boton',
        imageUrl: 'Ayuda_login/Soporte.gif',
        imageWidth: 200,
        imageHeight: 200,
        imageAlt: 'Menu de inicio',
      },
    ]).then((result) => {
      if (result.value) {
        const answers = JSON.stringify(result.value)
        Swal.fire({
          title: 'Por el momento es todo.',
          text: 'Si tienes dudas puedes ponerte en contacto con soporte, o solicitar un manual de usuario digital.',
          imageUrl: 'Ayuda_login/Final.gif',
          imageWidth: 400,
          imageHeight: 200,
          imageAlt: 'Cerrar',
          confirmButtonText: 'Entendido!'
        })
      }
    })
    }
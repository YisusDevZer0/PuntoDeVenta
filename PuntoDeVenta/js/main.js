$(document).ready(function(){
	/*Mostrar ocultar area de notificaciones*/
	$('.btn-Notification').on('click', function(){
        var ContainerNoty=$('.container-notifications');
        var NotificationArea=$('.NotificationArea');
        if(NotificationArea.hasClass('NotificationArea-show')&&ContainerNoty.hasClass('container-notifications-show')){
            NotificationArea.removeClass('NotificationArea-show');
            ContainerNoty.removeClass('container-notifications-show');
        }else{
            NotificationArea.addClass('NotificationArea-show');
            ContainerNoty.addClass('container-notifications-show');
        }
    });
    /*Mostrar ocultar menu principal*/
    $('.btn-menu').on('click', function(){
    	var navLateral=$('.navLateral');
    	var pageContent=$('.pageContent');
    	var navOption=$('.navBar-options');
    	if(navLateral.hasClass('navLateral-change')&&pageContent.hasClass('pageContent-change')){
    		navLateral.removeClass('navLateral-change');
    		pageContent.removeClass('pageContent-change');
    		navOption.removeClass('navBar-options-change');
    	}else{
    		navLateral.addClass('navLateral-change');
    		pageContent.addClass('pageContent-change');
    		navOption.addClass('navBar-options-change');
    	}
    });
    /*Salir del sistema*/
    $('.btn-exit').on('click', function(){
    	swal({
		  	title: 'You want out of the system?',
		 	text: "The current session will be closed and will leave the system",
		  	type: 'warning',
		  	showCancelButton: true,
		  	confirmButtonText: 'Yes, exit',
		  	closeOnConfirm: false
		},
		function(isConfirm) {
		  	if (isConfirm) {
		    	window.location='index.html'; 
		  	}
		});
    });
    /*Mostrar y ocultar submenus*/
    $('.btn-subMenu').on('click', function(){
    	var subMenu=$(this).next('ul');
    	var icon=$(this).children("span");
    	if(subMenu.hasClass('sub-menu-options-show')){
    		subMenu.removeClass('sub-menu-options-show');
    		icon.addClass('zmdi-chevron-left').removeClass('zmdi-chevron-down');
    	}else{
    		subMenu.addClass('sub-menu-options-show');
    		icon.addClass('zmdi-chevron-down').removeClass('zmdi-chevron-left');
    	}
    });
});
(function($){
        $(window).on("load",function(){
            $(".NotificationArea, .pageContent").mCustomScrollbar({
                theme:"dark-thin",
                scrollbarPosition: "inside",
                autoHideScrollbar: true,
                scrollButtons:{ enable: true }
            });
            $(".navLateral-body").mCustomScrollbar({
                theme:"light-thin",
                scrollbarPosition: "inside",
                autoHideScrollbar: true,
                scrollButtons:{ enable: true }
            });
        });
})(jQuery);


$(document).ready(function () {
    $('form').submit(function (event) {
        event.preventDefault();

        var userName = $('#userName').val();
        var password = $('#pass').val();

        $.ajax({
            type: 'POST',
            url: 'https://doctorpez.mx/PuntoDeVenta/Consultas/Login.php',
            data: { userName: userName, pass: password },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Redirigir o realizar acciones según el tipo de usuario
                    if (response.message.includes('administrador')) {
                        // Redirigir a la página de administrador
                        window.location.href = 'admin_home.html';
                    } else if (response.message.includes('vendedor')) {
                        // Redirigir a la página de vendedor
                        window.location.href = 'vendedor_home.html';
                    } else {
                        // Manejar otro tipo de usuario o caso no reconocido
                        alert('Rol no reconocido');
                    }
                } else {
                    // Mostrar mensaje de error
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error al procesar la solicitud.');
            }
        });
    });
});

$(document).ready(function(){
	/*Mostrar ocultar area de notificaciones*/
	$(document).on('click', '.btn-Notification', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		// Verificar si estamos usando la nueva implementación
		if ($('#notification-bell').length > 0) {
			// Usar la nueva implementación de Bootstrap
			$('#notification-dropdown').toggleClass('show');
			return;
		}
		
		// Usar la implementación antigua
		var ContainerNoty = $('.container-notifications');
		var NotificationArea = $('.NotificationArea');
		
		// Toggle classes
		ContainerNoty.toggleClass('container-notifications-show');
		NotificationArea.toggleClass('NotificationArea-show');
		
		// Asegurar que el fondo tenga pointer-events cuando está visible
		if (ContainerNoty.hasClass('container-notifications-show')) {
			$('.container-notifications-bg').css('pointer-events', 'auto');
		} else {
			$('.container-notifications-bg').css('pointer-events', 'none');
		}
	});
	
	// Cerrar notificaciones al hacer clic fuera
	$(document).on('click', function(e) {
		// Si el clic fue en el botón de notificaciones o dentro del área de notificaciones, no hacer nada
		if ($(e.target).closest('.btn-Notification, .NotificationArea, #notification-bell, #notification-dropdown').length) {
			return;
		}
		
		// Cerrar panel de notificaciones antiguo
		$('.NotificationArea').removeClass('NotificationArea-show');
		$('.container-notifications').removeClass('container-notifications-show');
		$('.container-notifications-bg').css('pointer-events', 'none');
		
		// Cerrar dropdown de notificaciones nuevo
		$('#notification-dropdown').removeClass('show');
	});
	
	// Prevenir que los clics dentro del área de notificaciones cierren el panel
	$('.NotificationArea').on('click', function(e) {
		e.stopPropagation();
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
    $(window).load(function(){
        $(".navLateral-body, .NotificationArea, .pageContent").mCustomScrollbar({
            theme:"dark-thin",
            scrollbarPosition: "inside",
            autoHideScrollbar: true,
            scrollButtons:{ enable: true }
        });
    });
})(jQuery);
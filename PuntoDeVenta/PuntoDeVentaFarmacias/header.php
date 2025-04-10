<!-- Favicon -->
<link href="img/favicon.ico" rel="icon">

<!-- Meta Tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistema Doctor Pez - Punto de Venta para Farmacias">
<meta name="keywords" content="farmacia, punto de venta, doctor pez">

<!-- Google Web Fonts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Pacifico&display=swap" rel="stylesheet">

<!-- Icon Font Stylesheet -->
<script src="https://kit.fontawesome.com/a337b4cc32.js" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Libraries Stylesheet -->
<link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
<link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<!-- Customized Bootstrap Stylesheet -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="css/style.css" rel="stylesheet">

<!-- DataTables and SweetAlert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>

<!-- Other Libraries -->
<script type="text/javascript" src="js/validation.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/darkmode-js@1.5.7/lib/darkmode-js.min.js"></script>

<!-- Custom Styles -->
<style>
    /* Estilos generales */
    :root {
        --primary: #00BCD4;
        --primary-light: #80DEEA;
        --primary-dark: #0097A7;
        --secondary: #4DD0E1;
        --accent: #FF9800;
        --light: #E0F7FA;
        --dark: #006064;
    }
    
    /* Header moderno con efecto sutil */
    .navbar {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(0, 188, 212, 0.1);
        box-shadow: 0 1px 3px rgba(0, 188, 212, 0.05);
        height: 60px;
        position: relative;
        z-index: 1000;
    }
    
    .navbar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-light), var(--primary), var(--primary-dark));
        z-index: 1;
    }
    
    .navbar-brand {
        color: var(--primary-dark);
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    
    .navbar-brand:hover {
        color: var(--primary);
    }
    
    .nav-item .nav-link {
        color: #455a64;
        padding: 1rem;
        font-weight: 400;
        transition: all 0.2s ease;
    }
    
    .nav-item .nav-link:hover,
    .nav-item .nav-link.active {
        color: var(--primary);
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 188, 212, 0.1);
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: rgba(0, 188, 212, 0.05);
        color: var(--primary);
    }
    
    /* Animaciones y efectos acuáticos */
    .wave-bg {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 120px;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(0,188,212,0.03)" d="M0,192L48,176C96,160,192,128,288,138.7C384,149,480,203,576,202.7C672,203,768,149,864,144C960,139,1056,181,1152,181.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
        background-size: cover;
        z-index: -1;
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Personalización de SweetAlert2 */
    .swal2-popup {
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff, rgba(224, 247, 250, 0.6));
        padding: 1.5rem;
    }
    
    .swal2-title {
        color: var(--dark) !important;
        font-weight: 500 !important;
    }
    
    .swal2-confirm {
        background-color: var(--primary) !important;
        border-radius: 4px !important;
    }
    
    .swal2-confirm:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 188, 212, 0.25) !important;
    }
    
    /* Personalización de Select2 */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.5rem + 2px) !important;
        border-color: rgba(0, 188, 212, 0.2) !important;
        border-radius: 4px !important;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.5rem) !important;
        padding-left: 1rem !important;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.5rem) !important;
    }
    
    .select2-container--bootstrap4.select2-container--focus .select2-selection {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.15rem rgba(0, 188, 212, 0.25) !important;
    }
    
    /* Personalización de DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
        border-radius: 4px !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--primary-light) !important;
        color: white !important;
        border-color: var(--primary-light) !important;
        border-radius: 4px !important;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(0, 188, 212, 0.2) !important;
        border-radius: 4px !important;
        padding: 0.3rem 0.75rem !important;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.15rem rgba(0, 188, 212, 0.25) !important;
        outline: none !important;
    }
    
    /* Loader acuático mejorado */
    .loader-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .wave-loader {
        width: 60px;
        height: 60px;
        position: relative;
    }
    
    .wave-loader:before,
    .wave-loader:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: var(--primary);
        opacity: 0.6;
        animation: pulse 2s ease-in-out infinite;
    }
    
    .wave-loader:after {
        animation-delay: 1s;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }
</style>

<!-- Loader -->
<div class="loader-container" id="main-loader">
    <div class="wave-loader"></div>
</div>

<script>
    // Ocultar el loader cuando la página cargue completamente
    window.addEventListener('load', function() {
        setTimeout(function() {
            const loader = document.getElementById('main-loader');
            loader.style.opacity = '0';
            setTimeout(function() {
                loader.style.display = 'none';
            }, 300);
        }, 500);
    });
    
    // Configuración para SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'animated fadeInDown'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    // Configuración para Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Selecciona una opción',
            allowClear: true,
            width: '100%'
        });
        
        // Efectos sutiles en los botones
        $('.btn').addClass('position-relative overflow-hidden');
        $('.btn').append('<span class="ripple"></span>');
        
        $('.btn').on('click', function(e) {
            const btn = $(this);
            const ripple = btn.find('.ripple');
            const btnOffset = btn.offset();
            const xPos = e.pageX - btnOffset.left;
            const yPos = e.pageY - btnOffset.top;
            
            ripple.css({
                top: yPos + 'px',
                left: xPos + 'px'
            });
            
            btn.addClass('ripple-active');
            
            setTimeout(function() {
                btn.removeClass('ripple-active');
            }, 600);
        });
    });
</script>

<style>
    /* Efecto ripple para botones */
    .btn {
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(30);
            opacity: 0;
        }
    }
    
    .ripple-active {
        overflow: hidden;
    }
    
    /* Transición para el loader */
    .loader-container {
        transition: opacity 0.3s ease;
    }
</style>
</head>
<body>
<!-- Fondo de onda -->
<div class="wave-bg"></div>


(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Sidebar Toggler
    $('.sidebar-toggler').click(function () {
        $('.sidebar').toggleClass('active');
        $('.content').toggleClass('active');
    });


    // Progress Bar
    $('.pg-bar').waypoint(function () {
        $('.progress .progress-bar').each(function () {
            $(this).css("width", $(this).attr("aria-valuenow") + '%');
        });
    }, {offset: '80%'});


    // Calender
    $('#calender').datetimepicker({
        inline: true,
        format: 'L'
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        items: 1,
        dots: true,
        loop: true,
        nav : false
    });


    // Worldwide Sales Chart
    var ctx1 = $("#worldwide-sales").get(0);
    if (ctx1) {
        ctx1 = ctx1.getContext("2d");
        var myChart1 = new Chart(ctx1, {
        type: "bar",
        data: {
            labels: ["2016", "2017", "2018", "2019", "2020", "2021", "2022"],
            datasets: [{
                    label: "USA",
                    data: [15, 30, 55, 65, 60, 80, 95],
                    backgroundColor: "rgba(0, 156, 255, .7)"
                },
                {
                    label: "UK",
                    data: [8, 35, 40, 60, 70, 55, 75],
                    backgroundColor: "rgba(0, 156, 255, .5)"
                },
                {
                    label: "AU",
                    data: [12, 25, 45, 55, 65, 70, 60],
                    backgroundColor: "rgba(0, 156, 255, .3)"
                }
            ]
            },
        options: {
            responsive: true
        }
    });
    }


    // Salse & Revenue Chart
    var ctx2 = $("#salse-revenue").get(0);
    if (ctx2) {
        ctx2 = ctx2.getContext("2d");
        var myChart2 = new Chart(ctx2, {
        type: "line",
        data: {
            labels: ["2016", "2017", "2018", "2019", "2020", "2021", "2022"],
            datasets: [{
                    label: "Salse",
                    data: [15, 30, 55, 45, 70, 65, 85],
                    backgroundColor: "rgba(0, 156, 255, .5)",
                    fill: true
                },
                {
                    label: "Revenue",
                    data: [99, 135, 170, 130, 190, 180, 270],
                    backgroundColor: "rgba(0, 156, 255, .3)",
                    fill: true
                }
            ]
            },
        options: {
            responsive: true
        }
    });
    }
    


    // Single Line Chart
    var ctx3 = $("#line-chart").get(0);
    if (ctx3) {
        ctx3 = ctx3.getContext("2d");
        var myChart3 = new Chart(ctx3, {
        type: "line",
        data: {
            labels: [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150],
            datasets: [{
                label: "Salse",
                fill: false,
                backgroundColor: "rgba(0, 156, 255, .3)",
                data: [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15]
            }]
        },
        options: {
            responsive: true
        }
    });
    }


    // Single Bar Chart
    var ctx4 = $("#bar-chart").get(0);
    if (ctx4) {
        ctx4 = ctx4.getContext("2d");
        var myChart4 = new Chart(ctx4, {
        type: "bar",
        data: {
            labels: ["Italy", "France", "Spain", "USA", "Argentina"],
            datasets: [{
                backgroundColor: [
                    "rgba(0, 156, 255, .7)",
                    "rgba(0, 156, 255, .6)",
                    "rgba(0, 156, 255, .5)",
                    "rgba(0, 156, 255, .4)",
                    "rgba(0, 156, 255, .3)"
                ],
                data: [55, 49, 44, 24, 15]
            }]
        },
        options: {
            responsive: true
        }
    });
    }


    // Pie Chart
    var ctx5 = $("#pie-chart").get(0);
    if (ctx5) {
        ctx5 = ctx5.getContext("2d");
        var myChart5 = new Chart(ctx5, {
        type: "pie",
        data: {
            labels: ["Italy", "France", "Spain", "USA", "Argentina"],
            datasets: [{
                backgroundColor: [
                    "rgba(0, 156, 255, .7)",
                    "rgba(0, 156, 255, .6)",
                    "rgba(0, 156, 255, .5)",
                    "rgba(0, 156, 255, .4)",
                    "rgba(0, 156, 255, .3)"
                ],
                data: [55, 49, 44, 24, 15]
            }]
        },
        options: {
            responsive: true
        }
    });
    }


    // Doughnut Chart
    var ctx6 = $("#doughnut-chart").get(0);
    if (ctx6) {
        ctx6 = ctx6.getContext("2d");
        var myChart6 = new Chart(ctx6, {
        type: "doughnut",
        data: {
            labels: ["Italy", "France", "Spain", "USA", "Argentina"],
            datasets: [{
                backgroundColor: [
                    "rgba(0, 156, 255, .7)",
                    "rgba(0, 156, 255, .6)",
                    "rgba(0, 156, 255, .5)",
                    "rgba(0, 156, 255, .4)",
                    "rgba(0, 156, 255, .3)"
                ],
                data: [55, 49, 44, 24, 15]
            }]
        },
        options: {
            responsive: true
        }
    });
    }

    
    // Menú dropdown en el sidebar - Compatible con Bootstrap 5
    $('.sidebar .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $this = $(this);
        const $dropdown = $this.closest('.dropdown');
        const $menu = $dropdown.find('.dropdown-menu');
        
        // Cierra todos los otros dropdowns abiertos
        $('.sidebar .dropdown').not($dropdown).each(function() {
            $(this).find('.dropdown-toggle').removeClass('show');
            $(this).find('.dropdown-menu').removeClass('show');
        });
        
        // Toggle el dropdown actual
        if ($this.hasClass('show')) {
            $this.removeClass('show');
            $menu.removeClass('show');
        } else {
            $this.addClass('show');
            $menu.addClass('show');
        }
    });

    // Cierra los dropdown cuando se hace clic fuera de ellos
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.sidebar .dropdown').length) {
            $('.sidebar .dropdown-toggle').removeClass('show');
            $('.sidebar .dropdown-menu').removeClass('show');
        }
    });

    // Prevenir que Bootstrap maneje los dropdowns automáticamente en el sidebar
    $('.sidebar .dropdown-toggle').attr('data-bs-toggle', '');
    $('.sidebar .dropdown-toggle').removeAttr('data-bs-toggle');

    // Agrega la clase active al elemento del menú actual
    const currentLocation = location.pathname;
    const pathParts = currentLocation.split('/');
    const page = pathParts[pathParts.length - 1];
    
    $('.sidebar .navbar-nav a').each(function() {
        const href = $(this).attr('href');
        if (href === page || href + '.php' === page) {
            $(this).addClass('active');
            
            // Si el elemento está dentro de un dropdown, abre el dropdown
            if ($(this).hasClass('dropdown-item')) {
                const dropdown = $(this).closest('.dropdown');
                dropdown.find('.dropdown-toggle').addClass('show');
                dropdown.find('.dropdown-menu').addClass('show');
            }
        }
    });

    // Efectos acuáticos decorativos
    function createBubbles() {
        const waterDecoration = $('<div class="water-decoration"></div>');
        $('body').append(waterDecoration);
        
        for (let i = 0; i < 15; i++) {
            const size = Math.random() * 30 + 10;
            const bubble = $('<div class="bubble"></div>');
            bubble.css({
                width: size + 'px',
                height: size + 'px',
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 5 + 's'
            });
            waterDecoration.append(bubble);
        }
        
        const wave = $('<div class="wave"></div>');
        waterDecoration.append(wave);
    }
    
    createBubbles();

    // Comportamiento responsivo
    function checkWindowSize() {
        if (window.innerWidth <= 992) {
            $('.sidebar').addClass('active');
        } else {
            $('.sidebar').removeClass('active');
        }
    }
    
    // Verificar tamaño al cargar
    checkWindowSize();
    
    // Verificar tamaño al redimensionar
    $(window).resize(function() {
        checkWindowSize();
    });

})(jQuery);
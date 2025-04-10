    <!-- Footer Start -->
    <div class="container-fluid px-0">
        <div class="bg-light rounded-0 p-4 mt-4" style="border-top: 1px solid rgba(0,188,212,0.15); box-shadow: 0 -2px 15px rgba(0,0,0,0.03);">
            <div class="row">
                <div class="col-12 col-sm-6 text-center text-sm-start">
                    &copy; <a href="#" class="text-primary">Doctor Pez</a> - Derechos Reservados. 
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-end">
                    Diseñado con <i class="fa fa-heart text-primary"></i> por Zero
                </div>
            </div>
            <!-- Fondo de ondas para el footer -->
            <div class="wave-bg"></div>
        </div>
    </div>
    <!-- Footer End -->
</div>
<!-- Content End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

<!-- JSZip -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>

<!-- pdfmake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/tempusdominus/js/moment.min.js"></script>
<script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="js/main.js"></script>

<!-- Bubble animation effect -->
<script>
    // Función para crear burbujas en el footer
    function createBubbles() {
        const footer = document.querySelector('.bg-light.p-4');
        const bubbleCount = 8;
        
        for (let i = 0; i < bubbleCount; i++) {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble-effect');
            
            // Tamaño aleatorio
            const size = Math.random() * 10 + 4;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            
            // Posición aleatoria
            const posX = Math.random() * 100;
            bubble.style.left = `${posX}%`;
            bubble.style.bottom = '0';
            
            // Animación con retardo aleatorio
            const delay = Math.random() * 3;
            bubble.style.animation = `bubbleRise 3s ease-in ${delay}s infinite`;
            
            footer.appendChild(bubble);
        }
    }
    
    // Ejecutar cuando el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
        createBubbles();
        
        // Hacer que el botón de volver arriba aparezca al hacer scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                document.querySelector('.back-to-top').style.display = 'flex';
            } else {
                document.querySelector('.back-to-top').style.display = 'none';
            }
        });
        
        // Acción del botón volver arriba
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    });
</script>

<style>
    /* Estilos para las burbujas */
    .bubble-effect {
        position: absolute;
        background-color: rgba(0, 188, 212, 0.15);
        border-radius: 50%;
        pointer-events: none;
        z-index: 1;
    }
    
    @keyframes bubbleRise {
        0% {
            transform: translateY(0) scale(1);
            opacity: 0;
        }
        50% {
            opacity: 0.3;
        }
        100% {
            transform: translateY(-60px) scale(1.2);
            opacity: 0;
        }
    }
    
    /* Estilos para el footer */
    .bg-light.p-4 {
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff, rgba(224, 247, 250, 0.3));
    }
    
    /* Estilo para el botón volver arriba */
    .back-to-top {
        display: none;
        position: fixed;
        right: 30px;
        bottom: 30px;
        z-index: 99;
        background-color: #00BCD4;
        color: white;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        text-align: center;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 188, 212, 0.1);
        border: none;
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }
    
    .back-to-top:hover {
        background-color: #0097A7;
        transform: translateY(-5px);
    }
    
    @keyframes float {
        0% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
        100% { transform: translateY(0); }
    }
    
    /* Efecto de wave para el footer */
    .wave-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(0,188,212,0.05)" d="M0,192L48,176C96,160,192,128,288,138.7C384,149,480,203,576,202.7C672,203,768,149,864,144C960,139,1056,181,1152,181.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
        background-size: cover;
        z-index: -1;
        opacity: 0.8;
        pointer-events: none;
    }
</style>
    <!-- Footer Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded-top p-4">
            <div class="row">
                <div class="col-12 col-sm-6 text-center text-sm-start">
                    &copy; <a href="#" class="text-primary">Doctor Pez</a> - Derechos Reservados. 
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-end">
                    Diseñado con <i class="fa fa-heart text-primary"></i> por Zero
                </div>
            </div>
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
        const footer = document.querySelector('.bg-light.rounded-top');
        const bubbleCount = 10;
        
        for (let i = 0; i < bubbleCount; i++) {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble-effect');
            
            // Tamaño aleatorio
            const size = Math.random() * 15 + 5;
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
    });
</script>

<style>
    /* Estilos para las burbujas */
    .bubble-effect {
        position: absolute;
        background-color: rgba(0, 188, 212, 0.2);
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
            opacity: 0.5;
        }
        100% {
            transform: translateY(-100px) scale(1.2);
            opacity: 0;
        }
    }
    
    /* Efecto acuático en el footer */
    .bg-light.rounded-top {
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff, rgba(224, 247, 250, 0.5));
        border-top: 2px solid rgba(0, 188, 212, 0.2);
    }
    
    .back-to-top {
        background-color: #00BCD4 !important;
        animation: float 2s ease-in-out infinite;
    }
    
    @keyframes float {
        0% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0); }
    }
</style>
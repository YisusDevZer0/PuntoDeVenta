    <!-- Footer Start -->
    <div class="container-fluid px-0">
        <div class="bg-light p-4 border-top" style="border-color: rgba(0,188,212,0.1) !important; box-shadow: none !important; border-radius: 0 !important;">
            <div class="row">
                <div class="col-12 col-sm-6 text-center text-sm-start">
                    &copy; <a href="#" class="text-primary" style="text-decoration: none;">Doctor Pez</a> - Derechos Reservados. 
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-end">
                    Diseñado por <span class="text-primary">Zero</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
</div>
<!-- Content End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" style="display: none; border-radius: 0 !important; box-shadow: none !important;"><i class="bi bi-arrow-up"></i></a>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    /* Estilos para el footer */
    .bg-light.p-4 {
        background-color: #f9fafc !important;
    }
    
    /* Estilo para el botón volver arriba */
    .back-to-top {
        display: none;
        position: fixed;
        right: 30px;
        bottom: 30px;
        z-index: 99;
        background-color: #00BCD4 !important;
        color: white;
        border-radius: 0 !important;
        width: 45px;
        height: 45px;
        text-align: center;
        justify-content: center;
        align-items: center;
        box-shadow: none !important;
        border: none;
        transition: background-color 0.3s ease;
    }
    
    .back-to-top:hover {
        background-color: #0097A7 !important;
    }
</style>
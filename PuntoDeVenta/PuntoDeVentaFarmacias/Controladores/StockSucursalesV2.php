
<script>

tabla = $('#Productos').DataTable({

 "bProcessing": true,
 "ordering": true,
 "stateSave":true,
 "bAutoWidth": false,
 "order": [[ 0, "desc" ]],
 "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ArrayStockAlmacenV2.php",
 "aoColumns": [
       { mData: 'Cod_Barra' },
       { mData: 'Nombre_Prod' },
       { mData: 'Proveedor' },

       { mData: 'Precio_Venta' },
       { mData: 'Nom_Serv' },
       { mData: 'FechaIngreso' },
//        { mData: 'Existencias_R' },
//        {mData: "Existencias_R",
//         "searchable": true,
//         "orderable":true,
//         "render": function (data, type, row) {
//             if ( row.Existencias_R < row.Min_Existencia) {

//             return '<button class="btn btn-default btn-sm" style="background-color:#ff1800!important">Resurtir</button>';
//         }
//         else if ( row.Existencias_R > row.Max_Existencia) {
// return '<button class="btn btn-default btn-sm" style="background-color:#fd7e14!important">Sobregirado</button>'
//         }
//             else {
 
//     return '<button class="btn btn-default btn-sm" style="background-color:#2bbb1d!important">Completo</button>';
 
// }
//         }
 
//     },
 
    { mData: 'Fecha_Caducidad' },

    //    { mData: 'Acciones' },
  
      ],
     
    
      "lengthMenu": [[10,20,150,250,500, -1], [10,20,50,250,500, "Todos"]],  
  
      language: {
            "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
			     },
			     "sProcessing":"Procesando...",
            },
          
        //para usar los botones   
        responsive: "true",
       
       
   
	   
        	        
    });     

</script>
<div class="text-center">
	<div class="table-responsive">
	<table  id="Productos" class="table table-hover">
<thead>

<th>Clave</th>
    <th>Nombre producto</th>
    <th>Proveedor </th>
    <th>Precio De Venta </th>
  
    <th>Tipo de servicio </th>
    <th>Fecha de ingreso </th>
    <!-- <th>Existencias </th> -->
<!-- <th>Estatus</th> -->
<th>Fecha de caducidad</th>
<!-- <th >Accciones</th> -->


</thead>

</div>
</div>



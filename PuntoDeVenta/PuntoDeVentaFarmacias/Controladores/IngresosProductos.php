
<script>

tabla = $('#Productos').DataTable({

 "bProcessing": true,
 "ordering": true,
 "stateSave":true,
 "bAutoWidth": false,
 "order": [[ 0, "desc" ]],
 "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ArrayIngresosProductos.php",
 "aoColumns": [
    { mData: 'Folio_Ingreso' },
                { mData: 'Factura' },
                { mData: 'Cod_Barra' },
                { mData: 'Nombre_Prod' },
                { mData: 'Precio_Compra' },
                { mData: 'TotalFactura' },
                { mData: 'Existencias_R' },
                { mData: 'ExistenciaPrev' },
                { mData: 'Recibido' },
              
                { mData: 'Sucursal' },
                { mData: 'AgregadoPor' },
                { mData: 'AgregadoEl' },
       
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
        dom: "B<'#colvis row'><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'bottom'ip><'clear'>'",
        buttons:[ 
			{
				extend:    'excelHtml5',
				text:      'Descargar excel  <i Descargar excel class="fas fa-file-excel"></i> ',
				titleAttr: 'Descargar excel',
                autoFilter: true,
        title: 'Ingresos de productos   ',
				className: 'btn btn-success'
			},
        ],
       
       
   
	   
        	        
    });     

</script>
<div class="text-center">
	<div class="table-responsive">
	<table  id="Productos" class="table table-hover">
<thead>

<th>Folio</th>
<th># De factura</th>
<th>Codigo barras</th>
    <th>Nombre</th>
    <th>Precio compra</th>
    <th>Importe </th>
    <th>Stock</th>
    <th>Stock previo </th>
    <th>Recibido </th>
 
    <th>Sucursal</th>
    <th>Agregado por</th>
    <th>Se agrego en  </th>
    
	


</thead>

</div>
</div>



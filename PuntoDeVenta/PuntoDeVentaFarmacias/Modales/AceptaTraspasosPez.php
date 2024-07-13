<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT 
    Traspasos_generados.ID_Traspaso_Generado,
    Traspasos_generados.TraspasoRecibidoPor,
    Traspasos_generados.TraspasoGeneradoPor,
    Traspasos_generados.Num_Orden,
    Traspasos_generados.Num_Factura,
    Traspasos_generados.TotaldePiezas,
    Traspasos_generados.Cod_Barra,
    Traspasos_generados.Nombre_Prod,
    Traspasos_generados.Fk_SucDestino,
    Traspasos_generados.Precio_Venta,
    Traspasos_generados.Precio_Compra,
    Traspasos_generados.Cantidad_Enviada,
    Traspasos_generados.FechaEntrega,
    Traspasos_generados.Estatus,
    Traspasos_generados.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal 
FROM 
    Traspasos_generados,
    Sucursales
WHERE 
    Traspasos_generados.Fk_SucDestino = Sucursales.ID_Sucursal AND
    Traspasos_generados.ID_Traspaso_Generado = ". $_POST["id"];

$query = $conn->query($sql1);
$Traspaso = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Traspaso = $r;
        break;
    }
}
?>

<?php if ($Traspaso != null): ?>

<form action="javascript:void(0)" method="post" id="ActualizaDatosDeTraspasos">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="ID_Traspaso_Generado">ID Traspaso Generado</label>
                <input type="text" class="form-control" disabled readonly value="<?php echo $Traspaso->ID_Traspaso_Generado; ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="TraspasoRecibidoPor">Traspaso Recibido Por</label>
                <input type="text" class="form-control" id="TraspasoRecibidoPor" name="TraspasoRecibidoPor" value="<?php echo $Traspaso->TraspasoRecibidoPor; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="TraspasoGeneradoPor">Traspaso Generado Por</label>
                <input type="text" class="form-control" id="TraspasoGeneradoPor" name="TraspasoGeneradoPor" value="<?php echo $Traspaso->TraspasoGeneradoPor; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Num_Orden">Número de Orden</label>
                <input type="text" class="form-control" id="Num_Orden" name="Num_Orden" value="<?php echo $Traspaso->Num_Orden; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Num_Factura">Número de Factura</label>
                <input type="text" class="form-control" id="Num_Factura" name="Num_Factura" value="<?php echo $Traspaso->Num_Factura; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="TotaldePiezas">Total de Piezas</label>
                <input type="text" class="form-control" id="TotaldePiezas" name="TotaldePiezas" value="<?php echo $Traspaso->TotaldePiezas; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Cod_Barra">Código de Barra</label>
                <input type="text" class="form-control" id="Cod_Barra" name="Cod_Barra" value="<?php echo $Traspaso->Cod_Barra; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Nombre_Prod">Nombre del Producto</label>
                <input type="text" class="form-control" id="Nombre_Prod" name="Nombre_Prod" value="<?php echo $Traspaso->Nombre_Prod; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Fk_SucDestino">Sucursal Destino</label>
                <select id="Fk_SucDestino" class="form-control" name="Fk_SucDestino">
                    <option value="<?php echo $Traspaso->Fk_SucDestino; ?>"><?php echo $Traspaso->Nombre_Sucursal; ?></option>
                    <?php
                    $query = $conn->query("SELECT * FROM Sucursales");
                    while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Precio_Venta">Precio de Venta</label>
                <input type="text" class="form-control" id="Precio_Venta" name="Precio_Venta" value="<?php echo $Traspaso->Precio_Venta; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Precio_Compra">Precio de Compra</label>
                <input type="text" class="form-control" id="Precio_Compra" name="Precio_Compra" value="<?php echo $Traspaso->Precio_Compra; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Cantidad_Enviada">Cantidad Enviada</label>
                <input type="text" class="form-control" id="Cantidad_Enviada" name="Cantidad_Enviada" value="<?php echo $Traspaso->Cantidad_Enviada; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="FechaEntrega">Fecha de Entrega</label>
                <input type="text" class="form-control" id="FechaEntrega" name="FechaEntrega" value="<?php echo $Traspaso->FechaEntrega; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Estatus">Estatus</label>
                <input type="text" class="form-control" id="Estatus" name="Estatus" value="<?php echo $Traspaso->Estatus; ?>" maxlength="60">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ID_H_O_D">ID H O D</label>
                <input type="text" class="form-control" id="ID_H_O_D" name="ID_H_O_D" value="<?php echo $Traspaso->ID_H_O_D; ?>" maxlength="60">
            </div>
        </div>
    </div>

    <input type="hidden" name="ID_Traspaso_Generado" id="id" value="<?php echo $Traspaso->ID_Traspaso_Generado; ?>">
    <button type="submit" id="submit" class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
</form>
<script src="js/ActualizaDataDeTraspasos.js"></script>

<?php else: ?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>

<?php 

/* llamadas */
require "config/config.php";
require "config/database.php";


/* Base de datos */
$db = new Database();
$con = $db->conectar();



/* Productos carrito */

$lista_carrito = array();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

if($productos != null){
    foreach($productos as $clave => $cantidad){
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id=? AND activo=1");
        $sql->execute([$clave]); 
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}
$total = 0;
$iva_total = 0;
$vuelto = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_detalles.css">
    <link rel="stylesheet" href="css/styles_productos_carrito.css">

    <title>Supermercado ABA - siempre con usted</title>
</head>

<body>
    <header>
        <div class="logo">
            <h2>Factura</h2>
        </div>
        <nav>
            <a href="main.php">Productos</a>
            <a href="#" class="btn-carrito">
                Carrito <span id="numero_carrito"><?php echo $numero_carrito?></span>
            </a>
        </nav>
    </header>
    <main>
        <section class="products">
            <div class="tabla-productos">
                <table>
                    <thead class="thead">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Precio con descuento</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>% IVA</th>
                            <th>Descuento</th>

                        <tr></tr>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($lista_carrito == null){
                            echo '<tr><td colspan = 5><b>Lista vacia</b></td></tr>';
                        }else{
                            $total = 0;
                            $iva_total = 0;
                            $total_neto = 0;
                            $porcentaje_iva = 0.13;
                            
                            foreach($lista_carrito as $producto){
                                $_id = $producto['id'];
                                $nombre = $producto['nombre'];
                                $precio = $producto['precio'];
                                $descuento = $producto['descuento'];
                                $cantidad = $producto['cantidad'];
                                $precio_descuento = $precio - (($precio * $descuento) / 100);
                                $subtotal = $cantidad * $precio_descuento ;  
                                $iva = $precio * $porcentaje_iva = 0.13;
                                $iva_total += $iva;
                                $total += $subtotal;


                            
                        ?>

                        <tr>
                            <td> <?php echo $nombre?></td>
                            <td>
                                <div id="precio_<?php echo $_id;?>" name="precio">
                                    <?php echo MONEDA . number_format($precio, 0, ';', '.');?></div>
                            </td>
                            <td>
                                <?php 
                                 if($descuento == 0 ){?>
                                <div><?php echo MONEDA. 0?></div>
                                <?php } else {?>
                                <?php echo MONEDA . number_format($precio_descuento, 0, ';', '.');?>
                            </td>

                            <?php }?>

                            <td>
                                <input type="number" min="1" max="100" step="1" value="<?php echo $cantidad; ?>"
                                    size="5" id="cantidad_<?php echo $_id; ?>"
                                    onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
                            </td>

                            <td>
                                <div id="subtotal_<?php echo $_id;?>" name="subtotal[]">
                                    <?php echo MONEDA . number_format($subtotal, 0, ';', '.');?></div>
                            </td>

                            <td>
                                <div id="IVA_<?php echo $_id;?>" name="IVA">
                                    <?php echo MONEDA . number_format($iva, 0, ';', '.');?></div>
                            </td>
                            <td>
                                <div id="%IVA_<?php echo $_id;?>" name="%IVA">
                                    <?php echo "$porcentaje_iva". "%" ;?></div>
                            </td>
                            <td>
                                <div id="descuento_<?php echo $_id;?>" name="descuento">
                                    <?php echo number_format($descuento, 0, ';', '.'). "%"; ?></div>
                            </td>
                            <td>

                                <button class="btn-eliminar" id="eliminar_producto" id_producto="<?php echo $_id;?>"
                                    onclick="eliminar()">Eliminar</button>

                            </td>
                        </tr>
                        <?php }?>

                        <tr>
                            <td>
                                <h3 id="iva_total">Total IVA:
                                    <?php echo MONEDA . number_format($iva_total, 0, ';', '.');?> </h3>
                            </td>
                            <td colspan="5"></td>
                            <td colspan="2">
                                <h3 id="total">
                                    <?php echo "Total  ". MONEDA . number_format($total + $iva_total, 0, '.', ',');?>
                                </h3>
                            </td>
                            <td></td>

                        </tr>

                    </tbody>

                    <?php } ?>
                </table>
            </div>
            <div class="formulario_pago">
                <form method="POST" action="">
                    <label for="pago" class="lbl_pago">Va a pagar con </label>
                    <br>
                    <input type="number" name="pago" required class="input_pago" min="1">
                    <br>
                    <button class="btn_realizar_pago">Realizar pago</button>
                </form>
            </div>


            <?php 
            $pago = 0;
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                $pago = $_POST['pago'];
        
            }?>

            <?php if ($pago >= $total + $iva_total) {?>


            <div class="factura">
                <table class="table_factura">
                    <div class="header_factura">
                        <div> </div>
                        <div> </div>
                        <h1>Factura</h1>
                        <?php
                            date_default_timezone_set('America/Costa_Rica');


                            $fecha_actual = date('Y-m-d'); 
                            $hora_actual = date('H:i:s'); 


                            echo $fecha_actual . " / ". $hora_actual ;
                    ?>
                    </div>
                    <thead class="thead_factura">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Descuento</th>
                            <th>Precio con descuento</th>
                            <th>Cantidad de productos</th>
                            <th>Sub Total</th>
                            <th>IVA</th>
                        </tr>
                    </thead>
                    <tbody class="tbody_factura">

                        <?php if($lista_carrito == null){
                            echo '<tr><td colspan = 5><b>Lista vacia</b></td></tr>';
                        }else{
                          $total = 0;
                          $iva_total = 0;
                          $total_neto = 0;
                          
                            
                            foreach($lista_carrito as $producto){
                                $_id = $producto['id'];
                                $nombre = $producto['nombre'];
                                $precio = $producto['precio'];
                                $descuento = $producto['descuento'];
                                $cantidad = $producto['cantidad'];
                                $precio_descuento = $precio - (($precio * $descuento) / 100);
                                $subtotal = $cantidad * $precio_descuento;
                                $iva = $precio * 0.13;
                                $total_neto = $total_neto + $subtotal;
                                $total += $subtotal;
                                $total_neto - $iva_total;
                                $iva_total += $iva ;
                                $vuelto =  $pago - ($total + $iva_total)
 
                        ?>

                        <tr>
                            <td> <?php echo $nombre?></td>

                            <td>
                                <div id="precio_<?php echo $_id;?>" name="precio">
                                    <?php echo MONEDA . number_format($precio, 0, ';', '.');?></div>
                            </td>
                            <td>
                                <div id="descuento_<?php echo $_id;?>" name="descuento">
                                    <?php echo number_format($descuento, 0, ';', '.'). "%";?></div>
                            </td>
                            <td>
                                <?php 
                                 if($descuento == 0 ){?>
                                    <div><?php echo MONEDA. 0?></div>
                                <?php } else {?>
                                <?php echo MONEDA . number_format($precio_descuento, 0, ';', '.');?>

                                <?php } ?>
                            </td>
                            <td>
                                <div id="cantidad_<?php echo $_id;?>" name="cantidad">
                                    <?php echo number_format($cantidad, 0, ';', '.');?></div>
                            </td>
                            <td>
                                <div id="subtotal_<?php echo $_id;?>" name="subtotal[]">
                                    <?php echo MONEDA . number_format($subtotal, 0, ';', '.');?></div>
                            </td>
                            <td>
                                <div id="iva_<?php echo $_id;?>" name="iva">
                                    <?php echo MONEDA . number_format($iva, 0, ';', '.');?></div>
                            </td>

                        </tr>

                        <?php }?>
                        <tr>
                            <td>
                                <h4 id="total_sin_iva">Total sin
                                    impuestos <br> <?php echo MONEDA . number_format($total_neto, 0, ';', '.');?>
                                </h4>
                            </td>
                            <td></td>
                            <td>
                                <h4 id="iva_total">Total
                                    IVA <br> <?php echo MONEDA . number_format($iva_total, 0, ';', '.');?> </h4>
                            </td>
                            <td></td>
                            <td>
                                <h4 id="total">Precio
                                    total  <br> <?php echo MONEDA . number_format($total + $iva_total, 0, ';', '.');?> </h4>
                            </td>
                            <td>
                                <h4 id="pago">Pago con <br> <?php echo MONEDA . number_format($pago, 0, ';', '.');?> </h4>
                            </td>   

                            <td>
                                <h4 id="vuelto">Su vuelto es <br> <?php echo MONEDA . number_format($vuelto, 0, ';', '.');?> </h4>
                            </td>
                        </tr>
                    </tbody>

                    <?php } ?>

                </table>
            </div>

            <a href="main.php" class="volver_inicio" <?php session_destroy();?>>Volver al inicio</a>

            <?php } else { ?>

            <h3><?php  echo "No cuenta con suficientes fondos para realizar el pago"; ?></h3>

            <?php $dinero_faltante =  ($total + $iva_total) - $pago;?>

            <h3><?php echo "Le hacen falta  ₡". number_format($dinero_faltante, 0, ';', '.'); ?></h3>

            <?php } ?>
        </section>
    </main>

    <script>
    let eliminaProducto = document.getElementById('eliminar_producto')
    eliminaProducto.addEventListener('click', function(event) {
        /* eliminaProducto = event.relatedTarget */
        let id = eliminaProducto.getAttribute('id_producto')
        eliminaProducto.value = id

    })

    function actualizaCantidad(cantidad, id) {
        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action', 'agregar')
        formData.append('id', id)
        formData.append('cantidad', cantidad)

        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {

                    let divsubtotal = document.getElementById('subtotal_' + id)
                    divsubtotal.innerHTML = data.sub

                    let total = 0.00;

                    let list = document.getElementsByName('subtotal[]')

                    for (let i = 0; i < list.length; i++) {
                        total += parseFloat(list[i].innerHTML.replace(/[₡,.]/g, ''))
                        /* Con el replace quitamos los signos entre los corchetes para que no nos de 
                         problemas y con el g indicamos porque lo queremos reemplazar en este caso con '' osea por nada*/
                    }

                    total = new Intl.NumberFormat('en-cr', {
                        minimumFractionDigits: 0

                    }).format(total)
                    document.getElementById('total').innerHTML = '<?php echo "Total  ". MONEDA ; ?>' + total

                }
            })
    }

    function eliminar() {

        let botonElimina = document.getElementById('eliminar_producto')
        let id = botonElimina.value


        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action', 'eliminar')
        formData.append('id', id)


        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    location.reload() //Para actualizar la ventana de forma automatica
                }
            })
    }
    </script>




</body>

</html>
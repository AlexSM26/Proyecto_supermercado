<?php 
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';  //El isset es para validar que si este definido
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == ''){
    echo "Error al procesar la peticion";
    exit;
}else{

    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if($token == $token_tmp){
        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
        $sql->execute([$id]);
        if($sql->fetchColumn() > 0 ){//Si es mayor a 0 es porque encontro un dato y va a arrojar un elemento
            $sql = $con->prepare("SELECT nombre, descripcion, precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1"); //Averiguamos si existe un producto con el id que el usuario me solicite con el count(id)
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_descuento = $precio - (($precio * $descuento) / 100);
        }

    }else{
        echo "Error al procesar la peticion";
        exit;
    }
}
    $imagen = "images/productos_super/" .$id. "/principal.png";

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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_detalles.css">
    <title>Supermercado ABA - siempre con usted</title>
</head>
<body>
    <header>
        <div class="logo">
            <h2>Detalles del producto</h2>
             
            </div>
            <nav>
                <a href="productos_carrito.php" class="btn-carrito">
                    Carrito <span id="numero_carrito"><?php echo $numero_carrito?></span>
                </a>
                <a href="main.php">Productos</a> 
            </nav>
    </header>
    <main>
        <section class = "products">
            <div class="imagen_detalles">
                 <?php 
                    if(!file_exists($imagen)){ //Le damos una imagen pre-definida si el programa no encuentra la imagen que le corresponde
                     echo "Lo sentimos la imagen no esta disponible por el momento!";
                     $imagen = "images/no-photo.jpg";
                    }
                ?>
                <img src="<?php echo $imagen; ?>">
            </div>
            <div class="container_text">
                <h1><?php echo $nombre; ?></h1>
                <div class="detalles_dinero">
                    <?php if($descuento > 0){?>
                    <p>Antes<del><?php  echo MONEDA . number_format($precio, 0, ';', '.'); ?></del></p>
                    <h3>
                        Ahora a tan solo
                        <?php  echo MONEDA . number_format($precio_descuento, 0, ';', '.'); ?>                       
                    </h3>
                        <small>Con un descuento del <?php echo $descuento; ?>%</small>
                    <?php }else {?>
                        <p>A tan solo <?php  echo MONEDA . number_format($precio, 0, ';', '.'); ?></p> <!-- 1: numero de decimales, 2: El digito que va a separar los decimales, 3: El que va a separar los miles -->
                    <?php }?>
                    
                    </div>
                    <div class= "detalles_descripcion">
                        <p>
                            <?php echo $descripcion;?>
                        </p>
                    </div>

                <div class = "btns_detalles">
                <a href="productos_carrito.php"><button  class ="btn_comprar_ahora" type="button">Carrito de compras</button></a>

                    <button class ="btn_carrito" type="button" onclick="addProducto(<?php 
                    echo $id; ?>, '<?php echo $token_tmp; ?>')">Agregar al carrito</button>
                </div>
            </div>
        </section>
    </main>

    <script>
        function addProducto(id,token){ /* Lo trabajamos con ajax para que modifique los datos en tiempo real */
            let url = 'clases/carrito.php'
            let formData = new FormData() //Nos ayudara a colocar los datos por metodo POST
            formData.append('id', id)
            formData.append('token', token)

            fetch(url, { /* Trabajamos con la API fetch que nos proporciona java  */
                method: 'POST',
                body: formData,
                mode: 'cors'
                }).then(response => response.json()) /* Configuramos nuestra peticion ajax y lo enviamos con los datos mediante el methodo POST */
                .then(data => {
                    if(data.ok){
                        let elemento = document.getElementById("numero_carrito")
                        elemento.innerHTML = data.numero
                    }
                })
        }
    </script>
</body>
</html>
<?php 

require "config/config.php";
require "config/database.php";

$db = new Database();

$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, precio, descuento FROM productos WHERE activo=1");
$sql->execute();
//Con el fetchAll llamamos a todos los productos que estan en la tabla 
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);//Lo quiero por nombre de columnas 

/* session_destroy(); */
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
    <link rel="stylesheet" href="css/main_styles.css">
    <title>Supermercado ABA - siempre con usted</title>
</head>

<style>
.hora_fecha{
    font-size: 25px;
}

</style>
<body>
    <header>
        <div class="logo">
                <h3>Supermercado Online ABA</h3>

            </div>
            <div class = "hora_fecha">
             <?php
                 $fechaActual = date("d-m-Y");
                echo($fechaActual);
                ?>
                <h4 id="clock"></h4>
            </div>

            <nav class = "header_links">
            <a href="inicio.html" id = "salir_ingreso_productos";>Salir</a>
                <a href="productos_carrito.php" class="btn-carrito"> 
                    Carrito  <span id="numero_carrito"><?php echo $numero_carrito?>
                </a>
                <a id = "contactanos" href="sobre_nosotros.html">Sobre Nosotros</a>
            </nav>
    </header>
    <main>
        <section class = "products">
        <?php foreach($resultado as $row) { ?>
            <div class = "card">
                <?php 
                    $id = $row['id']; //Hacemos el poner imagenes de manera sistematizada
                    $imagen = "images/productos_super/" .$id. "/principal.png";

                    if(!file_exists($imagen)){ //Le damos una imagen pre-definida si el programa no encuentra la imagen que le corresponde
                        $imagen = "images/no-photo.jpg";
                    }
                ?>
            <img src="<?php echo $imagen; ?>">
                <h5 class = "card-title"><?php echo $row['nombre']; ?></h5>
                <p class = "card-text"><?php  echo "₡". number_format($row['precio'], 0, ';', '.'); ?></p> <!-- 1: numero de decimales, 2: El digito que va a separar los decimales, 3: El que va a separar los miles -->
                <div class = "btns">
                    <button class ="btn_carrito" type="button" onclick="addProducto(<?php 
                    echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">Agregar al carrito</button>

                    <a href="detalles.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>" class="detalles">Detalles</a>
                    <!-- El hmac nos ayuda a cifrar el producto con una contraseña -->
                </div>
            </div>
            <?php } ?>
        </section>
    </main>
    <footer>
        <div class = "footer_container">
            <div class = "conocenos">
                <h3>Conocenos</h3>
                <a href="condiciones/terminos_condiciones.html">Terminos y condiciones</a>
                
            </div>
            <div class = "ayuda">
                <h3>¿Neceitas ayuda?</h3>
                <a href="condiciones/como_comprar.html">¿Como comprar en Supermercado Online ABA?</a>
                
            </div>
            <div class = "servicios">
                <h3>Pagos de servicios</h3>
                <p>- Luz</p>
                <p>- Agua</p>
                <p>- Internet</p>
            </div>    
            <div class = "contactanos">
                <h3>Contactanos</h3>
                <p>Correo:</p> 
                <a href="mailto:supermercado.onlineaba@gmail.com">supermercado.onlineaba@gmail.com</a>
                <br>
                <p>Numero telefonico: </p> 
                <p>+506-2552-6456<p>
                
            </div>    
    </footer>


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

        function updateClock() {
        // Obtener la hora actual en el lado del cliente
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();

        // Formatear la hora en el formato deseado (por ejemplo, HH:mm:ss)
        var timeString = hours.toString().padStart(2, '0') + ':'
        + minutes.toString().padStart(2, '0') + ':'
        + seconds.toString().padStart(2, '0');

        // Mostrar la hora en el elemento con el ID "clock"
        document.getElementById('clock').innerText = timeString;

        }

        // Actualizar el reloj cada segundo (1000 milisegundos)
        setInterval(updateClock, 1000);

        // Actualizar el reloj inmediatamente al cargar la página
        updateClock();


    </script>

</body>
</html>
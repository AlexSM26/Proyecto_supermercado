<?php 
require "config/database.php";

$db = new Database();
$con = $db->conectar();

$nombre = null;
$descripcion = null;
$precio = NAN;
$descuento = NAN;
$activo = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
    $nombre = $_POST["nombre_producto"];
    $descripcion = $_POST["descripcion_producto"];
    $precio = $_POST["precio_producto"];
    $descuento = $_POST["descuento_producto"];

    if ($nombre == !null){
        $sql = $con->prepare("INSERT INTO productos(nombre,descripcion,precio,descuento, activo) VALUES ('$nombre', '$descripcion', '$precio', '$descuento','$activo')");
        $sql->execute();
    }else{
        ob_clean();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles_productos.css">
    <title>Supermercado ABA - administrador</title>
</head>
<header>
        <div class="logo">
                <h3>Ingreso de productos</h3>
            </div>
            <nav class = "header_links">
                <a href="inicio.html" id = "salir_ingreso_productos">Salir</a>
                <a id = "contactanos" href="#">Sobre Nosotros</a>
            </nav>
    </header>
<body>  
    <main>
    <section class = "products">
        <div class="container">
        <div class = "info_productos">
            <h1>Agregar productos</h1>
            <form action="" method="post">
                <div class = "nombre_producto">
                    <h2>Nombre del producto</h2>  
                    <input type="text" id = "nombre_prod" placeholder = "Producto" name = "nombre_producto" required>
                </div>
                <div class = "descripcion_producto">
                    <h2>Ingrese la descripcion del producto</h2>
                    <textarea name="descripcion_producto" id="descripcion_prod" cols="30" rows="10"
                    placeholder="Descripcion" required ></textarea>
                </div>
               <div = class="precio_producto">
                    <h2>Precio</h2>
                    <input type="number" id = "precio_prod" min="0" name = "precio_producto" required>
               </div>
                <div class="descuento_producto">
                    <h2>descuento</h2>
                    <input type="number" id = "desc_prod" min="0" value = "0" name = "descuento_producto">
                </div>
                <div class = "guardar_datos">
                    <button class = "btn_guardar">Guardar</button>
                </div>
                
            </form>
        </div>
    </div>
</section>
</main>
</body>
</html>
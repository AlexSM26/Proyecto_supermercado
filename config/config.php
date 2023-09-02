<?php 

define("KEY_TOKEN", "ABC.dhw-245*"); //Es un password para que pueda cifrar informacion
define("MONEDA", "₡");

session_start();

$numero_carrito = 0;
if(isset($_SESSION['carrito']['productos'])){
    $numero_carrito = count($_SESSION['carrito']['productos']);
}
?>
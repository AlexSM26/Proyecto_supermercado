<?php 
require '../config/config.php';

if(isset($_POST['id'])){

    $id = $_POST['id'];
    $token = $_POST['token'];
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if($token == $token_tmp){
        
        if(isset($_SESSION['carrito']['productos'][$id])){
            $_SESSION['carrito']['productos'][$id] += 1;
        }else{
            $_SESSION['carrito']['productos'][$id] = 1;
        }

        $datos['numero'] = count($_SESSION['carrito']['productos']);//Esto es para que me valla contabilizando los numeros
        $datos['ok'] = true;

    }else{
        $datos['ok'] = false;
    }

}else{
    $datos['ok'] = false;
}

echo json_encode($datos);//Regresamos el arreglo mediante el json_encode para trasformalo a json

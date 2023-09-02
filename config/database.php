<?php

class Database{
    private $hostname = "127.0.0.1:3307"; // Cambia esto si tu base de datos no está en el servidor local
    private $username = "root";
    private $password = "admin";
    private $dbname = "supermercado";
    private $charset = "utf8";

    function conectar(){
        try{
        $conexion = "mysql:host=" .$this->hostname.";dbname=" .$this->dbname.";charset=". $this->charset;

        $options = [ 
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, //Para que nos genere exepcioes en caso de que alla con la conexion
            PDO::ATTR_EMULATE_PREPARES=>false //Para que las preparaciones que le vamos a hacer a las consultas sean reales y no sean emuladas, asi van a tener seguridad 
        ];
        $pdo = new PDO($conexion, $this->username, $this->password, $options); //Definimos una variable que llame a la funcion PDO

        return $pdo;
        

        }catch(PDOException $e){
            echo "error conexion:".$e->getMessage();
            exit; //SI muestra un error para que cierre el proceso y poder revisar donde esta el error
        }
    }
}

?>

<?php
try{
    $dsn="mysql:host=localhost;dbname=fixed_assets;";
$username="root";
$password="";
$connection=new PDO($dsn,$username,$password);
// echo "connect";
}catch(PDOException $e){
   echo $e->getMessage();
}


?>
<?php
require "config.php" ;

//$id = $_GET['id'];
$table=$_GET['table'];

$stml = $connection->prepare("SELECT * FROM `$table`");
$stml->execute();
$items = $stml->fetchAll();

//print_r($category);
echo json_encode(array(
    $items
 ));
//json_encode($category);




?>
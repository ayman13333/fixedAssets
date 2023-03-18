<?php
require "config.php";



$table = $_GET['table'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stml = $connection->prepare("SELECT * FROM `$table` WHERE id=? ");
    $stml->execute(array($id));

    $category = $stml->fetch();
} else {
    $stml = $connection->prepare("SELECT * FROM `$table`");
    $stml->execute();

    $category = $stml->fetchAll();
}


//print_r($category);
echo json_encode(array(
    $category
));
//json_encode($category);

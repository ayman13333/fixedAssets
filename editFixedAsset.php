<?php
require "config.php" ;

$id = $_GET['id'];
    $stml = $connection->prepare("SELECT *  FROM `fixed_assets` WHERE id=?");
    $stml->execute(array($id));
    $fixedAsset = $stml->fetch();
    //str_replace('<br />', ' ', $fixedAsset['notes'] . "\n");
    $fixedAsset['notes']=str_replace('<br />', ' ', $fixedAsset['notes'] . "\n");

    echo json_encode(array(
        $fixedAsset
     ));
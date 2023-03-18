<?php
require "config.php" ;
require_once "phpqrcode/qrlib.php";


function qr_url()
{
    $table = $_POST['table'];

    if ($table == 'class_workshop_other') {
        //workshop reports qr code
        return "http://fixedassets.aba.vg/fixed_assets/reportsQrPage.php?type=workshop_id&id=";
    } elseif ($table == 'floors') {
        //floor report qr code
        return "http://fixedassets.aba.vg/fixed_assets/reportsFloorsQrPage.php?type=floor_id&id=";
    } elseif ($table = 'loca_assets') {
        //places report qr code
        return "http://fixedassets.aba.vg/fixed_assets/reportsBuildingQrPage.php?type=places_id&id=";
    }
}

function print_page()
{
    $table = $_GET['table'];
    if ($table == 'class_workshop_other') {
        //workshop reports qr code
        return "reportsQrPage.php?type=workshop_id";
    } elseif ($table == 'floors') {
        //floor report qr code
        return "reportsFloorsQrPage.php?type=floor_id";
    } elseif ($table = 'loca_assets') {
        //places report qr code
        return "reportsBuildingQrPage.php?type=places_id";
    }
}



//$id = $_GET['id'];
$table=$_POST['table'];
$num=$_POST['num'];
$name_ar=$_POST['name_ar'];
$name_en=$_POST['name_en'];

$target_dir = "photos/";
$qrcode = $target_dir . time() . ".png";
$qr_image = $target_dir . time() . ".png";
$qr_text = qr_url();


if($table=='cate_assets')
{
    $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num) VALUES(?,?,?) ");
    $result = $stml->execute(array($name_ar, $name_en, $num));
    
}
else
{
    $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num,qr_image) VALUES(?,?,?,?) ");
    $result = $stml->execute(array($name_ar, $name_en, $num, $qr_image));
    
    $last_id = $connection->lastInsertId();
    $url =  $qr_text . $last_id;
    //var_dump($url );
    
    QRcode::png($url, $qrcode, 'H', 4, 4);
}

//SELECT fields FROM table ORDER BY id DESC LIMIT 1;
if($result)
{
    $stml = $connection->prepare("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1 ");
    $stml->execute();

    $category = $stml->fetch();
}
//$category = $stml->fetchAll();

echo json_encode(array(
    $category
));






?>
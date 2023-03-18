<?php

require "dbActions.php";
require_once "phpqrcode/qrlib.php";

$not_unique_flag = 0;
$dataBaseActions = new dbActions($connection);

//get all categories
$categories = $dataBaseActions->getAllData('cate_assets');

//get all workshops
$workShops = $dataBaseActions->getAllData('class_workshop_other');

//get all floors
$floors = $dataBaseActions->getAllData('floors');
$id = $_GET['id'];

//get floor name for this page
$selectedFloor=$dataBaseActions->getOneRowFromSelectedTable('floors',$id);

$items = "";
$workShopsNames = "";
//$selectedFloor = "aa";
$allFloors = "";

$search_type = 'floor_id';
$search_type_dp = $search_type;
// $search_filed_dp = $_GET['search_field'];
$search_filed_dp = $_GET['id'];


$workShopsNames = $dataBaseActions->searchUniqueFixedAsset($search_type_dp, $search_filed_dp, 'workshop_id');

?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<?php require('reportsTable/reportStyles.php'); ?>

<div id="layoutSidenav_content" dir="rtl">

    <main>

        <div class="container-fluid px-4">
           

            <div class="card mb-4">
                <div class="card-header">


                </div>

                <div class="card-body">


                    <h1  style="text-align: center;font-size:x-large;margin-top: 2%;">
                    <?php echo   'تقرير الاصول الثابتة عن '. $selectedFloor['name_ar'] ?>
                        
                    </h1>
                    <div class="card mb-4" style="margin-top: 2%;">

                        <?php require 'reportsTable/floorReports.php' ?>

                    </div>
                </div>




            </div>
    </main>
</div>
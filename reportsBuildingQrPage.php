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
$selectedBulding=$_GET['id'];


$selectedFloor='1';

//get all building
$allBuildings=$dataBaseActions->getAllData('loca_assets');


$items = "";
$workShopsNames = "";
//$selectedFloor = "aa";
$allFloors = "";

$reportsBuildingFlag=1;

$search_type = 'places_id';
$search_type_dp = $search_type;
// $search_filed_dp = $_GET['search_field'];
$search_filed_dp = $_GET['id'];


$workShopsNames = $dataBaseActions->searchUniqueFixedAsset($search_type_dp, $search_filed_dp, 'places_id');

?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<?php require('reportsTable/reportStyles.php'); ?>

<div id="layoutSidenav_content" dir="rtl">

    <main>

        <div class="container-fluid px-4">
            <h1 class="mt-4 h1Style"> تقارير المباني</h1>

            <div class="card mb-4">
                <div class="card-header">
                    
               
                </div>

                <div class="card-body">


                        <?php
                      
                        $building_name = $dataBaseActions->filter($allBuildings, $selectedBulding);
                        echo "<h1 class='h1Style' >$building_name</h1>";

                        //floors
                        $allFloorsInOneBuilding=$dataBaseActions->searchUniqueFixedAsset('places_id', $selectedBulding, 'floor_id');

                       
                   
                        ?>
                   

                    <div class="card mb-4">

                            <?php foreach($allFloorsInOneBuilding as $floor) : ?>

                            <?php
                                 $selectedFloorName=$dataBaseActions->getOneRowFromSelectedTable('floors',$floor['floor_id']);

                                 $workShopsNames=$dataBaseActions->selectFromFixedAssetsWithMultipleConditions(
                                    $selectedBulding,$floor['floor_id'],'places_id','floor_id'
                                );

                                $workShopsNames=$dataBaseActions->searchUniqueFixedAsset('places_id', $selectedBulding, 'workshop_id');
         
                            ?>

                            <?php require 'reportsTable/floorReports.php' ?>

                            <?php endforeach ?>
                    </div>

                </div>

           


            </div>
    </main>
</div>
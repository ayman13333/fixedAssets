<?php
include "navbar.php";
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

//get all building
$allBuildings=$dataBaseActions->getAllData('loca_assets');


$items = "";
$workShopsNames = "";
$selectedBulding = "";
$selectedFloor="";
$selectesBuilding="";
$allFloors = "";

$reportsBuildingFlag=1;

if (isset($_GET['search'])) {

   
        if ($_GET['search_type'] == 'places_id') {
            $search_type = 'places_id';
        }

        $search_type_dp = $search_type;
        $search_filed_dp = $_GET['search_field'];

        //$workShopsNames = $dataBaseActions->searchUniqueFixedAsset($search_type_dp, $search_filed_dp, 'places_id');
        $selectesBuilding = $dataBaseActions->searchUniqueFixedAsset($search_type_dp, $search_filed_dp, 'places_id');
   
}

if (isset($_GET['search_field'])) {
    $selectedBulding = $_GET['search_field'];

    $selectedFloor=$selectedBulding;
}



?>
<div id="layoutSidenav_content">

    <main>

        <div class="container-fluid px-4">
            <h1 class="mt-4"> تقارير المباني</h1>

            <div class="card mb-4">
                <div class="card-header">
                    
                <?php require 'reportsTable/searchButtonForBuilding.php' ?>
                </div>

                <div class="card-body">


                    <h1 style="text-align: center;">
                        التقرير
                    </h1>
                
                    <?php if($selectesBuilding) : ?>
                        <?php
                      
                        $building_name = $dataBaseActions->filter($allBuildings, $selectedBulding);
                        echo "<h1 style='text-align:center;margin-top:10px'>$building_name</h1>";

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

                               // print_r($workShopsNames);

                              


                                  
                            ?>

                            <?php require 'reportsTable/floorReports.php' ?>

                            <?php endforeach ?>
                    </div>

                   

                    <?php else : ?>
                        <h1 style="text-align: center;">من فضلك اختر المبني المطلوب</h1>

                    <?php endif ?>
                </div>

            </div>
    </main>
</div>
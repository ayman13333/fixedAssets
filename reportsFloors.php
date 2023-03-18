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


$items = "";
$workShopsNames = "";
$selectedFloor = "";
$allFloors = "";

if (isset($_GET['search'])) {

    if ($_GET['search_field'] == 'all') {
        $allFloors = $dataBaseActions->getAllData('floors');
    } else {
        if ($_GET['search_type'] == 'floor_id') {
            $search_type = 'floor_id';
        }

        $search_type_dp = $search_type;
        $search_filed_dp = $_GET['search_field'];

        $workShopsNames = $dataBaseActions->searchUniqueFixedAsset($search_type_dp, $search_filed_dp, 'workshop_id');
    }
}

if (isset($_GET['search_field'])) {
    $selectedFloor = $_GET['search_field'];
}



?>
<div id="layoutSidenav_content">

    <main>

        <div class="container-fluid px-4">
            <h1 class="mt-4"> تقارير الادوار</h1>

            <div class="card mb-4">
                <div class="card-header">
                    
                <?php require 'reportsTable/searchButton.php' ?>
                </div>

                <div class="card-body">


                    <h1 style="text-align: center;">
                        التقرير
                    </h1>
                    <div class="card mb-4">

                    <?php if ($allFloors) :  ?>
                        

                        

                        <?php foreach ($allFloors as $oneFloor) : ?>

                            <?php
                        $floor_name = $dataBaseActions->filter($floors, $oneFloor['id']);
                        echo "<h1 style='text-align:center;margin-top:10px'>$floor_name</h1>";

                        $workShopsNames = $dataBaseActions->searchUniqueFixedAsset('floor_id', $oneFloor['id'], 'workshop_id');

                        ?>

                            <?php require 'reportsTable/floorReports.php' ?>
                            <br>
                            <hr>
                            

                        <?php endforeach ?>

                        


                    <?php else : ?>

                        

                            <?php require 'reportsTable/floorReports.php' ?>

                       

                    <?php endif ?>

                    </div>
                </div>

           


            </div>
    </main>
</div>
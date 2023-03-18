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
$selectedWorkShop = "";

$items = "";

if (isset($_GET['search'])) {

    if ($_GET['search_type'] == 'class_workshop_other') {
        $search_type = 'workshop_id';
    }

    $search_type_dp = $search_type;
    $search_filed_dp = $_GET['search_field'];
    $selectedWorkShop = $_GET['search_field'];

    $items = $dataBaseActions->searchFixedAsset($search_type_dp, $search_filed_dp);
}



?>
<div id="layoutSidenav_content">

    <main>

        <div class="container-fluid px-4">
            <h1 class="mt-4"> تقارير الورش</h1>

            <div class="card mb-4">
                <div class="card-header">
                  <?php require 'reportsTable/workShopsSearchButton.php'; ?>

                </div>
                <div class="card-body">

                    <h1 style="text-align: center;">
                        التقرير
                    </h1>

                    <?php if($selectedWorkShop) : ?>
                        <?php
                      
                        $workshop_name = $dataBaseActions->filter($workShops, $selectedWorkShop);
                        echo "<h1 style='text-align:center;margin-top:10px'>$workshop_name</h1>";
                        ?>
                    <?php endif ?>

                    <table class="table table-striped" id="myTable">

                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>
                                    الاصل بالعربية
                                </th>
                                <th>
                                    التصنيف
                                </th>

                                <th>العدد</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($items) : ?>
                                <?php foreach ($items as $item) : ?>
                                    <tr>
                                        <td><?= $item['num'] ?></td>
                                        <td><?= $item['name_ar'] ?></td>
                                        <td>
                                            <?php $category_name = $dataBaseActions->filter($categories, $item['categories_id']);
                                            echo $category_name;
                                            ?>
                                        </td>

                                        <td> <?= $item['quantity'] ?> </td>
                                    </tr>
                                <?php endforeach ?>

                            <?php else : ?>

                                <tr>
                                    <td colspan="4">
                                        <p style="text-align: center;font-size: revert;">لا توجد بيانات</p>
                                    </td>
                                </tr>

                            <?php endif ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </main>
</div>
<script>
    // var table = 'class_workshop_other';

    // //delete all options from select
    // document.getElementById('searchField').innerHTML = "";
    // // var searchField = document.getElementById('searchField').innerHTML ="";

    // var xmlhttp = new XMLHttpRequest();
    // xmlhttp.onreadystatechange = function() {
    //     if (this.readyState == 4 && this.status == 200) {

    //         let category = JSON.parse(this.response);

    //         console.log(category[0]);

    //         var items = category[0];
    //         var searchField = document.getElementById('searchField');

    //         for (var i = 0; i < items.length; i++) {
    //             var opt = document.createElement('option');
    //             opt.value = items[i]['id'];
    //             opt.innerHTML = items[i]['name_ar'];
    //             searchField.appendChild(opt);
    //         }

    //     }
    // };
    // xmlhttp.open("GET", "getAll.php?table=" + table, true);
    // xmlhttp.send();

    function mianField(event) {
        var table = event.target.value;

        //delete all options from select
        document.getElementById('searchField').innerHTML = "";
        // var searchField = document.getElementById('searchField').innerHTML ="";

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                let category = JSON.parse(this.response);

                console.log(category[0]);

                var items = category[0];
                var searchField = document.getElementById('searchField');

                for (var i = 0; i < items.length; i++) {
                    var opt = document.createElement('option');
                    opt.value = items[i]['id'];
                    opt.innerHTML = items[i]['name_ar'];
                    searchField.appendChild(opt);
                }

            }
        };
        xmlhttp.open("GET", "getAll.php?table=" + table, true);
        xmlhttp.send();

        //console.log(event.target.value);
    }
</script>
<?php
header('Content-Type: text/html; charset=utf-8');
require "config.php";
require "dbActions.php";
require_once "phpqrcode/qrlib.php";

$not_unique_flag = 0;


$dataBaseActions = new dbActions($connection);

//get all categories
$categories = $dataBaseActions->getAllData('cate_assets');

//get all places
$locations = $dataBaseActions->getAllData('loca_assets');

//get all floors
$floors = $dataBaseActions->getAllData('floors');

//get all workshops
$workShops = $dataBaseActions->getAllData('class_workshop_other');



if ($_GET['type'] == 'workshop_id') {
    $search_type = 'workshop_id';
}

$search_type_dp = $search_type;
$search_filed_dp = $_GET['id'];

$items = $dataBaseActions->searchFixedAsset($search_type_dp, $search_filed_dp);

?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<?php require('reportsTable/reportStyles.php'); ?>



<div id="" style="text-align: right; direction: rtl;">

    <main>

        <div class="container-fluid px-4">


            <div class="card mb-4">

                <div class="card-body" style="margin-top: 4%;">

                    <h1 class="h1Style">
                        تقرير الورشة
                    </h1>

                    <table class="table" id="" style="margin-top: 3%;">

                        <thead>
                            <tr id="pdfTd">
                                <th id="pdfTd">الرقم</th>
                                <th id="pdfTd">
                                    الاصل بالعربية
                                </th>
                                <th id="pdfTd">
                                    التصنيف
                                </th>

                                <th>العدد</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($items) : ?>
                                <?php foreach ($items as $item) : ?>
                                    <tr id="pdfTd">
                                        <td id="pdfTd"><?= $item['num'] ?></td>
                                        <td id="pdfTd"><?= $item['name_ar'] ?></td>

                                        <td id="pdfTd">
                                            <?php $category_name = $dataBaseActions->filter($categories, $item['categories_id']);
                                            echo $category_name;
                                            ?>
                                        </td>

                                        <td id="pdfTd"> <?= $item['quantity'] ?> </td>
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
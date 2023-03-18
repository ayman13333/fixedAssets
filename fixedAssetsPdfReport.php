<?php
require "dbActions.php";

$dataBaseActions = new dbActions($connection);

 //get all categories
 $categories = $dataBaseActions->getAllData('cate_assets');

 //get all places
 $locations = $dataBaseActions->getAllData('loca_assets');

 //get all floors
 $floors = $dataBaseActions->getAllData('floors');

 //get all workshops
 $workShops = $dataBaseActions->getAllData('class_workshop_other');


if (isset($_GET['search_type'])) {
    
    $search_type=$_GET['search_type'];
    $search_field=$_GET['search_field'];

    $items = $dataBaseActions->searchFixedAsset($search_type,$search_field);

} else {
    //all
    $items = $dataBaseActions->getAllData('fixed_assets');
}

 //get total
 $total = $dataBaseActions->getTotal($items);


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
                        تقرير الاصول الثابتة
                    </h1>

                    <!-- pdfTd -->
                    <table class="table" id="">
                        <thead>
                            <tr id="pdfTd">
                                <th id="pdfTd">الرقم</th>
                                <th id="pdfTd">
                                    الاصل بالعربية
                                </th>
                                <th id="pdfTd"> الاصل بالانجليزية
                                </th>
                                <th id="pdfTd">الكمية</th>
                                <th id="pdfTd"> تاريخ الشراء</th>
                                <th id="pdfTd"> مبلغ الشراء</th>
                                <th id="pdfTd"> تاريخ الالغاء</th>
                                <th id="pdfTd"> التصنيف</th>
                                <th id="pdfTd"> المكان</th>
                                <th id="pdfTd"> الطابق</th>
                                <th id="pdfTd"> الورشة</th>
                                <th id="pdfTd">قيمة الاهلاك</th>

                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($items) : ?>

                                <?php foreach ($items as $item) : ?>
                                    <tr id="pdfTd">
                                        <td id="pdfTd">
                                            <?= $item['num'] ?>
                                        </td>
                                        <td id="pdfTd"> <?= $item['name_ar'] ?> </td>
                                        <td id="pdfTd"> <?= $item['name_en'] ?> </td>
                                        <td id="pdfTd" class="total" style="text-align: center"> <?= $item['quantity'] ?> </td>
                                        <td id="pdfTd"> <?= $item['date_purchase'] ?> </td>
                                        <td id="pdfTd" style="text-align: center"> <?= $item['price_purchase'] ?> </td>
                                        <td id="pdfTd"> <?= $item['cancel_date'] ?> </td>

                                        </td>
                                        <td id="pdfTd">
                                            <?php $category_name = $dataBaseActions->filter($categories, $item['categories_id']);
                                            echo $category_name;
                                            ?>
                                        </td>
                                        <td id="pdfTd">
                                            <?php $location_name = $dataBaseActions->filter($locations, $item['places_id']);
                                            echo $location_name;
                                            ?>
                                        </td>
                                        <td id="pdfTd">
                                            <?php
                                            $floor_name = $dataBaseActions->filter($floors, $item['floor_id']);
                                            echo $floor_name;
                                            ?>
                                        </td>
                                        <td id="pdfTd">
                                            <?php
                                            $workshop_name = $dataBaseActions->filter($workShops, $item['workshop_id']);
                                            echo $workshop_name;
                                            ?>
                                        </td>
                                        <td id="pdfTd" style="text-align: center;">
                                            <?= $item['depreciation_value'] ?>
                                        </td>



                                    </tr>
                                <?php endforeach ?>

                            <?php endif ?>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </main>
</div>
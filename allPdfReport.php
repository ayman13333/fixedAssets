<?php
session_start();
ob_start();
require "dbActions.php";


$dataBaseActions = new dbActions($connection);

$error = 0;
$table = $_GET['table'];

if (isset($_GET['search_type']) && $_GET['search_type'] !=='all') {

    // $_SESSION["search_type"] =  $search_type_dp; 
    // //urldecode($_GET['search_type']); 
    // //$search_filed_dp = str_replace('+', '-', $_GET['search_field']);
    // //$search_filed_dp=$_GET['search_field'];
    // $_SESSION["search_field"] =  $search_filed_dp;

     $search_type_dp = $_GET['search_type'];
    // $search_filed_dp = str_replace('+', ' ', $_GET['search_field']);
     $search_filed_dp = trim($_GET['search_field'], "$");
 //   $search_filed_dp = $_SESSION["search_field"];
  //  $search_type_dp = $_SESSION["search_type"];

    //echo $search_type_dp;
    //echo $search_filed_dp;

   // $_SESSION["search_field"]='';
   // $_SESSION["search_type"]='';

    $stml = $connection->prepare("SELECT * FROM `$table` WHERE $search_type_dp =? ");
    $stml->execute(array($search_filed_dp));

    $items = $stml->fetchAll(PDO::FETCH_ASSOC);
} else {

    $stml = $connection->prepare("SELECT * FROM `$table`");
    $stml->execute();

    $items = $stml->fetchAll();
}



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
                        <?php
                        if ($table == 'loca_assets')
                            echo 'جدول الاماكن';
                        elseif ($table == 'floors')
                            echo 'جدول الادوار';
                        elseif ($table == 'class_workshop_other')
                            echo 'جدول ادارة الورش والصفوف والخدمات الاخري';
                        elseif ($table == 'cate_assets')
                            echo 'جدول التصنيفات';
                        ?>
                    </h1>

                    <table class="table" id="" style="margin-top: 8%;">

                        <thead>
                            <tr id="pdfTd">
                                <th id="pdfTd">الرقم</th>
                                <th id="pdfTd">
                                    الاسم بالعربية
                                </th>
                                <th id="pdfTd">
                                    الاسم بالانجليزية
                                </th>

                                <?php if ($table == 'cate_assets') : ?>
                                    <th id="pdfTd">
                                        نسبة الاهلاك
                                    </th>
                                <?php endif ?>


                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($items) : ?>
                                <?php foreach ($items as $item) : ?>
                                    <tr id="pdfTd">
                                        <td id="pdfTd"><?= $item['num'] ?></td>
                                        <td id="pdfTd"><?= $item['name_ar'] ?></td>
                                        <td id="pdfTd"><?= $item['name_en'] ?></td>
                                        <?php if ($table == 'cate_assets') : ?>
                                            <td id="pdfTd">
                                                <?= $item['depreciation_percentage'] ?>
                                            </td>
                                        <?php endif ?>
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


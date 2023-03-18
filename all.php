<?php
// session_start();
// ob_start();
include "navbar.php";
require_once "phpqrcode/qrlib.php";
//require "vendor/autoload.php";
require "dbActions.php";
require_once __DIR__ . '/vendor/autoload.php';




$mpdf = new \Mpdf\Mpdf();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;


$dataBaseActions = new dbActions($connection);

$error = 0;
$table = $_GET['table'];
$redirect = "location:all.php?table=$table";

$search_type_dp='';
$search_filed_dp='';
$workShopIdForSearch='';

$stml = $connection->prepare("SELECT * FROM `$table`");
$stml->execute();

$categories = $stml->fetchAll();

$allcategories = $categories;


function qr_url()
{
    $table = $_GET['table'];

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

//for delete
if (isset($_POST['delete'])) {

    $id = $_POST['id'];

    $table = $_GET['table'];

    $search_type_dp = '';

    if ($table == 'class_workshop_other') {
        //workshop reports qr code
        $search_type_dp = 'workshop_id';
    } elseif ($table == 'floors') {
        //floor report qr code
        $search_type_dp = 'floor_id';
    } elseif ($table = 'loca_assets') {
        //places report qr code
        $search_type_dp = 'places_id';
    }


    $result = $dataBaseActions->searchFixedAsset($search_type_dp, $id);

    if ($result) {
        $error = 1;
    } else {
        $stml = $connection->prepare("DELETE FROM `$table` WHERE `id`=?");
        $stml->execute(array($id));

        $count = $stml->rowCount();
        // echo "<h1> $count </h1> ";
        if ($count == 1) {
            header($redirect);
            // $error=1;
        } else {
            $error = 1;
        }
    }
}
//for search
if (isset($_GET['search'])) {
    $search_type_dp = $_GET['search_type'];
    $search_filed_dp = $_GET['search_field'];

    $workShopIdForSearch=urlencode($search_filed_dp);
   

    if ($search_type_dp == 'all') {
        $categories = $allcategories;
    } else {
        $stml = $connection->prepare("SELECT * FROM `$table` WHERE $search_type_dp =? ");
        //var_dump($stml);
        $stml->execute(array($search_filed_dp));
        $categories = $stml->fetchAll(PDO::FETCH_ASSOC);
    }
}
//for add
if (isset($_POST['add'])) {
    // echo 'add';
    $name_ar = $_POST['name_ar'];
    $name_en = $_POST['name_en'];
    $num = $_POST['num'];

    $target_dir = "photos/";
    $qrcode = $target_dir . time() . ".png";
    $qr_image = $target_dir . time() . ".png";
    $qr_text = qr_url();


    $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num,qr_image) VALUES(?,?,?,?) ");
    $result = $stml->execute(array($name_ar, $name_en, $num, $qr_image));

    $last_id = $connection->lastInsertId();
    $url =  $qr_text . $last_id;
    //var_dump($url );

    QRcode::png($url, $qrcode, 'H', 4, 4);

    //var_dump($result);

    if ($result) {
        header($redirect);
    }
}
//for edit
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $name_ar = $_POST['name_ar'];
    $name_en = $_POST['name_en'];
    $num = $_POST['num'];


    $stml = $connection->prepare(" UPDATE `$table` 
        SET  `name_ar`=?, `name_en`=? ,`num`=?
        WHERE `id`=?
        ");
    $stml->execute(array($name_ar, $name_en, $num, $id));

    header($redirect);
}

//export from excel
if (isset($_POST['exportFromExcel'])) {

    $fileName = $_FILES['excel']['name'];
    $fileExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileExtension));

    $newFileName = date("y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;
    $targetDirectory = "uploads/" . $newFileName;

    move_uploaded_file($_FILES['excel']["tmp_name"], $targetDirectory);

    //$reader = new SpreadsheetReader($targetDirectory);
    $reader=dbActions::reader($targetDirectory);
   // return;

    $count_row = 0;

    foreach ($reader as $key => $row) {
        if ($count_row == 0) {
            $count_row = 1;
        } else {
            $num_excel = $row[0];
            $name_ar_excel = $row[1];
            $name_en_excel = $row[2];

            $target_dir = "photos/";
            $qrcode = $target_dir . $key . time() . ".png";
            $qr_image = $target_dir . $key . time() . ".png";
            $qr_text = qr_url();

            $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num,qr_image) VALUES(?,?,?,?) ");
            $result = $stml->execute(array($name_ar_excel, $name_en_excel, $num_excel, $qr_image));

            $last_id = $connection->lastInsertId();
            $url = $qr_text . $last_id;
            //var_dump($url );

            QRcode::png($url, $qrcode, 'H', 4, 4);

            $url = '';
            $qrcode = '';


            // $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num) VALUES(?,?,?) ");
            // $result = $stml->execute(array($name_ar_excel, $name_en_excel, $num_excel));
        }
    }

    header($redirect);
}

//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$


//print_report
if (isset($_GET['print'])) {


    $id = $_GET['print'];
    $a = file_get_contents($dataBaseActions->printPDFUrl() . print_page() . "&id=$id");

    $mpdf->WriteHTML($a);
    ob_end_clean();
    $mpdf->Output();
}

//print_table
if (isset($_POST['print_table'])) {

    if (isset($_GET['search_type'])) {
       
        $search_type_dp =$_GET['search_type'];

        $a = file_get_contents($dataBaseActions->printPDFUrl() . "allPdfReport.php?table=$table&search_type=$search_type_dp&search_field=$$workShopIdForSearch");
    } else {
        $a = file_get_contents($dataBaseActions->printPDFUrl() . "allPdfReport.php?table=$table");
    }
 
    $mpdf->WriteHTML($a);
    ob_end_clean();
    $mpdf->Output();
}

//oneAllReport
if(isset($_POST['oneAllReport']))
{
  $id=$_POST['id'];

   $a = file_get_contents($dataBaseActions->printPDFUrl() . "oneAllPdfReport.php?id=$id&table=$table");
   $mpdf->WriteHTML($a);
   ob_end_clean();
   $mpdf->Output();
}


?>




<div id="layoutSidenav_content">

    <main>

        <div class="container-fluid px-4">

            <h1 class="mt-4">
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

            <div class="card mb-4">

            </div>
            <div class="card mb-4">
                <div class="card-header">

                    <?php if ($error) : ?>
                        <div class="alert alert-danger" role="alert" style="text-align: center;">
                            لا يمكن مسح هذا الحقل لان لديه حركات
                        </div>

                    <?php endif ?>

                    <div style="padding-top: 10px;padding-bottom: 10px;">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalLong">
                            اضافة
                        </button>

                        <!-- import from excel -->
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModalLong2" style="margin-right: 2%;">
                            استيراد من اكسل
                        </button>

                    </div>


                    <!--add Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                        <?php

                                        if ($table == 'loca_assets')
                                            echo 'اضافة مكان';
                                        elseif ($table == 'floors')
                                            echo 'اضافة دور';
                                        elseif ($table == 'class_workshop_other')
                                            echo 'اضافة ورشة';

                                        ?>
                                    </h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form method="post" name="add" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">الرقم</label>
                                            <input type="num" class="form-control" name="num" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">الاسم بالعربية</label>
                                            <input type="text" class="form-control" name="name_ar" required>
                                        </div>
                                        <div class="form-group" style="margin-top: 10px">
                                            <label for="exampleInputEmail1">الاسم بالانجليزية</label>
                                            <input type="text" class="form-control" name="name_en" required>
                                        </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="add" class="btn btn-success" style="margin-left: 69%">اضافة</button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">اغلاق</button>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- search button -->

                    <div style="display: flex;">

                        <form class="form-inline my-2 my-lg-0" name="search" method="GET" action="all.php?" style="margin-top: 10px !important;display: flex;">

                            <input type="hidden" name="table" value="<?= $table ?>">
                            <input class="form-control mr-sm-2" type="search" placeholder="بحث" aria-label="Search" name="search_field" style="">

                            <select class="btn btn-outline-dark my-2 my-sm-0" name="search_type" style="margin-right: 1%;">
                                <option value="all">الكل</option>
                                <option value="num">الرقم</option>
                                <option value="name_ar">الاسم بالعربية</option>
                                <option value="name_en">الاسم بالانجليزية</option>

                            </select>

                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search" style="margin-right: 2%;">بحث</button>
                        </form>

                        <form style="margin-top: 10px !important" method="post" target="_blank">

                            <button class="btn btn-dark my-2 my-sm-0" type="submit" name="print_table" style="margin-right: 10%;">طباعة</button>

                        </form>

                    </div>




                    <!--import from excel Modal -->
                    <div class="modal fade" id="exampleModalLong2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">استيراد من اكسل</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form method="post" enctype="multipart/form-data">


                                        <input type="file" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>

                                </div>
                                <div class="modal-footer">

                                    <button type="submit" name="exportFromExcel" class="btn btn-dark" style="margin-left: 69%"> استيراد </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">اغلاق</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>
                                    الاسم بالعربية
                                </th>
                                <th> الاسم بالانجليزية
                                </th>
                                <th>
                                    تحكم
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($categories) : ?>
                                <?php foreach ($categories as $item) : ?>
                                    <tr>
                                        <td>


                                            <form method="GET">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                                                <a onclick="edit(event)" href="" type="button" data-bs-toggle="modal" data-bs-target="#exampleModalLong1">
                                                    <?= $item['num'] ?>
                                                </a>

                                            </form>

                                        </td>
                                        <td> <?= $item['name_ar'] ?> </td>
                                        <td> <?= $item['name_en'] ?> </td>


                                        <td>
                                            <form method="GET">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button onclick="edit(event)" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModalLong1">
                                                    تعديل
                                                </button>

                                            </form>


                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleModalLong1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                                <?php

                                                                if ($table == 'loca_assets')
                                                                    echo 'تعديل مكان';
                                                                elseif ($table == 'floors')
                                                                    echo 'تعديل دور';
                                                                elseif ($table == 'class_workshop_other')
                                                                    echo 'تعديل ورشة';

                                                                ?>
                                                            </h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <img src="" id="qr_image">

                                                            <a id="downloadBarCodeLink" href="" style="display: block" download>
                                                                تحميل الكود
                                                            </a>

                                                            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">

                                                                <input id="edit_id" type="hidden" class="form-control" name="id" value="" readonly>
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">الرقم</label>
                                                                    <input id="number" type="text" class="form-control" name="num" value="">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">الاسم بالعربية</label>
                                                                    <input type="text" class="form-control" id="name_ar" aria-describedby="emailHelp" name="name_ar" value="" required>
                                                                </div>
                                                                <div class="form-group" style="margin-top: 10px">
                                                                    <label for="exampleInputEmail1">الاسم بالانجليزية</label>
                                                                    <input type="text" class="form-control" id="name_en" aria-describedby="emailHelp" name="name_en" value="" required>
                                                                </div>


                                                        </div>
                                                        <div class="modal-footer" style="display: flex;">


                                                            <button type="submit" name="edit" class="btn btn-warning">تعديل</button>

                                                            </form>

                                                            <a href="" target="_blank" id="print_button" class="btn btn-dark">
                                                            طباعة تقرير
                                                            </a>

                                                            <form method="post" target="_blank">
                                                                <input type="hidden" name="id" id="oneAllReport" >

                                                                <button type="submit" name="oneAllReport"  class="btn btn-dark">طباعة</button>

                                                            </form>


                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-left: 30%;">اغلاق</button>

                                                            

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            <form method="POST" name="delete" action="<?php $_SERVER['PHP_SELF'] ?>">

                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button id="delete" name="delete" onclick="confirmDelete(event)" type="submit" class="btn btn-danger">
                                                    حذف </button>
                                            </form>
                                        </td>
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

    //console.log(window.location.href);

    function edit(event) {
        let id = event.target.previousElementSibling.value;
        let url = window.location.href;
        let table = url.split("?table=");
        table = table[1];
        //console.log(table)

        let print_url = `&print=`;
        let print_url_page = '';



        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                let category = JSON.parse(this.response);

                var id = category[0]['id'];
                var name_ar = category[0]['name_ar'];
                var name_en = category[0]['name_en'];
                var num = category[0]['num'];

                //qr_image downloadBarCodeLink  print_button
                print_url_page = '';
                print_url_page = print_url + id;

                document.getElementById("edit_id").value = id;
                document.getElementById("name_ar").value = name_ar;
                document.getElementById("name_en").value = name_en;
                document.getElementById("number").value = num;
                document.getElementById("qr_image").src = category[0]['qr_image'];
                document.getElementById("downloadBarCodeLink").href = category[0]['qr_image'];
                document.getElementById("print_button").href = '';
                document.getElementById("print_button").href += print_url_page;
                //document.getElementById("print_button").href += print_url_page;
                //oneAllReport
                document.getElementById("oneAllReport").value = id;


            }
        };
        xmlhttp.open("GET", "edit.php?id=" + id + "&table=" + table, true);
        xmlhttp.send();

    }
</script>



<?php include_once 'footer.php' ?>
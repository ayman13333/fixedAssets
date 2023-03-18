<?php
include "navbar.php";
require "dbActions.php";
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

$error = 0;
$table = 'cate_assets';
$redirect = "location:index.php";
$workShopIdForSearch = "";

$stml = $connection->prepare("SELECT * FROM `$table`");
$stml->execute();

$categories = $stml->fetchAll();

$allcategories = $categories;

$dataBaseActions = new dbActions($connection);



//for delete
if (isset($_POST['delete'])) {

    $id = $_POST['id'];
    $search_type_dp = 'categories_id';

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
        }
    }
}
//for search
if (isset($_GET['search'])) {
    $search_type_dp = $_GET['search_type'];
    $search_filed_dp = $_GET['search_field'];
    $workShopIdForSearch = urlencode($search_filed_dp);

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
    $depreciation_percentage = $_POST['depreciation_percentage'];


    $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num,depreciation_percentage) VALUES(?,?,?,?) ");
    $result = $stml->execute(array($name_ar, $name_en, $num, $depreciation_percentage));

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
    $depreciation_percentage = $_POST['depreciation_percentage'];


    $stml = $connection->prepare(" UPDATE `$table` 
        SET  `name_ar`=?, `name_en`=? ,`num`=? ,`depreciation_percentage`=?
        WHERE `id`=?
        ");
    $stml->execute(array($name_ar, $name_en, $num, $depreciation_percentage, $id));

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

    // require "excelReader/excel_reader2.php";
    // require "excelReader/SpreadsheetReader.php";

    $reader = new SpreadsheetReader($targetDirectory);

    //return;

    $count_row = 0;

    foreach ($reader as $key => $row) {
        if ($count_row == 0) {
            $count_row = 1;
        } else {
            $num_excel = $row[0];
            $name_ar_excel = $row[1];
            $name_en_excel = $row[2];

            $stml = $connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num) VALUES(?,?,?) ");
            $stml->execute(array($name_ar_excel, $name_en_excel, $num_excel));
        }
    }

    //header($redirect);
    echo "<script>history.back()</script>";
}


//print_table
if (isset($_POST['print_table'])) {
    if (isset($_GET['search_type'])) {
        $search_type_dp = $_GET['search_type'];
        $search_filed_dp = $_GET['search_field'];
        $a = file_get_contents($dataBaseActions->printPDFUrl() . "allPdfReport.php?table=$table&search_field=$workShopIdForSearch&search_type=$search_type_dp");
    } else {
        $a = file_get_contents($dataBaseActions->printPDFUrl() . "allPdfReport.php?table=$table");
    }

    $mpdf->WriteHTML($a);
    ob_end_clean();
    $mpdf->Output();
}

//oneAllReport
if (isset($_POST['oneAllReport'])) {
    $id = $_POST['id'];

    $a = file_get_contents($dataBaseActions->printPDFUrl() . "oneAllPdfReport.php?id=$id&table=cate_assets");
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


                    <!-- add  Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">اضافة </h5>
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
                                        <div class="form-group" style="margin-top: 10px">
                                            <label for="exampleInputEmail1">نسبة الاهلاك</label>
                                            <input type="number" class="form-control" name="depreciation_percentage">
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

                        <form class="form-inline my-2 my-lg-0" name="search" method="GET" action="" style="margin-top: 10px !important;display: flex;">

                            <input class="form-control mr-sm-2" type="search" placeholder="بحث" aria-label="Search" name="search_field" style="">

                            <select class="btn btn-outline-dark my-2 my-sm-0" name="search_type" style="margin-right: 1%;">
                                <option value="all">الكل</option>
                                <option value="num">الرقم</option>
                                <option value="name_ar">الاسم بالعربية</option>
                                <option value="name_en">الاسم بالانجليزية</option>
                                <option value="depreciation_percentage">نسبة الاهلاك</option>

                            </select>

                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search" style="margin-right: 2%;">بحث</button>

                        </form>

                        <form style="margin-top: 10px !important" method="post" target='_blank'>
                            <button class="btn btn-dark my-2 my-sm-0" type="submit" name="print_table" style="margin-right: 10%;">طباعة</button>
                        </form>

                    </div>



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


                                        <input type="file" name="excel" required>

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
                                    نسبة الاهلاك
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
                                        <td> <?= $item['depreciation_percentage'] ?> </td>

                                        <td>
                                            <form method="GET">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button onclick="edit(event)" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModalLong1">
                                                    تعديل
                                                </button>

                                            </form>


                                            <!-- edit  Modal -->
                                            <div class="modal fade" id="exampleModalLong1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">تعديل </h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
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
                                                                <div class="form-group" style="margin-top: 10px">
                                                                    <label for="exampleInputEmail1">نسبة الاهلاك</label>
                                                                    <input type="number" class="form-control" id="depreciation_percentage" aria-describedby="emailHelp" name="depreciation_percentage" value="">
                                                                </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="edit" class="btn btn-warning" style="">تعديل</button>

                                                            </form>

                                                            <form method="post" target="_blank">
                                                                <input type="hidden" name="id" id="oneAllReport">

                                                                <button type="submit" name="oneAllReport" class="btn btn-dark">طباعة</button>

                                                            </form>

                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-left: 55%;">اغلاق</button>


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
    function edit(event) {
        let id = event.target.previousElementSibling.value;
        let url = window.location.href;
        let table = "cate_assets";


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                let category = JSON.parse(this.response);

                var id = category[0]['id'];
                var name_ar = category[0]['name_ar'];
                var name_en = category[0]['name_en'];
                var num = category[0]['num'];
                var depreciation_percentage = category[0]['depreciation_percentage'];

                //oneAllReport

                document.getElementById("edit_id").value = id;
                document.getElementById("name_ar").value = name_ar;
                document.getElementById("name_en").value = name_en;
                document.getElementById("number").value = num;
                document.getElementById("depreciation_percentage").value = depreciation_percentage;
                document.getElementById("oneAllReport").value = id;

            }
        };
        xmlhttp.open("GET", "edit.php?id=" + id + "&table=" + table, true);
        xmlhttp.send();

    }
</script>



<?php include_once 'footer.php' ?>
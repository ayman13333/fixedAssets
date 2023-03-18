<?php
require "dbActions.php";

$dataBaseActions = new dbActions($connection);

$id = $_GET['id'];

//get all categories
$categories = $dataBaseActions->getAllData('cate_assets');

//get all places
$locations = $dataBaseActions->getAllData('loca_assets');

//get all floors
$floors = $dataBaseActions->getAllData('floors');

//get all workshops
$workShops = $dataBaseActions->getAllData('class_workshop_other');

$item = $dataBaseActions->getOneFixedAsset($id);
?>
<style>
    .rigth {
        padding: 10px;
    }

    .left {
        padding: 10px;
    }

    .width {
        width: 50%;
        margin-right: 25%;
    }

    .qr_image {
        margin-top: 20px;
        /* heigth:10px; */
        display: block;
        margin-right: 30%
    }
</style>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<div dir="rtl">
    <div>
        <div>
            <!-- <h5 class="modal-title" style=""> الاصل</h5> -->

        </div>

        <div>



            <img src="<?= $item['qr_image'] ?>" class="qr_image">


            <div class="width">

                <div class="rigth">
                    <label for="exampleInputEmail1">الرقم</label>
                    <input type="text" class="form-control" value="<?= $item['num'] ?>">
                </div>

                <div class="left">
                    <label for="exampleInputEmail1">الكمية</label>
                    <input type="number" class="form-control" value="<?= $item['quantity'] ?>">
                </div>


                <div class="rigth">
                    <label for="exampleInputEmail1">اسم الاصل بالعربية</label>
                    <input type="text" class="form-control" value="<?= $item['name_ar'] ?>">

                </div>
                <div class="left">
                    <label for="exampleInputEmail1">اسم الاصل بالانجليزية</label>
                    <input type="text" class="form-control" value="<?= $item['name_en'] ?>">

                </div>


                <div class="rigth">
                    <label for="exampleInputEmail1">تاريخ الشراء</label>
                    <input type="text" class="form-control" value="<?= $item['date_purchase'] ?>">
                </div>

                <div class="left">
                    <div class="form-group">
                        <label for="exampleInputEmail1">تاريخ الالغاء</label>
                        <input type="text" class="form-control" value="<?= $item['cancel_date'] ?>">
                    </div>
                </div>

                <div class="rigth">
                    <label for="exampleInputEmail1" style="display: block;">التصنيفات</label>

                    <input type="text" class="form-control" value="<?php $category_name = $dataBaseActions->filter($categories, $item['categories_id']);
                                                                    echo $category_name;
                                                                    ?>">

                </div>

                <div class="left">
                    <label for="exampleInputEmail1" style="display: block;">الطوابق</label>

                    <input type="text" class="form-control" value=" <?php
                                                                    $floor_name = $dataBaseActions->filter($floors, $item['floor_id']);
                                                                    echo $floor_name;
                                                                    ?>">
                </div>



                <div style="margin-top: 10px">
                    <div class="rigth">
                        <label for="exampleInputEmail1" style="display: block;">الورش والصفوف والخدمات الاخري</label>
                        <input type="text" class="form-control" value="<?php
                                                                        $workshop_name = $dataBaseActions->filter($workShops, $item['workshop_id']);
                                                                        echo $workshop_name;
                                                                        ?>">

                    </div>

                    <div class="left">
                        <label for="exampleInputEmail1" style="display: block;">الاماكن</label>
                        <input type="text" class="form-control" value="<?php $location_name = $dataBaseActions->filter($locations, $item['places_id']);
                                                                        echo $location_name;
                                                                        ?>">
                    </div>

                </div>

                <div style="margin-top: 10px">
                    <div class="rigth">
                        <label for="exampleInputEmail1">مبلغ الشراء</label>
                        <input type="number" class="form-control" value="<?= $item['price_purchase'] ?>">
                    </div>
                    <div class="left">
                        <label for="exampleInputEmail1">قيمة الاهلاك</label>
                        <input type="number" class="form-control" value="<?= $item['depreciation_value'] ?>">
                    </div>

                </div>

                <div class="" style="margin-top: 10px">
                    <label>ملاحظات</label>

                    <input type="text" class="form-control" value="<?php echo str_replace('<br />', ' ', $item['notes'] . "\n"); ?>">

                </div>

            </div>


        </div>

    </div>
</div>
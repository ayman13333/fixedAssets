<?php
require "dbActions.php";

$dataBaseActions = new dbActions($connection);

$id = $_GET['id'];
$table = $_GET['table'];


$item = $dataBaseActions->getOneRowFromSelectedTable($table, $id);
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
    .qr_image{
        margin-top:20px;
        /* heigth:10px; */
        display:block;
        margin-right:30%
    }
</style>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<div dir="rtl">
    <div class="container">
        <div>
            <h5 class="modal-title" style=""> </h5>

        </div>

        <div>


            <?php if ($table !== 'cate_assets') : ?>
                <img src="<?= $item['qr_image'] ?>" class="qr_image">
            <?php endif ?>


            <div class="width">
                <div class="rigth">
                    <label for="exampleInputEmail1">الرقم</label>
                    <input type="text" class="form-control " value="<?= $item['num'] ?>">
                </div>

                <div class="rigth">
                    <label for="exampleInputEmail1">الاسم بالعربية</label>
                    <input type="text" class="form-control" value="<?= $item['name_ar'] ?>">

                </div>
                <div class="left">
                    <label for="exampleInputEmail1">الاسم بالانجليزية</label>
                    <input type="text" class="form-control" value="<?= $item['name_en'] ?>">

                </div>

                <?php if ($table == 'cate_assets') : ?>

                    <div class="left">
                        <label for="exampleInputEmail1">نسبة الاهلاك</label>
                        <input type="text" class="form-control" value="<?= $item['depreciation_percentage'] ?>">

                    </div>

            </div>



        <?php endif ?>

        </div>

    </div>
</div>
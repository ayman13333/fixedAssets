<?php
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

$id = $_GET['id'];

$fixedAsset = $dataBaseActions->getOneFixedAsset($id);
$fixedAsset['cat_name'] = $dataBaseActions->getForeinKeyData($categories, $fixedAsset['categories_id']);
$fixedAsset['floor'] = $dataBaseActions->getForeinKeyData($floors, $fixedAsset['floor_id']);
$fixedAsset['workshop'] = $dataBaseActions->getForeinKeyData($workShops, $fixedAsset['workshop_id']);
$fixedAsset['places'] = $dataBaseActions->getForeinKeyData($locations, $fixedAsset['places_id']);

//edit
if (isset($_POST['edit'])) {

    $result = $dataBaseActions->updateFixedAsset();
    $not_unique_flag = $dataBaseActions->not_unique_flag;

    if ($result) {
        // header('location:editfixedAssetPage.php');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}

?>

<style>
    .rigth {
        width: 45%;
    }

    .left {
        width: 45%;
        margin-right: 10%;
    }

    .myContainer {
        display: flex;
    }
</style>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<!--edit  Modal -->
<div style="width: 100%;background-color: black;height: 25%;">

</div>
<div class="container" style="direction: rtl;">
    <h1 style="margin-top: 5%;">تعديل الاصل</h1>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">

        <input type="hidden" class="form-control" name="id" value="<?= $fixedAsset['id'] ?>">
        <input type="hidden" id="target_file" name="target_file" value="<?= $fixedAsset['photo'] ?>">
        <input type="hidden" id="old_barcode" name="old_barcode">



        <div class="form-group myContainer" style="margin-top: 5%;">
            <div class="rigth">
                <label for="exampleInputEmail1" style="display: block;height: 50px;">صورة الاصل</label>

                <img src="<?= $fixedAsset['photo'] ?>" id="old_photo" style="display:block;height: 140px;">

            </div>

            <div class="left">
                <img src="<?= $fixedAsset['qr_image'] ?>" id="old_barcode_edit" style="">

            </div>


        </div>

        <div class="form-group" style="margin-bottom:20px; width:50%;margin-top:20px">
            <label for="exampleInputEmail1">الصورة الجديدة</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>

        <div class="form-group myContainer">
            <div class="rigth">
                <label for="exampleInputEmail1">الرقم</label>
                <input type="number" class="form-control" id="num" name="num" value="<?= $fixedAsset['num'] ?>">
            </div>
            <div class="left">
                <label for="exampleInputEmail1">الكمية</label>
                <input type="text" class="form-control" id="quantity" name="quantity" value="<?= $fixedAsset['quantity'] ?>">
            </div>
        </div>

        <div class="form-group myContainer">

            <div class="rigth">
                <label for="exampleInputEmail1">اسم الاصل بالعربية</label>
                <input type="text" class="form-control" id="name_ar" name="name_ar" value="<?= $fixedAsset['name_ar'] ?>">

            </div>
            <div class="left">
                <label for="exampleInputEmail1">اسم الاصل بالانجليزية</label>
                <input type="text" class="form-control" id="name_en" name="name_en" value="<?= $fixedAsset['name_en'] ?>">

            </div>

        </div>

        <div class="form-group myContainer" style="margin-top: 10px">
            <div class="rigth">
                <label for="exampleInputEmail1">تاريخ الشراء</label>
                <input type="date" class="form-control" id="order_date" name="order_date" value="<?= $fixedAsset['date_purchase'] ?>">
            </div>

            <div class="left">
                <div class="form-group">
                    <label for="exampleInputEmail1">تاريخ الالغاء</label>
                    <input type="date" class="form-control" id="order_cancel" name="order_cancel" value="<?= $fixedAsset['cancel_date'] ?>">
                </div>
            </div>

        </div>

        <div class="form-group myContainer" style="margin-top: 10px">
            <div class="rigth">
                <label for="exampleInputEmail1" style="display: block;">التصنيفات</label>
                <select name="categories" id="selected_category" style="width: 100%;" value="<?= $fixedAsset['cat_name'] ?>" onchange="changeDeprecation()">
                    <option value="<?= $fixedAsset['categories_id'] ?>"><?= $fixedAsset['cat_name'] ?></option>
                    <?php foreach ($categories as $item) : ?>
                        <option value="<?= $item['id'] ?>"> <?= $item['name_ar'] ?> </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="left">
                <label for="exampleInputEmail1" style="display: block;">الطوابق</label>
                <select name="floor" style="width: 100%;">
                <option value="<?= $fixedAsset['floor_id'] ?>"><?= $fixedAsset['floor'] ?></option>
                    <?php foreach ($floors as $floor) : ?>
                        <option value="<?= $floor['id'] ?>"> <?= $floor['name_ar'] ?> </option>
                    <?php endforeach ?>
                </select>
            </div>

        </div>

        <div class="form-group myContainer" style="margin-top: 10px">
            <div class="rigth">
                <label for="exampleInputEmail1" style="display: block;">الورش والصفوف والخدمات الاخري</label>

                <select name="workshop" style="width: 100%;">
                <option value="<?= $fixedAsset['workshop_id'] ?>"><?= $fixedAsset['workshop'] ?></option>
                    <?php foreach ($workShops as $item) : ?>
                        <option value="<?= $item['id'] ?>"> <?= $item['name_ar'] ?> </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="left">
                <label for="exampleInputEmail1" style="display: block;">الاماكن</label>
                <select name="places" style="width: 100%;">
                <option value="<?= $fixedAsset['places_id'] ?>"><?= $fixedAsset['places'] ?></option>
                    <?php foreach ($locations as $item) : ?>
                        <option value="<?= $item['id'] ?>"> <?= $item['name_ar'] ?> </option>
                    <?php endforeach ?>
                </select>
            </div>

        </div>

        <div class="form-group myContainer" style="margin-top: 10px">
            <div class="rigth">
                <label for="exampleInputEmail1">مبلغ الشراء</label>
                <input type="number" value="<?= $fixedAsset['price_purchase'] ?>" class="form-control" name="order_price" id="order_price" onkeyup="changeDeprecation()">
            </div>
            <div class="left">
                <label for="exampleInputEmail1">قيمة الاهلاك</label>
                <input type="number" value="<?= $fixedAsset['depreciation_value'] ?>" class="form-control" id="depreciation_value_edit" name="depreciation_value" readonly>
            </div>

        </div>

        <div class="form-group" style="margin-top: 10px">
            <label for="exampleInputEmail1">ملاحظات</label>
            <textarea class="form-control" id="notes" name="notes" rows="4" cols="9"><?php echo str_replace('<br />', ' ', $fixedAsset['notes'] . "\n"); ?></textarea>
        </div>


        <button type="submit" name="edit" class="btn btn-warning" style="margin-left: 79%;margin-top:10px">تعديل</button>

    </form>
</div>


<script>
    async function changeDeprecation() {
        //console.log('done');
        var buy_price = document.getElementById('order_price').value;
        var id = document.getElementById('selected_category').value;
        var table = 'cate_assets';
        let depreciation_percentage;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = async function() {
            if (this.readyState == 4 && this.status == 200) {

                let category = JSON.parse(this.response);
                depreciation_percentage = category[0]['depreciation_percentage'];
                // console.log(depreciation_percentage);
                let deprecation_value = buy_price * (depreciation_percentage / 100);
                document.getElementById('depreciation_value_edit').value = deprecation_value;
            }
        };
        xmlhttp.open("GET", "edit.php?id=" + id + "&table=" + table, true);
        xmlhttp.send();

    }
</script>
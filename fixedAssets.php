<?php
//session_start();
include "navbar.php";
require "dbActions.php";
require_once "phpqrcode/qrlib.php";
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

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

$items = $dataBaseActions->getAllData('fixed_assets');

//get total
$total = $dataBaseActions->getTotal($items);

//search
if (isset($_GET['search'])) {

    if ($_GET['search_type'] == 'class_workshop_other') {
        $search_type = 'workshop_id';
    }

    $search_type_dp = $search_type;
    $search_filed_dp = $_GET['search_field'];

    $items = $dataBaseActions->searchFixedAsset($allItems, $search_type_dp, $search_filed_dp);
}

//add
if (isset($_POST['add'])) {

    $dataBaseActions->name_ar = $_POST['name_ar'];
    $dataBaseActions->name_en = $_POST['name_en'];
    $dataBaseActions->order_date = $_POST['order_date'];
    $dataBaseActions->order_price = $_POST['order_price'];
    $dataBaseActions->order_cancel = $_POST['order_cancel'];
    $dataBaseActions->categories = $_POST['categories'];
    $dataBaseActions->places = $_POST['places'];
    $dataBaseActions->floor = $_POST['floor'];
    $dataBaseActions->workshop = $_POST['workshop'];
    $dataBaseActions->num = $_POST['num'];
    $dataBaseActions->quantity = $_POST['quantity'];
    $dataBaseActions->depreciation_value = $_POST['depreciation_value'];

    $result = $dataBaseActions->addDataToFixedAssets($items);
    $not_unique_flag = $dataBaseActions->not_unique_flag;

    if ($result) {
        header('location:fixedAssets.php');
        // echo "<script>history.back()</script>";
    }
}
//edit
if (isset($_POST['edit'])) {

    $result = $dataBaseActions->updateFixedAsset($items);
    $not_unique_flag = $dataBaseActions->not_unique_flag;

    if ($result) {
        header('location:fixedAssets.php');
    }
}

//export
if (isset($_GET['export'])) {
    $delimiter = ",";
    $file_name = "fixed_assets_" . date('Y-m-d') . ".csv";

    $f = fopen('php://memory', 'w');

    $fields = array(
        'الرقم',
        'اسم الاصل بالعربية',
        'اسم الاصل بالانجليزية',
        'تاريخ الشراء',
        'مبلغ الشراء',
        'تاريخ الالغاء',
        'الباركود',
        'ملاحظات',
        'التصنيف',
        'المكان'
    );
    fputcsv($f, $fields, $delimiter);

    foreach ($categories as $item) {
        $lineData = array(
            $item['id'],
            $item['name_ar'],
            $item['name_en'],
            $item['order_date'],
            $item['order_price'],
            $item['order_cancel'],
            $item['bar_code'],
            $item['notes'],
            $item['categories'],
            $item['places']
        );

        fputcsv($f, $lineData, $delimiter);
    }

    fseek($f, 0);

    header('Content-Type:text/csv');
    header('Content-Disposition: attachment;filename="' . $file_name . '";');

    fpassthru($f);
}


//for delete
if (isset($_POST['delete'])) {

    $count = $dataBaseActions->deleteFixedAsset();
    if ($count == 1) {
        header('location:fixedAssets.php');
    }
}

//add place
if (isset($_POST['addPlace'])) {
    $result = $dataBaseActions->addDataToAssets('loca_assets');
    if ($result) {
        header('location:fixedAssets.php');
    }
}

//print_all
if (isset($_POST['print_all'])) {

    if ($_POST['search_type'] == 'all') {

        $a = file_get_contents($dataBaseActions->printPDFUrl() . "fixedAssetsPdfReport.php");
        $mpdf->WriteHTML($a);
        ob_end_clean();
        $mpdf->Output();
    } else {
        $search_type = $_POST['search_type'];
        $search_field = $_POST['search_field'];
        $a = file_get_contents($dataBaseActions->printPDFUrl() . "fixedAssetsPdfReport.php?search_type=$search_type&search_field=$search_field");
        $mpdf->WriteHTML($a);
        ob_end_clean();
        $mpdf->Output();
    }
}

//print one fixed asset
if (isset($_POST['printOneFixedAsset'])) {
    $id = $_POST['id'];

    $a = file_get_contents($dataBaseActions->printPDFUrl() . "oneFixedAssetPdfReport.php?id=$id");
    $mpdf->WriteHTML($a);
    ob_end_clean();
    $mpdf->Output();
}

//exportFromExcel
if (isset($_POST['exportFromExcel'])) {

    $fileName = $_FILES['excel']['name'];

    $fileExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileExtension));

    $newFileName = date("y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;
    $targetDirectory = "uploads/" . $newFileName;

    move_uploaded_file($_FILES['excel']["tmp_name"], $targetDirectory);



    $reader = dbActions::reader($targetDirectory);

    // return ;

    $count_row = 0;
    $category_id = $_POST['categories'];
    $place_id = $_POST['places'];
    $floor_id = $_POST['floor'];
    $workshop_id = $_POST['workshop'];

    foreach ($reader as $key => $row) {
        if ($count_row == 0) {
            $count_row = 1;
        } else {
            if ($row[0]) {
                $num_excel = $row[0];
                $name_ar_excel = $row[1];
                $name_en_excel = $row[2];
                $quantity_excel = $row[3];
                $order_date_excel = $row[4];
                $order_cancel_excel = $row[5];
                $order_price_excel = $row[6];
                $deprecation_value_excel = $row[7];

                $dataBaseActions->name_ar = $name_ar_excel;
                $dataBaseActions->name_en = $name_en_excel;
                $dataBaseActions->order_date = $order_date_excel;
                $dataBaseActions->order_price = $order_price_excel;
                $dataBaseActions->order_cancel = $order_cancel_excel;
                $dataBaseActions->categories = $category_id;
                $dataBaseActions->places = $place_id;
                $dataBaseActions->floor = $floor_id;
                $dataBaseActions->workshop = $workshop_id;
                $dataBaseActions->num = $num_excel;
                $dataBaseActions->quantity = $quantity_excel;
                $dataBaseActions->depreciation_value = $deprecation_value_excel;

                $result = $dataBaseActions->addDataToFixedAssets($items);
            }
            //excel


        }
    }

    // header('location:fixedAssets.php');
    echo "<script>history.back()</script>";
}

//addAssetInModal
if (isset($_POST['addAssetInModal'])) {
    $table = $_POST['table'];
    // $num=$_POST['num'];
    // $name_ar=$_POST['name_ar'];
    // $name_en=$_POST['name_en'];
    $result = $dataBaseActions->addDataToTables($table);
    if ($result) {
        header('location:fixedAssets.php');
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

    .hoverSearch:hover {
        background-color: #ff6900;
        /* width: 80%; */
    }

    .searchResults {
        display: block;
        background: #eaeaea;
        width: 90%;
    }

    @media (min-width: 992px) {
        .myModal {
            width: 140%;
            margin-top: -4%
        }
    }
</style>



<div id="layoutSidenav_content">

    <main>

        <div class="container-fluid px-4" style="width:fit-content !important">
            <h1 class="mt-4"> جدول الاصول</h1>

            <div class="card mb-4">

            </div>
            <div class="card mb-4">
                <div class="card-header">

                    <?php if ($not_unique_flag) : ?>
                        <div class="alert alert-danger" role="alert" style="text-align: center;">
                            qr code is used before
                        </div>

                    <?php endif ?>


                    <div style="display: flex;margin-top: 1%;margin-bottom: 1%;">

                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalLong">
                            اضافة
                        </button>

                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModalLong2" style="margin-right: 2%;">
                            استيراد من اكسل
                        </button>


                    </div>

                    <?php require 'modals/add.php'; ?>
                    <?php require 'modals/searchButton.php'; ?>
                    <?php require 'modals/importFromExcel.php'; ?>


                </div>
                <div class="card-body">

                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>
                                    الاصل بالعربية
                                </th>
                                <th> الاصل بالانجليزية
                                </th>
                                <th>الكمية</th>
                                <th> تاريخ الشراء</th>
                                <th> مبلغ الشراء</th>
                                <th> تاريخ الالغاء</th>
                                <th> صورة الاصل</th>
                                <th> التصنيف</th>
                                <th> المكان</th>
                                <th> الطابق</th>
                                <th> الورشة</th>
                                <th>قيمة الاهلاك</th>
                                <th>
                                    تحكم
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($items) : ?>

                                <?php foreach ($items as $item) : ?>
                                    <tr>
                                        <td>
                                            <form method="GET">
                                                <input class="original_id" type="hidden" name="id" value="<?= $item['id'] ?>">

                                                <a onclick="edit(event)" href="" type="button" data-bs-toggle="modal" data-bs-target="#exampleModalLong1">
                                                    <?= $item['num'] ?>
                                                </a>

                                            </form>

                                        </td>
                                        <td> <?= $item['name_ar'] ?> </td>
                                        <td> <?= $item['name_en'] ?> </td>
                                        <td class="total" style="text-align: center"> <?= $item['quantity'] ?> </td>
                                        <td> <?= $item['date_purchase'] ?> </td>
                                        <td style="text-align: center"> <?= $item['price_purchase'] ?> </td>
                                        <td> <?= $item['cancel_date'] ?> </td>
                                        <td> <img src="<?= $item['photo'] ?>" style="width:100px;height: 60px;">
                                        </td>
                                        <td>
                                            <?php $category_name = $dataBaseActions->filter($categories, $item['categories_id']);
                                            echo $category_name;
                                            ?>
                                        </td>
                                        <td>
                                            <?php $location_name = $dataBaseActions->filter($locations, $item['places_id']);
                                            echo $location_name;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $floor_name = $dataBaseActions->filter($floors, $item['floor_id']);
                                            echo $floor_name;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $workshop_name = $dataBaseActions->filter($workShops, $item['workshop_id']);
                                            echo $workshop_name;
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?= $item['depreciation_value'] ?>
                                        </td>

                                        <td>
                                            <form method="GET">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button onclick="edit(event)" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModalLong1">
                                                    تعديل
                                                </button>

                                            </form>
                                            <!-- edit modal -->


                                        </td>
                                        <td>
                                            <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">

                                                <input class="original_id_delete" type="hidden" name="id" value="">
                                                <button id="delete" name="delete" onclick="confirmDelete(event)" type="submit" class="btn btn-danger">
                                                    حذف </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach ?>


                                <tr id="noData" style="display: none;">
                                    <td colspan="14">
                                        <p style="text-align: center;font-size: revert; background:#f2f2f2;padding: 10px;margin-top: -10px;">لا توجد بيانات</p>
                                    </td>
                                </tr>

                            <?php endif ?>

                        </tbody>
                    </table>
                    <?php require 'modals/edit.php'; ?>

                    <div class="alert alert-primary" role="alert" style="text-align: center;">
                        المجموع= <span id="totalSum"><?= $total ?></span>
                    </div>

                </div>
            </div>
        </div>
    </main>

</div>

<script>
    let original_id = document.getElementsByClassName('original_id');
    let original_id_delete = document.getElementsByClassName('original_id_delete');
    let all_total = 0;

    //let print_url = `?id=`;
    //let print_url_page = '';



    for (var i = 0; i < original_id.length; i++) {
        original_id_delete[i].value = original_id[i].value;

    }


    //console.log(document.getElementsByClassName('total'));
    var total = document.getElementsByClassName('total');
    for (i = 0; i < total.length; i++) {
        //console.log(total[i].innerHTML);
        all_total += parseInt(total[i].innerHTML);
    }
    //console.log(all_total); tableSearch

    function edit(event) {
        let id = event.target.previousElementSibling.value;


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                let category = JSON.parse(this.response);
                var id = category[0]['id'];
                var name_ar = category[0]['name_ar'];
                var name_en = category[0]['name_en'];
                document.getElementById("edit_id").value = id;
                document.getElementById("num").value = category[0]['num'];

                document.getElementById("old_photo").src = category[0]['photo'];

                document.getElementById("target_file").value = category[0]['photo'];

                document.getElementById("name_ar").value = name_ar;
                document.getElementById("name_en").value = name_en;

                document.getElementById("order_date").value = category[0]['date_purchase'];
                document.getElementById("order_price").value = category[0]['price_purchase'];
                document.getElementById("order_cancel").value = category[0]['cancel_date'];

                //document.getElementById("bar_code").value = category[0]['qr_text'];
                document.getElementById("old_barcode_edit").src = category[0]['qr_image'];

                console.log('photos/' + category[0]['qr_image'])

                document.getElementById("quantity").value = category[0]['quantity'];
                document.getElementById("depreciation_value_edit").value = category[0]['depreciation_value'];

                //input hidden id values
                document.getElementsByClassName("hiddenSelectedCategory")[8].value=category[0]['categories_id'];
                document.getElementsByClassName("hiddenSelectedCategory")[9].value=category[0]['floor_id'];
                document.getElementsByClassName("hiddenSelectedCategory")[10].value=category[0]['workshop_id'];
                document.getElementsByClassName("hiddenSelectedCategory")[11].value=category[0]['places_id'];



                let selectedCategory = category[0]['categories_id'];
                let selectedPlace = category[0]['places_id'];
                let selectedFloor = category[0]['floor_id'];
                let selectedWorkShop = category[0]['workshop_id'];

                getForeinKeyData(selectedCategory, 'cate_assets', "selected_category");
                getForeinKeyData(selectedPlace, 'loca_assets', "selected_place");
                getForeinKeyData(selectedFloor, 'floors', 'selected_floor');
                getForeinKeyData(selectedWorkShop, 'class_workshop_other', 'selected_workshop')

                let notes = category[0]['notes'];
                document.getElementById("notes").innerHTML = notes;
                //bar code link downloadBarCodeLink
                document.getElementById("downloadBarCodeLink").href = category[0]['qr_image'];


                // print_url_page = '';
                // print_url_page = print_url + id;

                //print button in modal

                document.getElementById("printOneFixedAsset").value = id;



            }
        };
        xmlhttp.open("GET", "editFixedAsset.php?id=" + id, true);
        xmlhttp.send();

    }

    async function getForeinKeyData(id, table, element) {


        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                let data = JSON.parse(this.response);

                var id = data[0]['id'];
                var name_ar = data[0]['name_ar'];
                // document.getElementById(element).value = id;
                document.getElementById(element).value = name_ar;


                // document.getElementById("edit_id").value = id;
                // document.getElementById("name_ar").value = name_ar;
                // document.getElementById("name_en").value = name_en;
                // document.getElementById("number").value = num;

            }
        };
        xmlhttp.open("GET", "edit.php?id=" + id + "&table=" + table, true);
        xmlhttp.send();
    }

     function changeDeprecation(tableFlag=null) {
        console.log(tableFlag);
        var buy_price = document.getElementById('order_price').value;
        var id = document.getElementsByClassName('hiddenSelectedCategory')[tableFlag].value;

        console.log(id);
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

    // function generateBarCode(value, id) {
    //     // console.log(value);
    //     if (value == '') {
    //         //console.log('done');
    //         JsBarcode('#' + id, null, {
    //             format: 'code128',
    //             height: 45,
    //             displayValue: false,
    //             margin: 0,
    //             marginTop: 0,
    //             textAlign: "right"
    //         });
    //     } else {
    //         JsBarcode('#' + id, value, {
    //             format: 'code128',
    //             height: 45,
    //             displayValue: false,
    //             margin: 0,
    //             marginTop: 0,
    //             textAlign: "right"
    //         });
    //     }

    // }

    // function generateBarCodeForEdit(value) {
    //     //console.log(value);
    //     JsBarcode('#editBarCode', value, {
    //         format: 'code128',
    //         displayValue: true,
    //         height: 45,
    //         displayValue: false,
    //         margin: 0,
    //         marginTop: 0,
    //         textAlign: "right"
    //     });
    // }

    function tableSearch() {
        let input, filter, table, tr, td, txtValue;
        let totalSum = 0;
        input = document.getElementById('myInput');
        filter = input.value;
        table = document.getElementById('myTable');
        tr = document.getElementsByTagName('tr');
        let searchField = document.getElementById('searchSelect').value;
        document.getElementById("noData").style.display = 'none'
        let flag = 1;

        for (let i = 0; i < tr.length - 1; i++) {
            //all
            if (searchField == '') {
                td = tr[i].getElementsByTagName('td');
                // totalSum += parseInt(td[3].innerHTML) ;
                // flag=0;
                if (td) {
                    txtValue = td.innerText;
                    // console.log(document.getElementsByClassName('total'));

                    //totalSum += parseInt(td[3]) ;
                    tr[i].style.display = "";
                    // totalSum += parseInt(tr[i].getElementsByTagName('td')[3].innerHTML) ;
                }
            } else {
                td = tr[i].getElementsByTagName('td')[searchField];

                //  flag=0;

                //in all button display all tr
                if (td) {
                    txtValue = td.innerText;
                    // console.log(filter);
                    if (txtValue == filter) {
                        //console.log(txtValue);
                        tr[i].style.display = "";
                        totalSum += parseInt(tr[i].getElementsByTagName('td')[3].innerHTML);
                        //console.log(totalSum);
                        // flag=0;
                    } else {
                        tr[i].style.display = "none";
                        // totalSum += parseInt(tr[i].getElementsByTagName('td')[3].innerHTML) ;
                        //flag=1;
                    }
                }
            }

        }
        // document.getElementById('myInput').value = '';
        document.getElementById('totalSum').innerHTML = totalSum;
        // console.log(totalSum);
        // var tr_ondata=document.getElementsByTagName('tr');

        //console.log(tr_ondata);
        let stop = 1;
        let count = 0;
        //console.log(tr_ondata.length-1)

        for (let j = 0; j < tr.length; j++) {

            // console.log(tr_ondata[j].style.display=="none")
            if (tr[j].style.display) {
                ++count;
            }

            if (tr[j].style.display == "none") {
                flag = 0;
                stop = 0;

            }


            if (j == tr.length - 1 && count == j && count > 0) {

                flag = 1;
            }

            if (searchField == '') {
                document.getElementById('totalSum').innerHTML = all_total;

            }


        }

        if (flag == 0) {
            document.getElementById("noData").style.display = 'none';
        } else if (flag == 1) {
            document.getElementById("noData").style.display = 'contents';
        }

    }

    //search by typing functions



    function searchAssetFilter(tableFilter, tableFlag) {

        let flag;
        removeIdForSelect(tableFlag);
        // if(tableFlag==0) flag=searchAssetFilterFlag;
        // if(tableFlag==1) flag=searchFloorFilterFlag;


        console.log(tableFilter)
        //var table = 'cate_assets';

        if (1) {

            // results = [];
            // if(tableFlag==0) searchAssetFilterFlag = 1;
            // if(tableFlag==1) searchFloorFilterFlag =1;

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {

                    let items = JSON.parse(this.response);
                    console.log(JSON.parse(this.response));
                    searchInResponse(tableFlag, items);


                }
            };
            xmlhttp.open("GET", "getAll.php?table=" + tableFilter, true);
            xmlhttp.send();

        } else {
            searchInResponse(tableFlag);
        }

    }

    function addDataToTable() {

        var selectedSelect;
        var table = document.getElementById('selected_table').value;
        if (table == 'cate_assets') {
            selectedSelect = 0;
        } else if (table == 'floors') {
            selectedSelect = 1;
        } else if (table == 'class_workshop_other') {
            selectedSelect = 2;
        } else if (table == 'loca_assets') {
            selectedSelect = 3;
        }

        var num = document.getElementById('num_selected_table').value;
        var name_ar = document.getElementById('name_ar_selected_table').value;
        var name_en = document.getElementById('name_en_selected_table').value;

        var data = new FormData();
        data.append('table', table);
        data.append('num', num);
        data.append('name_ar', name_ar);
        data.append('name_en', name_en);


        var xmlhttp = new XMLHttpRequest();

        xmlhttp.open("POST", "addDataToTable.php", true);
        xmlhttp.onload = function() {
            // do something to response
            //  let item = JSON.parse(this.response);
            //  var id = item[0]['id'];
            //  var name_ar = item[0]['name_ar'];

            //  var x = document.getElementsByClassName("validate")[selectedSelect];
            //  var option = document.createElement("option");
            //  option.text = name_ar;
            //  option.value = id;
            //  x.add(option);

            document.getElementById('selected_category_add').style.display = 'none';
            document.getElementById('num_selected_table').value = '';
            document.getElementById('name_ar_selected_table').value = '';
            document.getElementById('name_en_selected_table').value = '';

            //set all flags to zero
            searchAssetFilterFlag = 0;


        };

        xmlhttp.send(data);
    }



    function searchInResponse(tableFlag, items = null) {
        var results = [];

        // var searchResults = document.getElementById('searchResults').innerHTML = '';
        // var filter = document.getElementById('searchFilter').value;
        console.log(tableFlag);

        var searchResults = document.getElementsByClassName('searchResults')[tableFlag].innerHTML = '';
        var filter = document.getElementsByClassName('searchFilter')[tableFlag].value;
        // var filter = document.getElementsByClassName('searchFilter')[0];
        console.log(filter);

        if (filter) {

            if (items) {
                for (var i = 0; i < items[0].length; i++) {
                    results.push({
                        'id': items[0][i]['id'],
                        'name_ar': items[0][i]['name_ar']
                    })
                }
            }
            console.log(results);

            var expectedResult = results.filter((el) => el['name_ar'].includes(filter));



            for (var i = 0; i < expectedResult.length; i++) {

                var searchResults = document.getElementsByClassName('searchResults')[tableFlag].innerHTML += `<option onclick="setIdForSelect(${tableFlag})"  value=${expectedResult[i]['id']}  class="hoverSearch">${expectedResult[i]['name_ar']}  </option>`;

            }

            if (expectedResult.length == 0) {

                document.getElementsByClassName('searchResults')[tableFlag].innerHTML = 'لا توجد نتائج';

            }

            

            console.log(expectedResult);
        }
    }

    function setIdForSelect(tableFlag) {
        var id = event.target.value;
        //  if(tableFlag==0)  document.getElementById('hiddenSelectedCategory').value = id;
        //  if(tableFlag==1)  document.getElementById('hiddenSelectedfloor').value = id;
        document.getElementsByClassName('hiddenSelectedCategory')[tableFlag].value = id;

        document.getElementsByClassName('searchFilter')[tableFlag].value = event.target.innerHTML;

        if(tableFlag==8)
            {
                changeDeprecation(tableFlag);
            }

    }

    function removeIdForSelect(tableFlag) {

        document.getElementsByClassName('hiddenSelectedCategory')[tableFlag].value = '';
        // document.getElementById('hiddenSelectedfloor').value='';

    }
</script>

<?php include_once 'footer.php' ?>
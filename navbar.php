<?php 
//header("Content-Type: text/plain");
ob_start();
require "config.php";
 
?>
<!DOCTYPE html>
<!-- <html lang="ar"> -->

<head>
    <meta charset="utf-8" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" /> -->
    <!-- <meta name="author" content="" /> -->
    <title> Fixed Assets</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" /> -->
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->

    <script src="barcode.js" type="text/javascript"></script>

</head>

<!-- class="col-xl-4 col-lg-4 col-md-6 col-sm-12" -->

<body class="sb-nav-fixed" dir="rtl">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="">لوحة التحكم</a>

        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" onclick="toggleNav()"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">

        </form>
        <!-- Navbar-->
        <button onclick="topFunction()" style="background: #0d6efd;margin: 1%;">
            <i class="fa-solid fa-up-long" style="font-size: large;color:aliceblue"></i>
        </button>

        <button onclick="downFunction()" style="background: #0d6efd;margin: 0.5%;">
            <i class="fa-solid fa-down-long" style="font-size: large;color:aliceblue;"></i>
        </button>



        </li>
        </ul>
    </nav>
    <div id="layoutSidenav">



        <div id="layoutSidenav_nav">

            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <a class="nav-link" href="index.php">

                            جدول التصنيفات
                        </a>

                        <a class="nav-link" href="all.php?table=loca_assets">

                            جدول اماكن الاصل
                        </a>

                        <a class="nav-link" href="all.php?table=floors">

                            جدول الادوار
                        </a>

                        <a class="nav-link" href="all.php?table=class_workshop_other">

                            جدول ادارة الورش والصفوف والخدمات الاخري
                        </a>

                        <a class="nav-link" href="fixedAssets.php">

                            جدول الاصول الثابتة
                        </a>

                        <a class="nav-link" href="reports.php">

                            تقارير الورش
                        </a>

                        <a class="nav-link" href="reportsFloors.php">

                            تقارير الادوار
                        </a>

                        <a class="nav-link" href="reportsBuilding.php">

                            تقارير المباني
                        </a>




                    </div>


                </div>

                <div class="sb-sidenav-footer">

            </nav>


        </div>



        <script>
            let flag = 0;

            function toggleNav() {
                if (flag == 0) {
                    document.getElementById("layoutSidenav_nav").style.width = "0";
                    document.getElementById("layoutSidenav_content").style.paddingRight = "0";
                    flag = 1;
                } else {
                    document.getElementById("layoutSidenav_nav").style.width = "225px";
                    document.getElementById("layoutSidenav_content").style.paddingRight = "225px";
                    flag = 0;
                }

            }

            function topFunction() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }

            function downFunction() {
                window.scrollTo(0, document.body.scrollHeight);
            }

          
        </script>
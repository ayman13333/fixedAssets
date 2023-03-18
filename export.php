<?php
require "config.php";

$stml = $connection->prepare("SELECT * FROM `fixed_assets`");
$stml->execute();

$categories = $stml->fetchAll();

   $delimiter=",";
   $file_name="fixed_assets_".date('Y-m-d'). ".csv";

   $f=fopen('php://memory','w');

   $fields=array('الرقم',
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
    fputcsv($f,$fields,$delimiter);

    //fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));

    foreach($categories as $item)
    {
      $lineData=array(
        $item['id'],
        $item['name_ar'],
        $item['name_en'],
        $item['order_date'],
        $item['order_price'],
        $item['order_cancel'],
        $item['bar_code'],
        str_replace('<br />', ' ', $item['notes'] . "\n"),
        $item['categories'],
        $item['places']
      );

      fputcsv($f,$lineData,$delimiter);
    }

    fseek($f,0);

    // fpassthru($f);
    header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream'); 
//header('Content-Disposition: attachment; filename=file.csv');
header('Content-Disposition: attachment;filename="' . $file_name . '";');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo "\xEF\xBB\xBF"; // UTF-8 BOM
fpassthru($f);

    exit();

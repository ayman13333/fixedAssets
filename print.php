<?php
require_once('TCPDF-main/tcpdf.php');



// extend TCPF with custom functions
class MYPDF extends TCPDF
{
    

    // Load table data from file
    public function LoadData($all)
    {
        require "config.php";
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

        $items = $dataBaseActions->getAllData('fixed_assets');

        
        $new  = array();
        $array_loop = 0;
        
        foreach($items as $item)
        {
           $new[$array_loop]['num']=$item['num'];
           $new[$array_loop]['name_ar']=$item['name_ar'];
           $new[$array_loop]['name_en']=$item['name_en'];
           $new[$array_loop]['quantity']=$item['quantity'];
           $new[$array_loop]['date_purchase']=$item['date_purchase'];
           $new[$array_loop]['price_purchase']=$item['price_purchase'];
          // $new[$array_loop]['qr_text']=$item['qr_text'];
           //$new[$array_loop]['bar_code']=$item['bar_code'];
           $new[$array_loop]['depreciation_value']=$item['depreciation_value'];


           $new[$array_loop]['cat_name']=$dataBaseActions->getForeinKeyData($categories,$item['categories_id']);
           $new[$array_loop]['place_name']=$dataBaseActions->getForeinKeyData($locations,$item['places_id']);
           $new[$array_loop]['floor_name']=$dataBaseActions->getForeinKeyData($floors,$item['floor_id']);
           $new[$array_loop]['workshop_name']=$dataBaseActions->getForeinKeyData($workShops,$item['workshop_id']);
           $array_loop++;
        }

       // $new['المجموع']=0;

        return $new;
    }

    // Colored table
    public function ColoredTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'C');
        // Header
        $w = array(10,15,10,15,25,15,15,15,20);
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++$i) {
            //$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['num'], 'LR', 0, 'R', $fill);
            $this->Cell($w[1], 6, $row['name_ar'], 'LR', 0, 'R', $fill);
            // $this->Cell($w[2], 6, $row['name_en'], 'LR', 0, 'R', $fill);
            $this->Cell($w[2], 6, $row['quantity'], 'LR', 0, 'R', $fill);
            //$this->Cell($w[3], 6, $row['date_purchase'], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $row['price_purchase'], 'LR', 0, 'R', $fill);
           // $this->Cell($w[4], 6, $row['qr_text'], 'LR', 0, 'R', $fill);
           // $this->Cell($w[6], 6, $row['bar_code'], 'LR', 0, 'R', $fill);
            $this->Cell($w[4], 6, $row['cat_name'], 'LR', 0, 'R', $fill);
            $this->Cell($w[5], 6, $row['place_name'], 'LR', 0, 'R', $fill);
            $this->Cell($w[6], 6, $row['floor_name'], 'LR', 0, 'R', $fill);
            $this->Cell($w[7], 6, $row['workshop_name'], 'LR', 0, 'R', $fill);
            $this->Cell($w[8], 6, $row['depreciation_value'], 'LR', 0, 'R', $fill);
            

            //depreciation_value
            // $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
        
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// set some language dependent data:
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';
$pdf->setLanguageArray($lg);

// set document information
$pdf->SetCreator(PDF_CREATOR);
 //$pdf->SetAuthor('المجموع');
$pdf->SetTitle('تقرير الاصول');
// $pdf->SetSubject('TCPDF Tutorial');
 //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$total=0;
 //get total
// $total = $dataBaseActions->getTotal($items);
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,"sum=$total");

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins('10', PDF_MARGIN_TOP, '0');
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
   // $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('aealarabiya', '', 12);

// add a page
$pdf->AddPage();

// column titles
$header = array('الرقم', 'الاسم','الكمية','المبلغ','التصنيف','المكان','الطابق','الورشة','قيمة الاهلاك');

// data loading

$type=$_GET['type'];
$data = $pdf->LoadData($type);

// print colored table
$pdf->ColoredTable($header, $data);
//$pdf->ColoredTable('المجموع', 0);

// ---------------------------------------------------------
//$pdf->setRTL(true);

// close and output PDF document
$pdf->Output('fixedAssetsReport.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
<?php
require "config.php";
//require  "vendor/autoload.php";
//use Zxing\QrReader as QrReader;
require "excelReader/excel_reader2.php";
require "excelReader/SpreadsheetReader.php";


class dbActions
{
   public $name_ar;
   public $name_en;
   public $order_date;
   public $order_price;
   public $order_cancel;
    // $qr_text = $_POST['bar_code'];
   public $categories;
   public $places;
   public $floor;
   public $workshop;
   public $num;
   public $quantity;
   public $depreciation_value;
   public $notes;
   
   private static $reader=NULL;

    public $connection;
    public $not_unique_flag = 0;


    function __construct($connection)
    {
        $this->connection = $connection;
    }

    public static function reader($targetDirectory)
    {
        if(self::$reader==NULL)
        {
           self::$reader= new SpreadsheetReader($targetDirectory);
        }

        return self::$reader;
    }

    public function getTotal($items)
    {
        // $stml = $this->connection->prepare("SELECT * FROM `fixed_assets`");
        // $stml->execute();
        // $items = $stml->fetchAll();
        $total = 0;

        foreach ($items as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }

    public function getAllData($table)
    {
        $stml = $this->connection->prepare("SELECT * FROM `$table`");
        $stml->execute();
        $items = $stml->fetchAll();

        return $items;
    }

    public function getOneRowFromSelectedTable($table,$id)
    {
        $stml = $this->connection->prepare("SELECT * FROM `$table` WHERE id=? ");
        $stml->execute(array($id));
        $row = $stml->fetch();

        return $row;
    }

    public function addDataToFixedAssets()
    {
        $name_ar = $this->name_ar;
        $name_en = $this->name_en;
        $order_date = $this->order_date;
        $order_price = $this->order_price;
        $order_cancel = $this->order_cancel;
        // $qr_text = $_POST['bar_code'];
        $categories = $this->categories;
        $places = $this->places;
        $floor = $this->floor;
        $workshop = $this->workshop;
        $num = $this->num;
        $quantity = $this->quantity;
        $depreciation_value = $this->depreciation_value;

        //calculate duprication value
        $depreciation_value = $order_price * $depreciation_value / 100;

        //$_POST['notes']
        $notes = nl2br(htmlentities($this->notes, ENT_QUOTES, 'UTF-8'));

         $target_dir = "photos/";
        // $target_file="123";

        if(isset($_FILES["photo"]['name']))
        {
             $target_file = $target_dir . time() . basename($_FILES["photo"]['name']);
             move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        }
        else
        {
            $target_file="123";
        }

        

        //qrcode
        $qrcode = $target_dir . time() . ".png";
        $qr_image = $target_dir . time() . ".png";
        $qr_text = "editFixedAsset.php?id=";

        $stml = $this->connection->prepare('INSERT INTO  `fixed_assets` (name_ar,name_en,date_purchase,price_purchase,cancel_date,qr_text,categories_id,places_id,notes,photo,floor_id,workshop_id,num,quantity,depreciation_value,qr_image) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ');
        $result = $stml->execute(array($name_ar, $name_en, $order_date, $order_price, $order_cancel, $qr_text, $categories, $places, $notes, $target_file, $floor, $workshop, $num, $quantity, $depreciation_value, $qr_image));
        $last_id = $this->connection->lastInsertId();
        //var_dump($last_id);

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $url = "https://";
        else
            $url = "http://";
        // http://fixedassets.aba.vg/fixed_assets   
        $url .= "fixedassets.aba.vg/fixed_assets";

        $url .= "/editFixedAssetPage.php?id=$last_id";
        //var_dump($url );

        QRcode::png($url, $qrcode, 'H', 4, 4);

        return $result;
    }

    public function deleteFixedAsset()
    {
        $id = $_POST['id'];
        //first delete photo
        $stml = $this->connection->prepare("SELECT * FROM `fixed_assets` WHERE `id`=?");
        $stml->execute(array($id));
        $item = $stml->fetch();
        // print_r($category);
        unlink($item['photo']);
        $stml = $this->connection->prepare("DELETE FROM `fixed_assets` WHERE `id`=?");
        $stml->execute(array($id));
        $count = $stml->rowCount();

        return $count;
    }

    public function searchFixedAsset($search_type_dp, $search_filed_dp)
    {
        //class_workshop_other
        if ($search_type_dp == 'all') {
            //$items = $allItems;
        } else {
            $stml = $this->connection->prepare("SELECT * FROM `fixed_assets` WHERE $search_type_dp =? ");
            $stml->execute(array($search_filed_dp));
            $items = $stml->fetchAll(PDO::FETCH_ASSOC);
        }

        return $items;
    }

    public function searchUniqueFixedAsset($search_type_dp, $search_filed_dp, $distinctColum)
    {

        if ($search_type_dp == 'all') {
            //$items = $allItems;
        } else {
            $stml = $this->connection->prepare("SELECT DISTINCT $distinctColum  FROM `fixed_assets` WHERE $search_type_dp =? ");
            $stml->execute(array($search_filed_dp));
            $items = $stml->fetchAll(PDO::FETCH_ASSOC);
        }

        return $items;
    }

    public function getOneFixedAsset($id)
    {
        $stml = $this->connection->prepare("SELECT *  FROM `fixed_assets` WHERE id=?");
        $stml->execute(array($id));
        $fixedAsset = $stml->fetch();

        return $fixedAsset;
    }

    public function selectFromFixedAssetsWithMultipleConditions($field1_id,$field2_id,$field1,$field2)
    {
        $stml = $this->connection->prepare("SELECT *  FROM `fixed_assets` WHERE $field1=?  AND $field2=?");
        $stml->execute(array($field1_id,$field2_id));
        $fixedAssets = $stml->fetchAll();

        return $fixedAssets;
    }

    public function updateFixedAsset($allItems = null)
    {
        $id = $_POST['id'];
        $name_ar = $_POST['name_ar'];
        $name_en = $_POST['name_en'];
        $order_date = $_POST['order_date'];
        $order_price = $_POST['order_price'];
        $order_cancel = $_POST['order_cancel'];
        $bar_code = $_POST['bar_code'];
        $old_barcode = $_POST['old_barcode'];
        $categories = $_POST['categories'];
        $quantity = $_POST['quantity'];
        $places = $_POST['places'];
        $floor = $_POST['floor'];
        $workshop = $_POST['workshop'];
        $num = $_POST['num'];
        $depreciation_value = $_POST['depreciation_value'];

        $notes = nl2br(htmlentities($_POST['notes'], ENT_QUOTES, 'UTF-8'));

        //bar code is unique


        //delete first photo
        $stml = $this->connection->prepare("SELECT * FROM `fixed_assets` WHERE `id`=?");
        $stml->execute(array($id));
        $item = $stml->fetch();


        if (isset($_FILES["photo"]['name']) && $_FILES["photo"]['name'] != null) {
            //echo'upload';
          //  unlink($item['photo']);
            $target_dir = "photos/";
            $target_file = $target_dir . time() . basename($_FILES["photo"]['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        } else {

            $target_file = $_POST['target_file'];
        }

      
        $stml = $this->connection->prepare('UPDATE  `fixed_assets` SET  `name_ar` =?, `name_en` =?,`date_purchase`=?,`price_purchase`=?,`cancel_date`=?,`categories_id`=?,`places_id`=?,`notes`=?,`photo`=? ,`floor_id`=?, `workshop_id`=? , `num`=? ,`quantity`=? , `depreciation_value`=? WHERE `id`=? ');
        $result = $stml->execute(array($name_ar, $name_en, $order_date, $order_price, $order_cancel, $categories, $places, $notes, $target_file, $floor, $workshop, $num, $quantity, $depreciation_value, $id));

        return $result;
    }

    public function filter($collection, $id)
    {
        foreach ($collection as $category) {
            if ($category['id'] == $id) {
                return $category['name_ar'];
            }
        }
    }

    public function getForeinKeyData($categories, $id)
    {
        foreach ($categories as $category) {
            if ($category['id'] == $id) {
                return $category['name_ar'];
            }
        }
    }

    public function addDataToAssets($table)
    {
        $name_ar = $_POST['name_ar'];
        $name_en = $_POST['name_en'];
        $num = $_POST['num'];
        $stml = $this->connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num) VALUES(?,?,?) ");
        $result = $stml->execute(array($name_ar, $name_en, $num));

        return $result;
    }

    public function addDataToTables($table)
    {
        $num=$_POST['num'];
        $name_ar=$_POST['name_ar'];
        $name_en=$_POST['name_en'];
        
        $target_dir = "photos/";
        $qrcode = $target_dir . time() . ".png";
        $qr_image = $target_dir . time() . ".png";
        $qr_text = qr_url();
    
    
        $stml = $this->connection->prepare("INSERT INTO  `$table` (name_ar,name_en,num,qr_image) VALUES(?,?,?,?) ");
        $result = $stml->execute(array($name_ar, $name_en, $num, $qr_image));
    
        $last_id = $this->connection->lastInsertId();
        $url =  $qr_text . $last_id;
        //var_dump($url );
    
        QRcode::png($url, $qrcode, 'H', 4, 4);
    }

    public function printPDFUrl()
    {
        if($_SERVER['HTTP_HOST']=='localhost')
        {
            return 'http://localhost/fixed_assets/';
        }
        else
        {
            return 'http://fixedassets.aba.vg/fixed_assets/';
        }
    }
}

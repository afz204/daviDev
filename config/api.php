<?php

require_once('db.php');

class Login
{

    private $conn;

    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }
    //execute your login
    public function runLogin($username,$password)
    {
        try
        {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE Email=:uname");
            $stmt->execute(array(':uname'=>$username));
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1)
            {
                if(password_verify($password, $userRow['password']))
                {
                    if($userRow['status'] == '1'){
                        $_SESSION['user_session'] = $userRow['id'];
                        
                        return true;
                    }else{

                        $_SESSION['error'] = "Sorry, Your Acount are ready Disable!";
                        return false;

                    }
                }
                else
                {
                    $_SESSION['error'] = "Trying to find somewhere";
                    return false;
                }
            }else{
                $_SESSION['error'] = "Still finding in Goooooogle!";
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
    //session data
    public function is_loggedin()
    {
        if(isset($_SESSION['user_session']))
        {
            return true;
        }
    }
    //link for session is true
    public function redirect($url)
    {
        header("Location: $url");
    }
    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
    //link logout
    public function doLogout()
    {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }


}

class Admin
{

    private $conn;

    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }
    // execute every query with pdo statement
    // $query = "YOUR QUERY";
    // $variable = $stmt->execute() or $Varname = $stmt->bindParam()
    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }

    public function adminID()
    {
        $id = $_SESSION['user_session'];
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = :user_id");
        $stmt->execute(array(':user_id' => $id));
        $stmt = $stmt->fetch(PDO::FETCH_LAZY);
        $stmt = $stmt['id'];
        return $stmt;

    }

    public function sessionFlorist()
    {
        $id = $_SESSION['user_session'];
        $stmt = $this->conn->prepare("SELECT * FROM florist WHERE ID = :user_id");
        $stmt->execute(array(':user_id' => $id));
        $stmt = $stmt->fetch(PDO::FETCH_LAZY);
        return $stmt;

    }

    public function getAdmin($id)
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, jabatan, role_id, status, created_at FROM users WHERE id = :user_id");
        $stmt->execute(array(':user_id' => $id));
        return $stmt;

    }
    public function getData($field, $table, $clause)
    {
        $stmt = $this->conn->prepare("SELECT ". $field ." FROM ". $table ." WHERE ". $clause ." ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_LAZY);
        return $row;

    }
    public function getDataTable($field, $table, $clause)
    {
        $stmt = $this->conn->prepare("SELECT ". $field ." FROM ". $table ." WHERE ". $clause ." ");
        $stmt->execute();
        return $stmt;

    }
    public function getKurir($id)
    {
        $stmt = $this->conn->prepare("SELECT id, nama_kurir, email, phone, wa, alamat, kel, kec, kota, province, status, created_at FROM kurirs WHERE id = :user_id");
        $stmt->execute(array(':user_id' => $id));
        return $stmt;

    }

    public  function CountTables($field, $table)
    {
        $stmt = $this->conn->prepare('SELECT COUNT('. $field .') FROM '. $table);
        $stmt->execute();
        $stmt = $stmt->fetch();
        $stmt = $stmt[0];
        return $stmt;
    }

    public function Products($field, $table)
    {
        $stmt = $this->conn->prepare('SELECT '. $field .' FROM '. $table);
        $stmt->execute();
        return $stmt;
    }
    public function FindProducts($field, $table, $clause)
    {
        $stmt = $this->conn->prepare('SELECT '. $field .' FROM '. $table .' WHERE '. $clause);
        $stmt->execute();
        return $stmt;
    }
    public function ProductsJoin($field, $table, $join, $clause)
    {
        $stmt = $this->conn->prepare('SELECT '. $field .' FROM '. $table.' '.$join.' '.$clause);
        $stmt->execute();
        return $stmt;
    }
    public function weightPages($val)
    {
        switch ($val){
            case '2':
                $weight = 'c';
                $create = 'style=""""';
                $read = 'style="display:none;"'; 
                $update = 'style="display: none;"';
                $delete = 'style="display: none;"';
                break;
            case '3':
                $weight = 'r';
                $create = 'style="display: none;"';
                $read = 'style=""""'; 
                $update = 'style="display: none;"';
                $delete = 'style="display: none;"';
                break;
            case '4':
                $weight = 'u';
                $create = 'style="display: none;"';
                $read = 'style="display:none;"';
                $update = 'style=""""';
                $delete = 'style="display: none;"';
                break;
            case '5':
                $weight = 'cr';
                $create = 'style=""""';
                $read = 'style=""""';
                $update = 'style="display: none;"';
                $delete = 'style="display: none;"';
                break;
            case '6':
                $weight = 'cu';
                $create = 'style=""""';
                $read = 'style="display:none;"';
                $update = 'style=""""';
                $delete = 'style="display: none;"';
                break;
            case '7':
                $weight = 'ru';
                $create = 'style="display: none;"';
                $read = 'style="display:block;"';
                $update = 'style=""""';
                $delete = 'style="display: none;"';
                break;
            case '8':
                $weight = 'd';
                $create = 'style="display: none;"';
                $read = 'style="display:none;"';
                $update = 'style="display: none;"';
                $delete = 'style=""""';
                break;
            case '9':
                $weight = 'cru';
                $create = 'style=""""';
                $read = 'style=""""';
                $update = 'style=""""';
                $delete = 'style="display: none;"';
                break;
            case '10':
                $weight = 'cd';
                $create = 'style=""""';
                $read = 'style="display:none;"';
                $update = 'style="display: none;"';
                $delete = 'style=""""';
                break;
            case '11':
                $weight = 'rd';
                $create = 'style="display: none;"';
                $read = 'style=""""';
                $update = 'style="display: none;"';
                $delete = 'style=""""';
                break;
            case '12':
                $weight = 'ud';
                $create = 'style="display: none;"';
                $read = 'style="display:none;"';
                $update = 'style=""""';
                $delete = 'style=""""';
                break;
            case '13':
                $weight = 'crd';
                $create = 'style=""""';
                $read = 'style="display:block;"';
                $update = 'style="display: none;"';
                $delete = 'style=""""';
                break;
            case '14':
                $weight = 'cud';
                $create = 'style=""""';
                $read = 'style="display:none;"';
                $update = 'style=""""';
                $delete = 'style=""""';
                break;
            case '15':
                $weight = 'rud';
                $create = 'style="display: none;"';
                $read = 'style=""""';
                $update = 'style=""""';
                $delete = 'style=""""';
                break;
            case '17':
                $weight = 'crud';
                $create = 'style=""""';
                $read   = 'style=""""';
                $update = 'style=""""';
                $delete = 'style=""""';
                break;

            default:
                $weight = '';
                $create = '';
                $read = '';
                $update = '';
                $delete = '';
                break;
        }

        $previllages = array(
                'weight'    => $weight,
                'create'    => $create,
                'read'     => $read,
                'update'    => $update,
                'delete'    => $delete
        );
        return $previllages;
    }
    public function Province()
    {
        $stmt = $this->conn->prepare('SELECT id, name FROM provinces');
        $stmt->execute();
        return $stmt;
    }
    public function Category()
    {
        $stmt = $this->conn->prepare('SELECT id, name FROM categories WHERE parent_id = 0 ');
        $stmt->execute();
        return $stmt;
    }
    public function paging($query,$records_per_page)

    {

        $starting_position=0;
        if(isset($_GET["page_no"]))
        {
            $starting_position=($_GET["page_no"]-1)*$records_per_page;
        }
        $query2=$query." limit $starting_position,$records_per_page";
        return $query2;
    }

    public function saveLogs($reff, $user, $method, $ket){
        switch($method){
            case 'c':
                $types = 'create';
            break;
            case 'u':
                $types = 'update';
            break;
            case 'd':
                $types = 'delete';
            break;
            case 'f':
                $types = 'function';
            break;
            default:
                $types = 'none';
            break;
        }

        $stmt = $this->conn->prepare('INSERT INTO logs_users (reff_id, user_id, methode, ket) 
        VALUES (:a, :b, :c, :d)');
        $stmt->execute(array(
            ':a'    => $reff,
            ':b'    => $user,
            ':c'    => $types,
            ':d'    => $ket
        ));
        return $stmt;

    }

    public function paginglink($total, $records_per_page)

    {

        //$self = $_SERVER['PHP_SELF'];
        //$self = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $self = "$_SERVER[REQUEST_URI]";
        $self = explode('&', $self);
        $self = $self['0'];
        $total_no_of_records = $total;

        if($total_no_of_records > 0)
        {
            ?><ul class="pagination"><?php
            $total_no_of_pages=ceil($total_no_of_records/$records_per_page);
            $current_page=1;
            if(isset($_GET["page_no"]))
            {
                $current_page=$_GET["page_no"];
            }
            if($current_page!=1)
            {
                $previous =$current_page-1;
                echo "<li><a href='".$self."&page_no=1'>First</a></li>";
                echo "<li><a href='".$self."&page_no=".$previous."'>Previous</a></li>";
            }
            for($i=1;$i<=$total_no_of_pages;$i++)
            {
                if($i==$current_page)
                {
                    echo "<li class='paginate_button active'><a href='".$self."&page_no=".$i."';'>".$i."</a></li>";
                }
                else
                {
                    echo "<li><a href='".$self."&page_no=".$i."'>".$i."</a></li>";
                }
            }
            if($current_page!=$total_no_of_pages)
            {
                $next=$current_page+1;
                echo "<li><a href='".$self."&page_no=".$next."'>Next</a></li>";
                echo "<li><a href='".$self."&page_no=".$total_no_of_pages."'>Last</a></li>";
            }
            ?></ul><?php
        }
    }

    public function paginglinkURL($query, $url, $records_per_page)

    {

        //$self = $_SERVER['PHP_SELF'];
        //$self = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $self = "$_SERVER[REQUEST_URI]";
        $self = explode('&', $self);
        $self = $self['0'];

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $total_no_of_records = $stmt->rowCount();

        if($total_no_of_records > 0)
        {
            ?><ul class="pagination"><?php
            $total_no_of_pages=ceil($total_no_of_records/$records_per_page);
            $current_page=1;
            if(isset($_GET["page_no"]))
            {
                $current_page=$_GET["page_no"];
            }
            if($current_page!=1)
            {
                $previous =$current_page-1;
                echo "<li><a href='".$self."&".$url."page_no=1'>First</a></li>";
                echo "<li><a href='".$self."&".$url."page_no=".$previous."'>Previous</a></li>";
            }
            for($i=1;$i<=$total_no_of_pages;$i++)
            {
                if($i==$current_page)
                {
                    echo "<li class='paginate_button active'><a href='".$self."&".$url."page_no=".$i."';'>".$i."</a></li>";
                }
                else
                {
                    echo "<li><a href='".$self."&".$url."page_no=".$i."'>".$i."</a></li>";
                }
            }
            if($current_page!=$total_no_of_pages)
            {
                $next=$current_page+1;
                echo "<li><a href='".$self."&".$url."page_no=".$next."'>Next</a></li>";
                echo "<li><a href='".$self."&".$url."page_no=".$total_no_of_pages."'>Last</a></li>";
            }
            ?></ul><?php
        }
    }

    public function paginglinkURLJquery($query, $url, $records_per_page)

    {

        //$self = $_SERVER['PHP_SELF'];
        //$self = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $self = "$_SERVER[REQUEST_URI]";
        $self = explode('&', $self);
        $self = $self['0'];

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $total_no_of_records = $stmt->rowCount();

        if($total_no_of_records > 0)
        {
            ?><ul class="pagination"><?php
            $total_no_of_pages=ceil($total_no_of_records/$records_per_page);
            $current_page=1;
            if(isset($_GET["page_no"]))
            {
                $current_page=$_GET["page_no"];
            }
            if($current_page!=1)
            {
                $previous =$current_page-1;
                echo "<li><a href='".$url."page_no=1'>First</a></li>";
                echo "<li><a href='".$url."page_no=".$previous."'>Previous</a></li>";
            }
            for($i=1;$i<=$total_no_of_pages;$i++)
            {
                if($i==$current_page)
                {
                    echo "<li class='paginate_button active'><a href='".$url."page_no=".$i."';'>".$i."</a></li>";
                }
                else
                {
                    echo "<li><a href='".$url."page_no=".$i."'>".$i."</a></li>";
                }
            }
            if($current_page!=$total_no_of_pages)
            {
                $next=$current_page+1;
                echo "<li><a href='".$url."page_no=".$next."'>Next</a></li>";
                echo "<li><a href='".$url."page_no=".$total_no_of_pages."'>Last</a></li>";
            }
            ?></ul><?php
        }
    }
    public function CodeOrder()
    {
        $length_abjad = "2";
        $length_angka = "7";

        $huruf = "ABCDEFGHJKMNPRSTUVWXYZ";


        $i = 1;
        $txt_abjad = "";
        while ($i <= $length_abjad) {
            $txt_abjad .= $huruf{mt_rand(0,strlen($huruf))};
            $i++;
        }


        $datejam = date("His");
        $time_md5 = rand(time(), $datejam);
        $cut = substr($time_md5, 0, $length_angka);


        $acak = str_shuffle($txt_abjad.$cut);
        $acak = 'BD_' .$acak;

        //menghitung dan memeriksa hasil generate di database menggunakan fungsi getTotalRow(),
        //jika hasil generate sudah ada di database maka proses generate akan diulang
        $cek  = 'SELECT id_trx FROM `detail_trxs` WHERE id_trx = "'.$acak.'"';
        $stmt = $this->conn->prepare($cek);
        $stmt->execute();

        if($stmt->rowCount() > 0) { $stmt = CodeOrder(); }

        return $acak;
    }
    public function getDate($format)
    {
        date_default_timezone_set("Asia/Jakarta");

        $tanggal = date($format);

        return $tanggal;
    }
    public function _parsingproductname($product) {
        $tmpname = str_replace('_', ' ', $product);

        $newname = $product;
        if($tmpname) {
            $countname = strlen($tmpname);
            $tmpnewname = substr($tmpname, 0, 12);
            if(is_numeric($tmpnewname)) {
                $newname = substr($tmpname, 12, $countname);
            }
        }
        

        return $newname;
    }
    public function timeAgo($time_ago)
    {
        date_default_timezone_set("Asia/Jakarta");
        $time_ago = strtotime($time_ago);
        $cur_time   = time();
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            return "just now";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "one minute ago";
            }
            else{
                return "$minutes minutes ago";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "an hour ago";
            }else{
                return "$hours hrs ago";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "yesterday";
            }else{
                return "$days days ago";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "a week ago";
            }else{
                return "$weeks weeks ago";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "a month ago";
            }else{
                return "$months months ago";
            }
        }
        //Years
        else{
            if($years==1){
                return "one year ago";
            }else{
                return "$years years ago";
            }
        }
    }
    public function formatPrice($val)
    {
        $count = strlen($val);
        if($count >= 3) {
            $price = number_format($val, 2, ',', '.');
            $price = 'Rp. ' . $price;
        } elseif($val == 0 ) {
            $price = 'Rp. ' . $val;
        } else {
            $price = '$ ' . $val;
        }

        return $price;
    }
    public function _formatdate($val) {
        $Dates = Date('d F Y', strtotime($val));

        return $Dates;
    }
    public function insert($table, $field, $data)
    {
        $sql = "INSERT INTO ". $table ." (" . $field . ") VALUES (". $data .") ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt;
    }
    public function delRecord($table, $field, $id)
    {
        $sql = "DELETE FROM ". $table ." WHERE ". $field ." = ". $id;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt;
    }
    public function actionMsg($type, $msg)
    {
        switch($type)
        {
            case'c':
                $msg = $msg. ' Berhasil ditambahkan ke Database!';
            break;
            case'r':
                $msg = $msg. ' telah tersedia di Database!';
            break;
            case'u':
                $msg = $msg. ' Berhasil di update ke Database!';
            break;
            case'd':
                $msg = $msg. ' Berhasil dihapus dari Database!';
            break;
            default:
                $msg = '';
            break;

        }
        return $msg;
    }
    public static function systemInfo()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform    = "Unknown OS Platform";
        $os_array       = array('/windows phone 8/i'    =>  'Windows Phone 8',
            '/windows phone os 7/i' =>  'Windows Phone 7',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile');
        $found = false;
//        $addr = new RemoteAddress;
        $device = '';
        foreach ($os_array as $regex => $value)
        {
            if($found)
                break;
            else if (preg_match($regex, $user_agent))
            {
                $os_platform    =   $value;
                $device = !preg_match('/(windows|mac|linux|ubuntu)/i',$os_platform)
                    ?'MOBILE':(preg_match('/phone/i', $os_platform)?'MOBILE':'SYSTEM');
            }
        }
        $device = !$device? 'SYSTEM':$device;
        return array('os'=>$os_platform,'device'=>$device);
    }
    public function generateRandomString($length = 10)
    {

        return substr(str_shuffle(str_repeat($x='!@#$%^&*()0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public function newPassword($pass){
        $password = password_hash($pass, PASSWORD_DEFAULT);

        return $password;
    }

    public function _debugvar($var){
        echo '<pre>';
        $data = var_dump($var);
        echo '</pre>';

        return $data;
    }

    public function url() {
        // first get http protocol if http or https

        $base_url = (isset($_SERVER['HTTPS']) &&

        $_SERVER['HTTPS']!='off') ? 'https://' : 'http://';

        // get default website root directory

        $tmpURL = dirname(__FILE__);

        // when use dirname(__FILE__) will return value like this "C:\xampp\htdocs\my_website",

        //convert value to http url use string replace, 

        // replace any backslashes to slash in this case use chr value "92"

        $tmpURL = str_replace(chr(92),'/',$tmpURL);

        // now replace any same string in $tmpURL value to null or ''

        // and will return value like /localhost/my_website/ or just /my_website/

        $tmpURL = str_replace($_SERVER['DOCUMENT_ROOT'],'',$tmpURL);

        // delete any slash character in first and last of value

        $tmpURL = ltrim($tmpURL,'/');

        $tmpURL = rtrim($tmpURL, '/');


        // check again if we find any slash string in value then we can assume its local machine

            if (strpos($tmpURL,'/')){

        // explode that value and take only first value

            $tmpURL = explode('/',$tmpURL);

            $tmpURL = $tmpURL[0];

            }

        // now last steps

        // assign protocol in first value

        if ($tmpURL !== $_SERVER['HTTP_HOST'])

        // if protocol its http then like this

            $base_url .= $_SERVER['HTTP_HOST'].'/'.$tmpURL.'/';

            else

        // else if protocol is https

            $base_url .= $tmpURL.'/';

        // give return value
        $base_url = explode('/', $base_url);
        $BasedURL = $base_url[0]. '//' . $base_url[2] . '/';
        if($base_url[3] == 'bungdav') {
            $BasedURL = $base_url[0]. '//' . $base_url[2] . '/'. $base_url[3].'/';
        }

        return $BasedURL; // = http://example.com/path/directory
    }

    public function _rangedate($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');
    
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
    
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    
        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }
    
        return $array;
    }

    public function capitalize($string) { 
        $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE); 
        $new_string = ''; 
        foreach ($sentences as $key => $sentence) { 
            $new_string .= ($key & 1) == 0? 
                ucfirst(strtolower(trim($sentence))) : 
                $sentence.' '; 
        } 
        return trim($new_string); 
    }
      

}

?>

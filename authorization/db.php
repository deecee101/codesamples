<?php 
include_once($spath.'php/functions.php');
include_once($servroot.'mySQL_AUTH.php');

$sqldbname = 'codesamples';
$idxdbname = 'codesamples';
$_SESSION['dbname'] = $sqldbname;

$dbh = new PDO("mysql:host=$host", $admin, $admin_password);

$_SESSION['nodb'] = false; //is this the first run?
$publicid = rand(500000, 1000000);
$_SESSION['public'] = array('id'=>$publicid,'uname'=>'user'.makehash(),'email'=>'null@null.com','avatar'=>'imgs/user.png','access'=>'g','rating'=>'g','role'=>'user','age'=>'0'); //is this a public user?
if(!isset($_SESSION['user'])){
    $_SESSION['user'] = $_SESSION['public'];
}

$myQuery = "SHOW DATABASES LIKE '".$sqldbname."';";// exit($myQuery);
try{
     $result = $dbh->query($myQuery)->fetch(PDO::FETCH_ASSOC);
    if($result == NULL || $result == ""){
         $_SESSION['nodb'] = true;
     }else{
         $_SESSION['nodb'] = false;
        try {
            $db = new PDO('mysql:host='.$host.';dbname='.$sqldbname, $admin, $admin_password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        catch(PDOException $e) {
            exit("5.".$e->getMessage());
        }
    }
}
catch(PDOException $e){
     exit("3. error : ".$e->getMessage());
}

$tables = array();
$tables['users'] = array('id'=>'MEDIUMINT NOT NULL AUTO_INCREMENT','uname'=>'TEXT','pword'=>'TEXT','pinpass'=>'TEXT','avatar'=>'TEXT','description'=>'LONGTEXT','email'=>'TEXT','access'=>'TEXT','address'=>'TEXT',"latlng"=>"TEXT",'sms'=>'TEXT','notifications_subscription'=>'TEXT',"deviceaccess"=>"TEXT","role"=>"TEXT");
$tables['visitors'] = array('id' => 'MEDIUMINT NOT NULL AUTO_INCREMENT', 'internet_provider' => 'TEXT', 'city' => 'TEXT','country' => 'TEXT','countrycode' => 'TEXT','isp' => 'TEXT','lat' => 'TEXT','lon' => 'TEXT','org' => 'TEXT','ipaddress' => 'TEXT','region' => 'TEXT','timezone' => 'TEXT','zip' => 'TEXT','browser' => 'TEXT','referrer' => 'TEXT','timedate' => 'TEXT','page' => 'TEXT','userid' => 'INT(11)','token' => 'TEXT');
if($_SESSION['nodb'] == true){
    try {
        $dbh->exec("CREATE DATABASE IF NOT EXISTS `".$sqldbname."`;
                GRANT ALL PRIVILEGES ON `".$sqldbname."`.* TO '".$admin."'@'localhost' IDENTIFIED BY '".$admin_password."' WITH GRANT OPTION; 
                FLUSH PRIVILEGES;");
    } 
    catch (PDOException $e) {
        die("4. error : ". $e->getMessage());
    }
    try {
        $db = new PDO('mysql:host='.$host.';dbname='.$sqldbname, $admin, $admin_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
    catch(PDOException $e) {
        exit("5.".$e->getMessage());
    }
}

if(!isset($_SESSION['no_auth_tables'])){$_SESSION['no_auth_tables'] = true;}
if($_SESSION['no_auth_tables'] == true){
    foreach($tables as $ke => $val){
        $cquery = "SHOW TABLES LIKE '".$ke."';";
        try{
            $_result = $db->query($cquery)->fetch(PDO::FETCH_ASSOC);
            if($_result == false){
                $sql = "CREATE TABLE IF NOT EXISTS ".$ke." ("; 
                        foreach($val as $k => $v){
                            $sql .= $k." ".$v.", ";
                        }
                        $sql = str_replace('database','db_', $sql);
                        $sql = rtrim($sql, ", ");
                        $sql .= ", PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                        //logerror($sql);
                        $db->exec($sql);
                if($ke == 'visitors'){
                    $sql = "CREATE UNIQUE INDEX user_id ON visitors (userid)";
                        try { $db->exec($sql); }
                        catch(PDOException $e) { die ($e->getMessage()); }
                }
            }else{$_SESSION['no_auth_tables'] = false;}
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    } 
    
    $myQuery = "SELECT * FROM users WHERE uname = 'default'";
    try{
        $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
        if($result['id'] == NULL || $result['id'] == ""){
            $my_query = "REPLACE INTO users (uname,pword,pinpass,avatar,description,email,access,sms,role) VALUES ('default','".crypt_apr1_md5('default','s4lt')."','".crypt_apr1_md5('default','s4lt')."','imgs/user.png','','codemonkey@".$_SERVER['HTTP_HOST']."','xxx','8005551234','user'); ";
            //logerror($my_query);
            try {
                $db->exec($my_query);
                }
            catch(PDOException $e)
                {
                echo $e->getMessage();
                }
            include_once($spath.'php/htaccess.php');
            @mkdir($servroot.'auth/');
            $ht = new htaccess();
            $ht->setFPasswd($servroot.'auth/.htpasswd');
            ///$ht->setFHtaccess($spath."/.htaccess");
            $ht->addUser('default','default');

            $str=file_get_contents('.htaccess');
            $str=str_replace('# ', '',$str);
            $str=str_replace('*****',$servroot.'auth/.htpasswd',$str);
            file_put_contents('.htaccess', $str); 
        }
    }
    catch(PDOException $e){
         echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
         ?><SCRIPT>alert('<?php echo $e->getMessage(); ?>');</SCRIPT><?php  exit;
    }
    $_SESSION['no_auth_tables'] = false;
}
$internet_provider = '';
$city = '';
$country = '';
$countrycode = '';
$isp = '';
$lat = '';
$lon = '';
$org = '';
$ipaddress = '';
$region = '';
$timezone = '';
$zip = '';
$browser = '';
$referrer = '';
$timedate = '';
$userid = $_SESSION['user']['id'];
$token=base64_encode(session_id().'default');

$dbtables['user'] = $tables['users'];
if(!isset($_SESSION['visit'])){$_SESSION['visit'] = array();}
if(!isset($_SESSION['visit'][$token])){
    $my_query = "REPLACE INTO visitors(internet_provider, city,country,countrycode,isp,lat,lon,org,ipaddress,region,timezone,zip,browser,referrer,timedate,page,userid,token) VALUES ('".$internet_provider."','".$city."','".$country."','".$countrycode."','".$isp."','".$lat."','".$lon."','".$org."','".$ipaddress."','".$region."','".$timezone."','".$zip."','".$browser."','".$referrer."','".$timedate."','".$_SERVER['PHP_SELF']."?".urldecode($_SERVER['QUERY_STRING'])."','".$userid."','".$token."')";
        logerror($my_query.' db.php');
        try {
            $db->exec($my_query);
            }
        catch(PDOException $e)
            {
            echo $e->getMessage();
            }
    $_SESSION['visit'][$token] = true;
}

$loginurl = $hpath.'login/';
?>
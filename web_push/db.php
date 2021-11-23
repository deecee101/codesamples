<?php 
//byteshaman.us database
//0563 is admin folder
include_once($spath.'php/functions.php');
//exit(crypt_apr1_md5('default','f4rt'));
try{
    $db = new PDO('sqlite:DB.sqlite');
    //echo "initiated the database file<br>\n";
}
catch(PDOException $e){
     exit($e->getMessage());
}
$_SESSION['firstrun'] = false; //is this the first run?
$publicid = rand(500000, 1000000);
$_SESSION['user'] = array('id'=>$publicid,'uname'=>'user'.makehash(),'email'=>'null@null.com','avatar'=>$hostroot.'imgs/user.png','access'=>'g','rating'=>'g','age'=>'0'); //is this a public user?
if(!isset($_SESSION['user']['role'])){$_SESSION['user']['role'] = 'user';}

$output = '';
$tables = array();
$tables['apis'] = array('browser_key' => 'VARCHAR(100)', 'server_key' => 'VARCHAR(100)','subject' => 'TEXT');
$tables['users'] = array('id'=>'INTEGER PRIMARY KEY AUTOINCREMENT','uname'=>'TEXT','pword'=>'TEXT','secretword'=>'TEXT','avatar'=>'TEXT','description'=>'LONGTEXT','email'=>'TEXT','access'=>'TEXT','sms'=>'TEXT','notifications_subscription'=>'');
$tables['subscribers'] = array('id'=>'INTEGER PRIMARY KEY','endpoint'=>'TEXT','auth'=>'TEXT','p256dh'=>'TEXT');
foreach($tables as $ke => $val){
 	$cquery = "SELECT name FROM sqlite_master WHERE type='table' AND name='".$ke."';";
	try{
		$_result = $db->query($cquery)->fetch(PDO::FETCH_ASSOC);
		if($_result == false){
			$_SESSION['firstrun'] = true;
			$sql = "CREATE TABLE IF NOT EXISTS ".$ke." ("; ///if not exists only for sqlite 3.3 and up? "select sqlite_version();"
				    //echo '['.$ke."- \n";
				    foreach($val as $k => $v){
				 		$sql .= $k." ".$v.", ";
				 	}
				    //echo '] ';
				    $sql = str_replace('database','db_', $sql);
				    $sql = rtrim($sql, ", ");
				    $sql .= ")";
				//echo $sql."<br>\n";
					$db->exec($sql);
					$output .= "created table ".$ke."<br> \n";
		}else{$output .= $ke." table already exists<br> \n";}
	}
	catch(PDOException $e){
    	exit($e->getMessage());
	}
}
//echo $output; $output = '';
?>
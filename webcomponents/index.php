<?php  
if(!isset($_SESSION)){session_start();}
//date_default_timezone_set('America/New_York');
$spath = str_replace("\\","/",getcwd()).'/';
$servroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/';
if(isset($_SERVER['HTTPS'])){$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';}else{$protocol = 'http';}
$hostroot = $protocol.'://'.$_SERVER['HTTP_HOST'].'/';
$hpath = str_replace($servroot, $hostroot, $spath);
$name = basename(__FILE__, '.php'); 
$fname = basename(__FILE__); 
$docurl = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$self = $_SERVER['PHP_SELF'];
/*echo getcwd().'<br>'; 
echo $spath.'<br>';
echo $servroot.'<br>';
echo $hpath.'<br>';
echo $hpath;
echo $fname.'<br>';
echo $name.'<br>';
echo $docurl.'<br>'; exit;*/
include_once($spath.'php/functions.php');

try{
    $db = new PDO('sqlite:'.$spath.'DB.sqlite');
}
catch(PDOException $e){
     exit($e->getMessage());
}
$_SESSION['firstrun'] = false; //is this the first run?
$_SESSION['member'] = false; //is this a member user?

$tables = array();
$tables['reminders'] = array('id' => 'INTEGER PRIMARY KEY AUTOINCREMENT', 'listname' => 'TEXT', 'description' => 'TEXT', 'timedate' => 'TEXT','active' => 'TEXT');
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
				    $sql = str_replace('database','db_', $sql);
				    $sql = rtrim($sql, ", ");
				    $sql .= ")";
					$db->exec($sql);
		}
	}
	catch(PDOException $e){
    	exit($e->getMessage());
	}
}
if($_SESSION['firstrun'] == true){
    $my_query = "REPLACE INTO reminders (listname, description,timedate,active) VALUES ('my todo list','start_list_app','".date('Y-m-d H:i:s', strtotime('12/10/1998 10:00'))."','false'); "; //exit($my_query);
    //echo $my_query.'<BR><BR>';
    try {
        $db->exec($my_query);
        }
    catch(PDOException $e)
        {
        echo $e->getMessage();
        }
    $my_query = "REPLACE INTO reminders (listname, description,timedate,active) VALUES ('my todo list','finish computer','".date('Y-m-d H:i:s', strtotime('10/10/2021 10:00'))."','true'); "; //exit($my_query);
    //echo $my_query.'<BR><BR>';
    try {
        $db->exec($my_query);
        }
    catch(PDOException $e)
        {
        echo $e->getMessage();
        }
    $my_query = "REPLACE INTO reminders (listname, description,timedate,active) VALUES ('my todo list','grocery shopping','".date('Y-m-d H:i:s', strtotime('10/11/2021 15:23'))."','true'); "; //exit($my_query);
    //echo $my_query.'<BR><BR>';
    try {
        $db->exec($my_query);
        }
    catch(PDOException $e)
        {
        echo $e->getMessage();
        }
    $_SESSION['firstrun'] = false;
}
$lists = Array();
$reminders = Array();
$myQuery = "SELECT DISTINCT listname FROM reminders";
	try{
		 $result = $db->query($myQuery);
	}
	catch(PDOException $e){
		 exit($e->getMessage());
	}
	if($result != false){
		while($results = $result->fetch(PDO::FETCH_ASSOC)){
			array_push($lists, $results);
			$reminders[$results['listname']] = Array();

		}
	}
$myQuery = "SELECT * FROM reminders";
	try{
		 $result = $db->query($myQuery);
	}
	catch(PDOException $e){
		 exit($e->getMessage());
	}
	if($result != false){
		while($results = $result->fetch(PDO::FETCH_ASSOC)){
			//do something list
			if($results['description'] == 'start_list_app'){
				$reminders[$results['listname']][0] = $results;
			}else{
				array_push($reminders[$results['listname']],$results);
			}
		}
	}

if(isset($_POST['axn'])){
switch($_POST['axn']){
	case "addreminder": 
		$entrydata = base64_decode($_POST['reminder']);
		$e = explode('|',$entrydata);
		$listname = $e[0];
		$description = $e[1];
		$err['flag'] = NULL;
		preg_replace("/[^a-zA-Z0-9 ,\. ' \" $ !@#%& \* \- \+ = \? ; :]/", "", $description);
		if(validate($listname, 'title') == false){$err['flag']=$err['loose']." list name was invalid";};
		if(validate($description, 'loose') == false){$err['flag']=$err['loose']." description was invalid";};
		if($err['flag'] != NULL){
			echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			?><SCRIPT>alert('<?php echo $err['flag']; ?>');</SCRIPT><?php  exit;
		}
		$timedate = date('Y-m-d H:i:s', time());
		$active = 'true';
		$my_query = "REPLACE INTO reminders (listname,description,timedate,active) VALUES ('".$listname."',".$db->quote($description).",'".$timedate."','".$active."'); ";
		    try {
		        $db->exec($my_query);
		        }
		    catch(PDOException $e)
		        {
		        echo $e->getMessage();
		        }
	break;
	case "forgetreminder": 
		if(validate($_POST['active'], 'az') == false){$err['flag']=$err['loose']." active status was invalid";};
		if(validate($_POST['listname'], 'alnum') == false){$err['flag']=$err['loose']." list name was invalid";};
		if(validate($_POST['timedate'], 'loose') == false){$err['flag']=$err['loose']." date time was invalid";};
		if($err['flag'] != NULL){
			echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			?><SCRIPT>alert('<?php echo $err['flag']; ?>');</SCRIPT><?php  exit;
		}
		$myQuery = "UPDATE reminders SET active = '".$_POST['active']."' WHERE listname = '".$_POST['listname']."' AND timedate = '".$_POST['timedate']."'";
			try{
				 $result = $db->query($myQuery);
			}
			catch(PDOException $e){
				 exit($e->getMessage());
			}
	break;
	case "removereminder": 
		if(validate($_POST['listname'], 'alnum') == false){$err['flag']=$err['loose']." list name was invalid";};
		if(validate($_POST['timedate'], 'loose') == false){$err['flag']=$err['loose']." date time was invalid";};
		if($err['flag'] != NULL){
			echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			?><SCRIPT>alert('<?php echo $err['flag']; ?>');</SCRIPT><?php  exit;
		}
		$mydQuery = "DELETE FROM reminders WHERE listname = '".$_POST['listname']."' AND timedate = '".$_POST['timedate']."'"; //exit($mydQuery);
		try{
		     $result = $db->query($mydQuery);
		}
		catch(PDOException $e){
		     exit($e->getMessage());
		}
	break;
	case "removechecklist":
		if(validate($_POST['listname'], 'alnum') == false){$err['flag']=$err['loose']." list name was invalid";};
		if($err['flag'] != NULL){
			echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			?><SCRIPT>alert('<?php echo $err['flag']; ?>');</SCRIPT><?php  exit;
		}
		$mydQuery = "DELETE FROM reminders WHERE listname = '{$_POST['listname']}'";

		echo '{"status":"ok","function":"deleted list '.$_POST['listname'].'"}';  exit;

		break;
	default:
} exit;
}else if(isset($_GET['axn'])){
switch($_GET['axn']){
	default;
} exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>reminders</title>

	<!-- Mobile -->
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />



<script src="<?php echo $hpath; ?>js/functions.js" ></script>
<LINK href="<?php echo $hpath; ?>css.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./checklistbox/checklist-box.php" ></script>
</head>
<body>
<?php foreach($lists as $ke => $val){ ?>
	<checklist-box id="checklistbox_<?=$val['listname']; ?>" class="checklistbox" width="35vw" title="<?=$val['listname']; ?>" style="min-width:310px"
	<?php
		$rem = $reminders[$val['listname']];
		for($i=1;$i<count($rem);$i++){
			echo ' reminder-'.($i-1).'="'.$rem[$i]['id'].'|'.$rem[$i]['description'].'|'.$rem[$i]['timedate'].'|'.$rem[$i]['active'].'"'."\n";
		}
	?> >
	</checklist-box>
<?php } ?><br>
<span style="font-size:small" ><a href="db_admin.php" target="_blank" >Database Admin</a> password : admin</span>
<script type="text/javascript">
	window.removechecklist = function(cl){
		//demo don't need this
	}
</script>
</body>
</html>
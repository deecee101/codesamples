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
echo $hostroot.'<br>';
echo $hpath;
echo $fname.'<br>';
echo $name.'<br>';
echo $docurl.'<br>'; exit;*/
include_once($spath.'../php/functions.php');
include_once($servroot.'mySQL_AUTH.php');
$dbname = 'codesamples';

$sqldbname = 'codesamples';
$idxdbname = 'codesamples';
$_SESSION['dbname'] = $sqldbname;

$tables = array();
$tables['videos'] = array('id'=>'INTEGER PRIMARY KEY AUTOINCREMENT','title'=>'TEXT','description'=>'LONGTEXT','idx'=>'int(11) NOT NULL','url'=>'TEXT');

try{
	$db = new PDO('sqlite:DB.sqlite');
}
catch(PDOException $e){
	 exit($e->getMessage());
}
foreach($tables as $ke => $val){
 	$cquery = "SELECT name FROM sqlite_master WHERE type='table' AND name='".$ke."';";
	try{
		$_result = $db->query($cquery)->fetch(PDO::FETCH_ASSOC);
		if($_result == false){
			$sql = "CREATE TABLE IF NOT EXISTS ".$ke." ("; ///if not exists only for sqlite 3.3 and up? "select sqlite_version();"
				    //echo '['.$ke."- \n";
				    foreach($val as $k => $v){
				 		$sql .= $k." ".$v.", ";
				 	}
				    //echo '] ';
				    $sql = str_replace('database','_db', $sql);
				    $sql = rtrim($sql, ", ");
				    $sql .= ")";
				//echo $sql."<br>\n";
					$db->exec($sql);
			}
	}
	catch(PDOException $e){
    	exit($e->getMessage());
	}
}

$myQuery = "SELECT count(*) as entries FROM videos";
try{
    $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
    if($result['entries'] <= 0){
         
		$path = realpath('../items/');
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		$i=1;
		foreach($objects as $file => $object){
			$file = str_replace("\\",'/', $file);
			$e=explode('/', $file);
			$fname = $e[count($e)-1];
			$n = explode('.', $fname);
			$filename = $n[0];
			$ext = $n[1];
			if($filename =='.' || $filename =='..' || $filename == ''){continue;}
			$_myQuery = "SELECT * FROM videos WHERE url = ".$db->quote(str_replace($servroot, '', $file));
			logerror($_myQuery);
			try{
			     $_result = $db->query($_myQuery)->fetch(PDO::FETCH_ASSOC);
			    if($_result == false || $_result['id'] == NULL || $_result['id'] == ""){
			         
			     }else{
			     	$i++;
			        continue;
			     }
			}
			catch(PDOException $e){
			     echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			}

			$my_query = "REPLACE INTO videos (title,description,idx,url) VALUES (".$db->quote($filename).", ".$db->quote(randomwords()).", '".$i."', ".$db->quote(str_replace($servroot, '', $file))."); ";
			    logerror($my_query);
			    try {
			        $db->exec($my_query);
			        }
			    catch(PDOException $e)
			        {
			        echo $e->getMessage();
			        }
			$i++;
		}



     }
}
catch(PDOException $e){
     
}


//if(!isset($_POST['axn']) && !isset($_GET['axn'])){exit;}
if(isset($_POST['axn'])){
	switch($_POST['axn']){
		case "addVideoList":
			$path = realpath('../items/');
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
			$i=1;
			foreach($objects as $file => $object){
				$file = str_replace("\\",'/', $file);
				$e=explode('/', $file);
				$fname = $e[count($e)-1];
				$n = explode('.', $fname);
				$filename = $n[0];
				$ext = $n[1];
				if($filename =='.' || $filename =='..' || $filename == ''){continue;}
				$my_query = "REPLACE INTO videos (title,description,idx,url) VALUES (".$db->quote($filename).", ".$db->quote(randomwords()).", '".$i."', ".$db->quote(str_replace($servroot, '', $file))."); ";
				    logerror($my_query);
				    try {
				        $db->exec($my_query);
				        }
				    catch(PDOException $e)
				        {
				        echo $e->getMessage();
				        }
				    $i++;
			}
			echo '{"status":"ok","function":"added videos completed"}';  exit;
			break;
		case "updatedb":
			//exit('update '.$_POST['itmid']);

			$_POST['val'] = base64_decode($_POST['val']);
			//include_once($servroot.'php/regex.php');
			$err['flag'] = NULL;
			if(validate($_POST['val'], 'loose') == false){$err['flag']=$err['loose']." ";};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}

			$myQuery = "UPDATE ".$_POST['tbl']." SET ".$_POST['var']." = ".$db->quote($_POST['val'])." WHERE id = '".$_POST['itmid']."'";
			logerror($myQuery);
				try{
					 $result = $db->query($myQuery);
				}
				catch(PDOException $e){
					 echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
				}
			echo '{"status":"ok","function":"completed video data update"}';  exit;
			break;
		case "deleteitm":
			$err['flag'] = NULL;
			if(validate($_POST['itmid'], 'num') == false){$err['flag']=$err['loose']." ";};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			$mydQuery = "DELETE FROM videos WHERE id = '{$_POST['itmid']}'";
			logerror($mydQuery);
			try{
			     $result = $db->query($mydQuery);
			}
			catch(PDOException $e){
			     echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			}
			echo '{"status":"ok","function":"completed item delete "}';  exit;
			break;
		case "deletedb":
			$db->query('KILL CONNECTION_ID()');
			$db = NULL;
			if(is_file('DB.sqlite')){unlink('DB.sqlite');}
			echo '{"status":"ok","function":"deleted database "}';  exit;
			break;
		case "emptydb":
			$myQuery = "SELECT name FROM sqlite_master WHERE type='table' and name NOT LIKE 'sqlite_%';";
			logerror($myQuery);
			try{
				 $result = $db->query($myQuery);
			}
			catch(PDOException $e){
				echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			}
			$rows = array();
			while($results = $result->fetch(PDO::FETCH_ASSOC)){
				array_push($rows, $results);
			}
			$n = 0;
			foreach ($rows as $row) {
			    $sql = 'DELETE FROM  '. $row['name']. ';';
				logerror($sql);
			    $db->exec($sql);
			    ++$n;
			}
			echo '{"status":"ok","function":"completed database empty"}';  exit;
			break;
		case "emptytbl":
			$err['flag'] = NULL;
			if(validate($_POST['tbl'], 'alnum') == false){$err['flag']=$err['loose']." ";};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			$sql = 'DELETE FROM  ' . $_POST['tbl'] . ';';
			logerror($sql);
		    $db->exec($sql);
		    echo '{"status":"ok","function":"completed table empty"}';  exit;
			break;
		case "deletetbl":
			$err['flag'] = NULL;
			if(validate($_POST['tbl'], 'alnum') == false){$err['flag']=$err['loose']." ";};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			$sql = 'drop table if exists ' . $_POST['tbl'] . ';';
			logerror($sql);
		    $db->exec($sql);
		    echo '{"status":"ok","function":"completed table delete"}';  exit;
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
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="<?php echo $hpath; ?>../js/functions.js" ></script>
</head>
<body>
<?php

$itms = array();
$myQuery = "SELECT * FROM videos ORDER BY idx ASC";
	try{
		 $result = $db->query($myQuery);
	}
	catch(PDOException $e){
		 exit($e->getMessage());
	}
	while($results = $result->fetch(PDO::FETCH_ASSOC)){
		array_push($itms, $results);
	}
echo "<h1>VIDEOS</h1><br>";
echo "this is a demonstration of CREATE READ UPDATE DELETE on an SQLite database<br>";
echo "to view the database <a href='".$hpath."sqliteAdmin.php' target='_blank'>ADMIN HERE</a> <strong style='color:red' >password is \"admin\"</strong><br/> \n";
echo "this demo will only reload the videos into the database if the videos table is missing or empty<br> \n";
echo "<button onclick=\"emptytbl('videos')\"  >empty table</button><button onclick=\"deletetbl('videos')\"  >delete table</button><button onclick=\"emptydb()\"  >empty db</button><button onclick=\"deletedb()\"  >delete db</button> \n";
echo "<div id='dbdiv' > \n";
foreach($itms as $ke => $val){ ?>
<hr>
<FORM NAME="_<?php echo $val['id']; ?>form" ID="_<?php echo $val['id']; ?>form" METHOD="POST"  enctype='multipart/form-data' ACTION="<?=$self; ?>" TARGET="processor" >
	<video src="<?=$hostroot.$val['url']; ?>" style="width:300px"></video><br>
	<DIV STYLE="display:inline-block;margin:5px" >index:<BR /><input type="text" name="idx" id="idx" value="<?php echo $val['idx']; ?>" size="2" onchange="updatedb(this)" data-tbl="videos"  data-itmid = "<?php echo $val['id']; ?>"></DIV>
	<DIV STYLE="display:inline-block;margin:5px" >title:<BR /><input type="text" name="title" id="title" value="<?php echo $val['title']; ?>" size="50" onchange="updatedb(this)"  data-tbl="videos" data-itmid = "<?php echo $val['id']; ?>" ></DIV><br>
	<DIV STYLE="display:inline-block;margin:5px" >description:<br><textarea name="description" id="description" cols="40" rows="10" onchange="updatedb(this)"  data-tbl="videos" data-itmid = "<?php echo $val['id']; ?>" ><?php echo $val['description']; ?></textarea></DIV>
<INPUT TYPE="hidden" NAME="axn" ID="axn" VALUE="edititm" >
<button  >update</button>
<button onclick="deleteitm('<?php echo $val['id']; ?>')" >delete</button>
</FORM>
<?php }
echo '</div>';
?>
<IFRAME SRC="<?=$hostroot; ?>blank.html" NAME="processor" ID="processor" STYLE="width:0px;height:0px;position:absolute;top:0px; left:0px" FRAMEBORDER="no" allowtransparency="yes" ></IFRAME>
</body>
<script>
	window.updatedb = function(i){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: "axn=updatedb&var="+i.getAttribute('id')+'&val='+base64Encode(i.value)+"&tbl="+i.dataset.tbl+"&itmid="+i.dataset.itmid
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
			}else{
				console.log(response4.error);
				alert(response4.error);
			}
		});
	}
	window.deleteitm = function(itmid){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: 'axn=deleteitm&itmid='+itmid
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
				document.getElementById('_'+(itmid)+'form').style.display = 'none';
			}else{
				console.log(response4.error);
			}
		});
	}
	window.deletedb = function(){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: 'axn=deletedb'
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
				document.getElementById('dbdiv').style.display = 'none';
			}else{
				console.log(response4.error);
			}
		});	
	}
	window.emptydb = function(){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: 'axn=emptydb'
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
				document.getElementById('dbdiv').style.display = 'none';
			}else{
				console.log(response4.error);
			}
		});	
	}
	window.emptytbl = function(tbl){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: 'axn=emptytbl&tbl='+tbl
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
				document.getElementById('dbdiv').style.display = 'none';
			}else{
				console.log(response4.error);
			}
		});
	}
	window.deletetbl = function(tbl){
		fetch('<?=$self; ?>', {
			method: 'post',
		    headers: {
		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		      },
		    body: 'axn=deletetbl&tbl='+tbl
		}).then(function(response3){
			return response3.json();
		}).then(function(response4){
			if(response4.status.toLowerCase() == 'ok'){
				console.log(response4);
				document.getElementById('dbdiv').style.display = 'none';
			}else{
				console.log(response4.error);
			}
		});
	}
</script>
</html>
<?php 
//this file will clear the demo
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
include_once($spath.'php/functions.php');
include_once($servroot.'mySQL_AUTH.php');

$sqldbname = 'codesamples';
$idxdbname = 'codesamples';

$dbh = new PDO("mysql:host=$host", $admin, $admin_password);
$mydQuery = "DROP DATABASE ".$sqldbname;
	try{
	     $result = $dbh->query($mydQuery);
	}
	catch(PDOException $e){
	     exit($e->getMessage());
	}
if(is_file($spath.'.htaccess')){unlink($spath.'.htaccess');}
copy($spath.'DEMO.htaccess',$spath.'.htaccess');

if(is_file($servroot.'auth/.htpasswd')){unlink($servroot.'auth/.htpasswd'); rmdir($servroot.'auth');}
if(is_file($spath.'error.txt')){unlink($spath.'error.txt');}
?>
<html><head><title></title></head>
 <body>
 			<script type="text/javascript">
				window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
				window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"}; // This line should only be needed if it is needed to support the object's constants for older browsers
				window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
				if (!window.indexedDB) {
				    window.alert("Your browser doesn't support a stable version of IndexedDB. This website won't work on your browser.");
				}else{
					var dbExists = true;
					var request = window.indexedDB.open("<?php echo $idxdbname; ?>");
					request.onupgradeneeded = function (e){
						console.log('need database');
					    e.target.transaction.abort();
					    dbExists = false;
					}
					request.onerror = function(e){
					    console.log('no database');
					    //setTimeout("window.location='<?=$hpath; ?>'",1000);
					}
					request.onsuccess = function(e){
						console.log('has db');
						window.db = e.target.result;
						deletedb();
						//window.authenticate('users','3','token');
						//setTimeout("window.location='<?=$hpath; ?>'",1000);
					}
				}
				window.deletedb = function(){
				    if(db){
				        db.close();
				        var req = indexedDB.deleteDatabase('<?php echo $idxdbname; ?>');
				        req.onsuccess = function () {
				            console.log("Deleted database successfully");
				        };
				        req.onerror = function () {
				            console.log("Couldn't delete database");
				        };
				        req.onblocked = function () {
				            console.log("Couldn't delete database due to the operation being blocked");
				        };
				        delete db; 
				    }              
				}
			</script>
</body>
</html>
<?php 
 	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);
	session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_start();
    session_regenerate_id(true);
?>
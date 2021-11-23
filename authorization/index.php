<?php //exit('here');
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
include_once("db.php");
if(!isset($_SESSION['user']) || $_SESSION['user']['id'] == '0'){logerror('no session user'); /*exit;*/ header("location:".$hpath."login/"); exit;}
if(!isset($_SESSION['user']['token'])){logerror('no user token'); /*exit;*/ header("location:".$hpath."login/"); exit;}
$err['flag'] = NULL;
if(validate($_SESSION['user']['id'], 'num') == false){$err['flag']="something was wrong with the user id please try again";};
if($err['flag'] != NULL){
    logerror('user id invalid'); /*exit;*/
    header("location:".$hpath."clear/");   exit;
}

$myQuery = "SELECT * FROM users WHERE id = '{$_SESSION['user']['id']}'"; logerror($myQuery);
try{
     $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
    if($result['id'] == NULL || $result['id'] == ""){
        logerror('no matching user id for logdin'); /*exit;*/
        header('location:'.$hpath."clear/");   exit;
     }
}
catch(PDOException $e){
     logerror($e->getMessage()); /*exit;*/
     header('location:'.$hpath."clear/");   exit;
}

$myQuery = "SELECT * FROM visitors WHERE userid = '{$_SESSION['user']['id']}' AND token<>'' ORDER BY timedate DESC LIMIT 0,1";
try{
        $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
        if($result['id'] == NULL || $result['id'] == ""){
            //exit('no user matching session');
             logerror('no user matching session'); /*exit;*/
             header('location:'.$hpath."clear/");   exit;
        }
    }
    catch(PDOException $e){
        logerror($e->getMessage()); /*exit;*/
        header('location:'.$hpath."clear/");   exit;
    }

if(count($_POST) == 0){
    $_POST = json_decode(file_get_contents('php://input'), true); //for php 7
}
if(!isset($_SESSION['user']['locationpermission'])){
    if($_SESSION['user']['address'] != '' && $_SESSION['user']['address'] != NULL){ 
        $_SESSION['user']['locationpermission'] = 'true';
    }else{ $_SESSION['user']['locationpermission'] = 'false'; }
}
if(isset($_POST['axn']) && $_POST['axn'] != NULL){
    switch($_POST['axn']){
        default:
            exit;
    } exit;
}
 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Codesamples :: dashboard</title>

    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
    <script src="<?php echo $hpath; ?>js/functions.js" ></script>
</head>
<body onload="dbcnx()" style="text-align:center">
    <div style="display:block;background:black;color:white;font-size:x-large" >User Dashboard </div>

    </div>
    <IFRAME SRC="<?php echo $hostroot ?>blank.html" NAME="processor" ID="processor" STYLE="width:0px;height:0px;position:absolute;top:0px; left:0px" FRAMEBORDER="no" allowtransparency="yes" ></IFRAME>
    <div id="flag" style="z-index:2000;text-align:center;position:fixed;bottom:0px;left:0px;width:100%;display:block;overflow:hidden;background:gray;color:white;height:0px" ></div>

<script type="text/javascript">
var flag = document.getElementById('flag');
window.showflag = function(msg){
  flag.innerHTML = msg;
  flag.style.height = "auto"; flag.style.overflow = "auto";
  setTimeout('flag.style.height="0px";',1000);
}

window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"}; // This line should only be needed if it is needed to support the object's constants for older browsers
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
if (!window.indexedDB) {
    window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
}else{
    window.db; //indexedb object
    window.DB = []; //window object to hold indexeddb data
} 
window.dbtables = [<?php
    $str0 = '';
    foreach($dbtables as $ke => $val){
        $str0 .= '"'.$ke.'",';
    }
    $str0 = rtrim($str0,',');
    echo $str0;
?>];
var currtblidx = 0;
var dbnm = '<?php echo $idxdbname; ?>';
window.dbcnx = function(){ 
    if(!window.db){
        console.log("connecting to db"); 
        var dbExists = true;
        var request = window.indexedDB.open("<?php echo $idxdbname; ?>");
        request.onupgradeneeded = function (e){
            console.log('need database');
            e.target.transaction.abort();
            dbExists = false;
        }
        request.onerror = function(){
            alert('no client database');
            setTimeout("window.location = '<?php echo $hostroot; ?>login/?err="+base64Encode('no client storage')+"'",1000);
        }

        request.onsuccess = function(e) {
            console.log("connected to <?php echo $idxdbname; ?>");
            window.db = e.target.result; 
            for(var q in db.objectStoreNames){
                var sig = db.objectStoreNames[q];
                if (typeof sig=="string"){
                    window.DB[db.objectStoreNames[q]] = [];
                    var c = db.transaction(db.objectStoreNames[q]).objectStore(db.objectStoreNames[q]).openCursor();
                    c.onsuccess = function(evt){
                          var cursor = evt.target.result;
                          if (cursor) {
                            window.DB[cursor.source.name].push(cursor.value);
                            for(var field in cursor.value) {
                                //console.log(cursor.source.name+" : "+field);
                                //console.log(cursor.value[field]);
                                //console.log(field);
                            }
                            cursor.continue();
                          }else {
                            currtblidx++;
                            //console.log("No more entries!");
                            //console.log("end "+dbtables.length+" "+currtblidx);
                            if(dbtables.length == currtblidx){
                                for(var i in DB){
                                    //console.log(i);
                                }
                                window.uid = DB.user[0].id;
                            }
                          }
                    }
                    
                    c.onerror = function () {
                        console.log("Couldn't load database");
                    };
                }
            }    
        }
    }   
}

 </script>
 </body>
 </html>
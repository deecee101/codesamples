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
//exit(session_id());
include_once("db.php"); //exit($loginurl);

$b64sid = base64_encode(session_id());
$errormsg = '';
if(isset($_GET['login']) || isset($_GET['logdin'])){
    $err['flag'] = NULL;
    if(validate($_GET['uid'], 'alnum') == false){$err['flag']='something was wrong with the user id';};
    if($err['flag'] != NULL){
        logerror($err['flag']);
        header('location:'.$hpath.'clear/'); exit;
    }
    if(isset($_GET['logdin'])){
        if(validate($_GET['logdin'], 'loose') == false){$err['flag']='something was wrong with the token';};
        if($err['flag'] != NULL){
            logerror($err['flag']);
            header('location:'.$hpath.'clear/'); exit;
        }
        $myQuery = "SELECT users.*, visitors.* FROM users,visitors WHERE users.id = '{$_GET['uid']}' AND visitors.token = '{$_GET['logdin']}' and users.id = visitors.userid";
        logerror($myQuery);
        try{
            $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
            if($result['id'] == NULL || $result['id'] == ""){
                logerror('no matching user id for logdin');
                header('location:'.$hpath.'clear/'); exit;
            }else{$member = $result;$member['id'] = $result['userid'];$_SESSION['user']=$member;}
        }
        catch(PDOException $e){
             logerror($e->getMessage());
             header('location:'.$hpath.'clear/'); exit;
        }
    }
    $clientToken = '';
    if(isset($_GET['login'])){$clientToken=$_GET['login'];}else if(isset($_GET['logdin'])){$clientToken=$_GET['logdin'];}
    if(!isset($_SESSION['user']['id']) && isset($_GET['login'])){/*logerror('no login cred to authenticate');*/ header('location:'.$hpath.'clear/'); exit;}
    
    $myQuery = "SELECT * FROM visitors WHERE userid = '{$_GET['uid']}' AND token<>'' ORDER BY timedate DESC LIMIT 0,1";
    logerror($myQuery.' login.php');
    try{
        $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
        if($result['id'] == NULL || $result['id'] == ""){
            logerror('no user matching session');
            header('location:'.$hpath.'clear/'); exit;
        }else{
             $previousvisit = $result;
             if($previousvisit['token'] == $clientToken){
                $_SESSION['user']['token'] = $clientToken;
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <link href="<?php echo $hostroot; ?>logo.png" rel="icon" type="image/x-icon" /> 
                    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
                    <title>Codesamples :: LOGGING IN....</title>
                    <script type="text/javascript" src="<?=$hpath; ?>js/functions.js" ></script>
                
                </head>
                <body onload="dbcnx()" style="text-align:center">
                    <div id="msg" style="display:inline-block;position:fixed;top:45%;margin:0px auto" ></div>
                <script type="text/javascript">
                window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
                window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"}; // This line should only be needed if it is needed to support the object's constants for older browsers
                window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
                if (!window.indexedDB) {
                    document.getElementById("msg").innerHTML = "Your browser doesn't support a stable version of Storage. You cannot login to the website without Storage.";
                    setTimeout("window.location = '<?=$hpath; ?>login?err="+base64Encode('no browser storage support')+"'",5000);
                }else{
                    window.db; //indexedb object
                    window.DB = []; //window objectt to hold indexeddb data
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
                window.dbcnx = function(){<?php 
                if(isset($_GET['login'])){ ?>
                    if(!window.db){
                        console.log("connecting to db"); 
                        //this connects to the database if it exists. if i does not exist, it throws an error
                        var request = window.indexedDB.open(dbnm, 1);
                        request.onupgradeneeded = function(e) {
                            console.log("running onupgradeneeded");
                            window.db = e.target.result; 
                            <?php foreach($dbtables as $ke => $val){ ?>
                                var <?php echo $ke; ?>_objStore = db.createObjectStore("<?php echo $ke; ?>", { <?php
                                if(strstr(strtolower($val['id']),'auto')){ ?> keyPath: "id", autoIncrement: true <?php }else{ ?> keyPath: "<?php echo $val['id']; ?>" <?php } ?> });  
                            <?php } ?>
                        }

                        request.onsuccess = function(e) {
                            console.log("connected to"+dbnm);
                            window.db = e.target.result; 
                            
                            db.transaction(['user'], 'readonly').objectStore('user').count().onsuccess = function(event) {
                                if(event.target.result === undefined || event.target.result == '' || event.target.result <= 0){
                                    startdb();
                                    return;
                                }else{
                                    db.transaction('user').objectStore('user').get('<?php echo $_GET['uid']; ?>').onsuccess = function(event) {                                  
                                        var data = event.target.result; 
                                        if(data.token == ''){
                                            data.token = '<?=$_GET['login']; ?>';
                                            var dataObjectStore = db.transaction(['user'], "readwrite").objectStore('user');
                                            var requestUpdate = dataObjectStore.put(data); 
                                        }
                                    }
                                    document.getElementById("msg").innerHTML = 'user is verified member. redirecting...';
                                    setTimeout("window.location = '<?=$hpath; ?>'",500);
                                }
                            }
                        }

                        request.onerror = function(e) {
                            document.getElementById("msg").innerHTML = 'ERROR : '+e;
                            setTimeout("window.location = '<?=$hpath; ?>login?err="+base64Encode('cannot match user credentials with member')+"'",500);
                        }
                    } 
                <?php }else if(isset($_GET['logdin'])){ ?>
                    if(!window.db){
                        console.log("connecting to db"); 
                        //this connects to the database if it exists. if i does not exist, it throws an error
                        var request = window.indexedDB.open("<?php echo $idxdbname; ?>", 1);
                        var dbnm = "<?php echo $idxdbname; ?>";
                        var dbExists = true;
                        request.onupgradeneeded = function (e){
                            console.log('need database');
                            e.target.transaction.abort();
                            dbExists = false;
                        }
                        request.onsuccess = function(e) {
                            console.log("connected to"+dbnm);
                            window.db = e.target.result; 
                            db.transaction('user').objectStore('user').get('<?php echo $member['userid']; ?>').onsuccess = function(event) {                                  
                                var data = event.target.result; 
                                if(event.target.result === undefined || event.target.result == '' || event.target.result <= 0){
                                    //console.log('no users table');
                                    document.getElementById("msg").innerHTML = "no user table";
                                    setTimeout("window.location = '<?=$hpath; ?>clear/'",500);
                                }else{
                                    /////!!!!!!!!!!!!THIS IS LOGIN!!!!!!!!!!!!!!!!!!!!!
                                    document.getElementById("msg").innerHTML = "logging in...";
                                    setTimeout("window.location = '<?=$hpath; ?>'",500);
                                }
                            }    
                        } 
                        request.onerror = function(e) {
                            document.getElementById("msg").innerHTML = "Error no database "+e;
                            setTimeout("window.location = '<?=$hpath; ?>clear/'",500);
                        }
                    } 
                <?php }else{
                    header('location:'.$hpath.'clear');
                } ?>
                }
                <?php if(isset($_GET['login'])){ ?>
                window.startdb = function(){ 

                    var userObjectStore = db.transaction("user", "readwrite").objectStore("user");
                    userObjectStore.add({<?php 
                        echo '"token":"'.$_GET['login'].'",';
                        foreach($_SESSION['user'] as $ke => $val){
                            if($ke == 'pword' || $ke == 'secretword'){continue;}
                            $val = preg_replace( "#\n|\r|\r\n#", '', $val);
                            echo '"'.$ke.'":"'.$val.'",';
                        }
                        ?>});
                    console.log('startdb window location : '+replaceCharacters(window.location.href,'dologin','member'));
                    window.location = replaceCharacters(window.location.href,'dologin','member');
                } 
                <?php } ?>
                </script></body></html>
<?php        }else{
                logerror("user match, token mismatch : ".$previousvisit['token'].' != '.$clientToken);
                $_myQuery = "REPLACE INTO visitors(page,userid,token) VALUES ('".$_SERVER['PHP_SELF']."?".urldecode($_SERVER['QUERY_STRING'])."','".$_GET['uid']."','')";
                logerror($_myQuery.' login.php');
                    try{
                         $_result = $db->query($_myQuery);
                    }
                    catch(PDOException $e){
                         logerror($e->getMessage());
                    }
                header('location:'.$hpath.'clear/'); exit;
            }
        }
    }
    catch(PDOException $e){
         logerror($e->getMessage());
         header('location:'.$hpath.'clear/'); exit;
    }
    exit();
}
if(isset($_POST['axn'])){

    switch($_POST['axn']){
        case "dologin":
            $err['flag'] = NULL;
            $_POST['pword'] = base64_decode($_POST['pword']);
            $_POST['uname'] = base64_decode($_POST['uname']);
            $_POST['sessionid'] = base64_decode($_POST['sid']);
            if(validate($_POST['uname'], 'uname') == false){$err['flag']='something was wrong with the username. please try again uname regex';};
            if(validate($_POST['pword'], 'loose') == false){$err['flag']='something was wrong with the password. please try again pword regex';};
            if(validate($_POST['sessionid'], 'alnum') == false){$err['flag']='something was wrong, please try again. session regex';};
            if($err['flag'] != NULL){
                logerror($err['flag']);
                echo '{"status":"error","error":"'.$err['flag'].'","loginerror":"bad post value"}'; exit;
            }
            if(session_id() != $_POST['sessionid']){
                logerror('session id : '.session_id().' != '.$_POST['sessionid']);
                exit('{"status":"error","error":"something was wrong-, please try again","loginerror":"sessionid"}');
            }
            $myQuery = "SELECT * FROM users WHERE uname = '".strtolower($_POST['uname'])."' AND pword = '".crypt_apr1_md5($_POST['pword'],'s4lt')."'";
            //logerror($myQuery);
            try{
                 $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);

                if($result['id'] == NULL || $result['id'] == ""){
                    echo '{"status":"error","error":"no matching user ","loginerror":"no matching user"}'; exit;
                 }else{
                    $_SESSION['user'] = $result; unset($_SESSION['user']['pword']);unset($_SESSION['user']['pinpass']);
                    $_SESSION['user']['role'] = 'user';
                    $token = base64_encode(session_id().$_POST['pword']);
                    $_myQuery = "REPLACE INTO visitors(page,userid,token) VALUES ('".$_SERVER['PHP_SELF']."?".urldecode($_SERVER['QUERY_STRING'])."','".$_SESSION['user']['id']."','".$token."')";
                    logerror($_myQuery.' login.php dologin');
                        try{
                             $_result = $db->query($_myQuery);
                        }
                        catch(PDOException $e){
                             exit($e->getMessage());
                        }
                    echo '{"status":"ok","function":"dologin","url":"'.urlencode($token).'/dologin/'.$_SESSION['user']['id'].'"}'; exit;
                 }
            }
            catch(PDOException $e){
                 echo '{"status":"error","error":"'.$e->getMessage().'","loginerror":"sql error"}'; exit;
            }

            break;
        case "unset":
            session_unset();
            break;
        default:
            echo '{"status":"error","error":"'.$_POST['axn'].'"}'; exit;
    }
    exit;
}
if(isset($_GET['err'])){
    include_once($spath.'php/functions.php');
    $err['flag'] = NULL;
    $_GET['err'] = base64_decode($_GET['err'] );
    if(validate($_GET['err'], 'loose') == false){$err['flag']='bad error url variable';};
    if($err['flag'] != NULL){
        $errormsg = '';
    }else{
        $errormsg = $_GET['err'];
    }
}

?>
<html><head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="<?php echo $hostroot; ?>logo.png" rel="icon" type="image/x-icon" /> 
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
<title>Codesamples :: LOGIN</title>
<script type="text/javascript" src="<?=$hpath; ?>js/functions.js" ></script>
<link rel="stylesheet" type="text/css" href="<?=$hpath; ?>ui/buttons.css">
</head>
<body style="display:flex;flex-flow:column wrap;justify-content: center;align-items:center" >
        <div style="display:block;width:100%;text-align:center;min-width:310px;" ><img src="<?=$hostroot; ?>logo.png" style="width:25%;min-width:200px;max-width:380px" ></div>
        <DIV  STYLE="padding:5px;margin:0px 5px;background:rgba(212,212,212,0.5);border-radius:10px;margin:0px auto;display:inline-block;position:relative" >
            <strong style="color:red" ><?php echo $errormsg; ?></strong>
            <FORM NAME="loginform" ID="loginform" METHOD="POST"  enctype='multipart/form-data' ACTION="<?php echo $self; ?>" TARGET="_self" >
            <DIV STYLE="display:inline-block;" >username:<BR /><input type="text" name="uname" id="uname" placeholder="enter username" ></DIV>
            <DIV STYLE="display:inline-block;" >password:<BR /><input type="password" name="pword" id="pword" placeholder="enter password" ></DIV>
            <INPUT TYPE="hidden" NAME="axn" ID="axn" VALUE="login" ><br>
            <span class="button button-pill button-action" onclick="dologin()" class="button button-primary button-raised button-circle button-longshadow" style="margin:3px;float:right" >Login</span>
            </FORM>
        </DIV>
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
    }
    request.onsuccess = function(e){
        console.log('has db');
        window.db = e.target.result;
        getuser(); 
    }
}
var DB = [];
DB['user'] = [];
window.getuser = function(){
    if(!db.objectStoreNames.contains('user')){console.log("objectstore not found");setTimeout("deletedb()",400);return;}
    db.transaction(['user'], 'readonly').objectStore('user').count().onsuccess = function(e) {/*console.log(e.target.result+' records');*/ }
    var objectStore = db.transaction('user').objectStore('user');
    objectStore.openCursor().onsuccess = function(event) {
        var cursor = event.target.result;
        console.log(cursor.value.id);
        var re = db.transaction('user').objectStore('user').get(cursor.value.id);
        re.onsuccess = function(evt) {
            console.log(evt.target.result);
            if(evt.target.result['token'] != null && evt.target.result['token'] != ''){
                console.log('has a token');
                console.log('<?=$hpath; ?>'+evt.target.result['token']+'/member/'+evt.target.result['id']);
                window.location = '<?=$hpath; ?>'+evt.target.result['token']+'/member/'+evt.target.result['id'];
            }else{
              console.log('does NOT have a token');
              document.getElementById("uname").value = evt.target.result['uname'];
            }
            return true;
        };
        re.onerror = function(e){
            console.log('no match');
            return false;
        }
    };          
} 
window.dologin = function(){
    fetch('<?php echo $self; ?>', {
        method: 'post',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
          },
        body: 'axn=dologin&sid=<?php echo $b64sid; ?>&pword='+base64Encode(document.getElementById("pword").value)+'&uname='+base64Encode(document.getElementById("uname").value)
    }).then(function(response3){
        return response3.json();
    }).then(function(response4){
        if(response4.status == 'ok'){
            window.location = '<?=$hpath; ?>'+response4.url;
        }else{
            showflag(response4.error);
            if(response4.loginerror == 'sessionid'){
                DB['user']['token'] = null;
                var dataObjectStore = db.transaction(['user'], "readwrite").objectStore('user');
                var requestUpdate = dataObjectStore.put(DB['user']);
                document.getElementById("uname").value = evt.target.result['uname'];
            }
        }
    }).catch(function(e) {
        showflag(e);
    });
}
document.getElementById("pword").addEventListener("keyup", event => {
    if (event.keyCode === 13) {
        dologin();
    }
});
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
</body></html>
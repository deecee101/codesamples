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
$aspkey = '00000';
$pkey = '00000';
 include_once('db.php');




if(count($_POST) == 0){
	$_POST = json_decode(file_get_contents('php://input'), true); //for php 7
}
if(isset($_POST['axn']) && $_POST['axn'] != NULL){
	switch($_POST['axn']){
		case "setpushdata":
			$_POST['subject'] = str_replace('<','',$_POST['subject']); $_POST['subject'] = str_replace('>','',$_POST['subject']);
			if(validate($_POST['subject'], 'loose') == false){$err['flag']='the subject was bad. please try again';};
			if(validate($_POST['browser_key'], 'loose') == false){$err['flag']='the browser key was bad. please try again';};
			if(validate($_POST['server_key'], 'loose') == false){$err['flag']='the server key was bad. please try again';};
			if($err['flag'] != NULL){
				?><script>alert('<?=$err['flag']; ?>');</script><?php   exit;
			}

			$my_query = "REPLACE INTO apis (subject,browser_key,server_key) VALUES (".$db->quote($_POST['subject']).",".$db->quote($_POST['browser_key']).",".$db->quote($_POST['server_key']).")";
			
			try {
			    $db->exec($my_query);
			    ?><script type="text/javascript">window.top.location='<?=$self; ?>';</script><?php
			    }
			catch(PDOException $e)
			    {
			    echo $e->getMessage();
			    }
			exit;
			break;
		case "subscribe":
			//filter out bad data
			$err['flag'] = NULL;
			if(validate($_POST['endpoint'], 'url') == false){$err['flag']='the endpoint was bad. please try again';};
			if(validate($_POST['key'], 'loose') == false){$err['flag']='the key was bad. please try again';};
			if(validate($_POST['token'], 'loose') == false){$err['flag']='the token was bad. please try again';};
			if(validate($_POST['uid'], 'num') == false){$err['flag']='the user id was bad. please try again';};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			$myQuery = "SELECT * FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
			try{
			    $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
			    if($result == false || $result['id'] == NULL || $result['id'] == ""){
					$my_query = "REPLACE INTO subscribers (id,endpoint, p256dh, auth) VALUES (".$_POST['uid'].",".$db->quote($_POST['endpoint']).", ".$db->quote($_POST['key']).", ".$db->quote($_POST['token'])."); ";
					    //echo $my_query.'<BR><BR>';
					    try {
					        $db->exec($my_query);
					        
					        }
					    catch(PDOException $e)
					        {
					        echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
					        }
					    //$i++;
					    //$output .= 'adding user <br>';
		   	    }else{
			        //echo '{"status":"ok","function":"user exists in db"}'; exit;
			    }
			    $myQuery = "UPDATE users SET notifications_subscription = 'on' WHERE id = '{$_POST['uid']}'";
			    	try{
			    		 $result = $db->query($myQuery);
			    		 echo '{"status":"ok","function":"turned on user notifications"}'; exit;
			    	}
			    	catch(PDOException $e){
						echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			    	}
			}
			catch(PDOException $e){
				echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			}
			break;
		case "unsubscribe":
			$err['flag'] = NULL;
			if(validate($_POST['endpoint'], 'loose') == false){$err['flag']='the endpoint was bad. please try again';};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			$myQuery = "SELECT * FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
			try{
			    $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
			    if($result['id'] != NULL){
			    	$rid = $result['id'];
					$my_query = "DELETE FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
					   // exit($my_query);
					    try {
					        $db->exec($my_query);
					        //echo '{"status":"ok","function":"removed user"}';  
					        }
					    catch(PDOException $e)
					        {
					        echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
					        }
					$my_query = "UPDATE users SET notifications_subscription = 'off' WHERE id = '$rid'";
					   // exit($my_query);
					    try {
					        $db->exec($my_query);
					        echo '{"status":"ok","function":"turned off notifications for this user"}';  exit;
					        }
					    catch(PDOException $e)
					        {
					        echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
					        }
		   	    }else{
			        echo '{"status":"ok","function":"user is not in subscribers list"}';  exit;
			    }
			}
			catch(PDOException $e){
				echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
			} 
			break;
		default:
	}
}

$myQuery = "SELECT browser_key,server_key,subject FROM apis";
try{
     $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
    if($result == false || $result['browser_key'] == NULL || $result['server_key'] == NULL || $result['subject'] == NULL ){
		?>
		<html><head><title>Codesamples Push Notifications</title>
			<link rel="stylesheet" type="text/css" href="<?=$hpath; ?>css.css">
		</head>
		<body>
			<h2>Please enter in your credentials</h2><br>
			<div style="display:block;font-size:small" >credentials can be generated at <a href="https://vapidkeys.com/" target="_blank" >vapid keys.com</a></div>
			<FORM NAME="pushdataform" ID="pushdataform" METHOD="POST"  enctype='multipart/form-data' ACTION="<?=$self; ?>" TARGET="processor" >
			<DIV STYLE="display:inline-block;margin:5px" >subject:<BR /><input type="text" name="subject" id="subject" placeholder="enter subject" ></DIV>
			<DIV STYLE="display:inline-block;margin:5px" >public key:<BR /><input type="text" name="browser_key" id="browser_key" placeholder="enter public key" ></DIV>
			<DIV STYLE="display:inline-block;margin:5px" >private key:<BR /><input type="text" name="server_key" id="server_key" placeholder="enter private key" ></DIV>
			<INPUT TYPE="hidden" NAME="axn" ID="axn" VALUE="setpushdata" >
			<input type="submit" ></input>
			</FORM>
			<IFRAME SRC="<?=$hostroot; ?>blank.html" NAME="processor" ID="processor" STYLE="width:0px;height:0px;position:absolute;top:0px; left:0px" FRAMEBORDER="no" allowtransparency="yes" ></IFRAME>
		</body></html>
		<?php
		   exit;
     }else{
         $apis = $result;
         $_SESSION['apis']['push'] = $result;
     }
}
catch(PDOException $e){
     echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
     ?><SCRIPT>alert('<?php echo $e->getMessage(); ?>');</SCRIPT><?php  exit;
}
$aspkey = $apis['browser_key'];
$pkey = $apis['server_key'];

?><html><head><title>Push Notification Test</title>
	<link rel="stylesheet" type="text/css" href="<?=$hpath; ?>css.css">
	<link rel="stylesheet" type="text/css" href="<?=$hpath; ?>ui/buttons.css">
	<script>
		var aspkey = '<?php echo $aspkey; ?>';
		var uid = '<?=$_SESSION['user']['id']; ?>';
	</script>
<style type="text/css">
 /* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
} 
</style>
</head>
<body>
	<h1>Push Notification Code Sample</h1>
	<?php 
	if($ub == 'safari'){exit('sorry friend, safari does not support web push notifications');}
	?>
	<div style="font-size: small;display:block;width:100%" >visit this page in a browser, or on a mobile device to subscribe to push notifications<BR>
	<a href="<?=$hpath; ?>sendpush/" class="button button-inverse button-pill button-small" >CLICK HERE</a> to visit the page to SEND a push notification<br>
	<br><a href="<?=$hpath; ?>dbmgr.php" class="button button-inverse button-pill button-small" >CLICK HERE</a> to review the database, login is <strong style="color:red" >admin</strong><br>

	</div>
	subscribe to push notifications in this browser :<br>
<label id="notifications_susbcription_toggle_btn" class="switch" >
  <input type="checkbox" id="notifications_susbcription_toggle_checkbox">
  <span class="slider round"></span>
</label><br>
<span id="notifications_susbcription_status_label" ></span>
	<script type="text/javascript" src="./js/push.js"></script>
<script type="text/javascript">
if ('serviceWorker' in navigator && 'PushManager' in window) {
  console.log('Service Worker and Push is supported');
  //mysw.js has the push method and payload, mysw.js also has the eventhandler fr when the notification is clicked
  navigator.serviceWorker.register('<?php echo $hpath; ?>sw.js') // <---!!!!!! NO EXCEPTIONS!!!! this MUST be in the same directory as index.php
  .then(function(swReg) {
    console.log('Service Worker is registered', swReg);
    swRegistration = swReg;
    initialiseUI();
  })
  .catch(function(error) {
    console.error('Service Worker Error', error);
  });
} else {
  console.warn('Push messaging is not supported');
  pushButton.textContent = 'Push Not Supported';
}
</script>
</body></html>
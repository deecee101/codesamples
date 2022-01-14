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
echo $docurl.'<br>'; exit;
*/
$sitename = preg_replace('#\W+#', ' ',$_SERVER['HTTP_HOST']);
include_once($spath."db.php");
include_once($spath."php/functions.php");

$myQuery = "SELECT browser_key,server_key,subject FROM apis";


try{
     $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
    if($result == false || $result['browser_key'] == NULL || $result['server_key'] == NULL || $result['subject'] == NULL ){
           exit('no push credentials');
     }else{
         $apis = $result;
         $_SESSION['apis']['push'] = $result;
     }
}
catch(PDOException $e){
     echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
     ?><SCRIPT>alert('<?php echo $e->getMessage(); ?>');</SCRIPT><?php  exit;
}

require $spath. 'vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

if(count($_POST) == 0){
  $_POST = json_decode(file_get_contents('php://input'), true); //for php 7
}
if(isset($_POST['axn'])){
    switch($_POST['axn']){
        case "uplimg":
            $upldir = $spath.'imgs';
            @mkdir($upldir);
            $err = array('flag'=>'');
            if(count($_FILES) > 0){
                $addedfile = img_upld('file', $upldir.'/', 250, 250, true);;
                if($addedfile == false){
                    echo '{"status":"error","function":"upload notification image","file":"'.$upldir.'","error":"upload error. '.$err['flag'].'"}';
                    exit;
                }else{
                    $f = str_replace($servroot, $hostroot, $addedfile);
                    echo '{"status":"OK","function":"upload item icon","file":"'.$f.'"}';
                    exit;
                }
            }else{
                if(validate($_POST['imgurl'], 'url') == false){$err['flag']=$err['url'];};
                if($err['flag'] != NULL){
                       echo '{"status":"error","function":"get notification image","file":"'.$_POST['imgurl'].'","error":"network error"}'; exit;
                }
                $char = "0123456789abcdefghijklmnopqrstuvwxyz";
                $ulen = mt_rand(5, 10);
                $hash = '';
                for ($i = 1; $i <= $ulen; $i++) {
                    $hash .= substr($char, mt_rand(0, strlen($char)), 1);
                }
                $ext = strtolower(substr(strrchr($_POST['imgurl'],"."),1));
                copy($_POST['imgurl'], $upldir.'/'.$hash.'_.'.$ext);
                if(!is_image($upldir.'/'.$hash.'_.'.$ext)){
                    unlink($upldir.'/'.$hash.'_.'.$ext);
                    echo '{"status":"error","function":"get image from url","file":"'.$upldir.'/'.$hash.'.'.$ext.'"}';
                    exit;
                }else{
                    createthumb($upldir.'/'.$hash.'_.'.$ext,$upldir.'/'.$hash.'.'.$ext,250,250);
                    unlink($upldir.'/'.$hash.'_.'.$ext);
                    $f = $upldir.'/'.$hash.'.'.$ext;
                    echo '{"status":"OK","function":"copy post icon","file":"'.str_replace($servroot,$hostroot,$f).'"}';
                    exit;
                }
            } 
            break;
        case "sendcomm":
            $err['flag'] = NULL;
            
            if(validate($_POST['uid'], 'num') == false){$err['flag']="bad user id : ".$_POST['uid'];}
            if(validate($_POST['subject'], 'loose') == false){$err['flag']='something was wrong with the subject';};
            if(validate($_POST['url'], 'url') == false){$err['flag']='somethng was wrong with the url';}
            if(validate($_POST['badge'], 'fileurl') == false){$err['flag']='something was wrong with the post badge';};
            if(validate($_POST['icon'], 'fileurl') == false){$err['flag']='something was wrong with the post icon';};
            if($err['flag'] != NULL){
                echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
            }
            
            $_POST['body'] = preg_replace("/[^a-zA-Z0-9 ,\. ' \" \$ !@#%& \* \- \+ = \? ; :]/", "", $_POST['body']);
            $_POST['body'] = truncate($_POST['body'], 240, ' ', '...');
            $_POST['subject'] = truncate($_POST['subject'], 65, ' ', '...');
            $myQuery = "SELECT * FROM subscribers WHERE id = '{$_POST['uid']}'";

            try{
                 $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
                if($result['id'] == NULL || $result['id'] == ""){
                    echo '{"status":"error","error":"no users matching the uid"}';  exit;
                 }else{
                   $subscriberdata = $result;
                   $subscriberdata['publicKey'] = str_replace(['_', '-'], ['/', '+'],$result['p256dh']);
                   $subscriberdata['authToken'] = str_replace(['_', '-'], ['/', '+'],$result['auth']);
                   $subscriberdata['contentEncoding'] = 'aesgcm'; // one of PushManager.supportedContentEncodings
                }
            }
            catch(PDOException $e){
                echo '{"status":"error","error":"'.$e->getMessage().'"}';  exit;
            }
            $subscription = Subscription::create($subscriberdata, true);

            $auth = array(
                'VAPID' => array(
                    'subject' => $_SESSION['apis']['push']['subject'],
                    'publicKey' => $_SESSION['apis']['push']['browser_key'],
                    'privateKey' => $_SESSION['apis']['push']['server_key'], // in the real world, this would be in a secret file
                ),
            );
            $webPush = new WebPush($auth);
            //this code was modified from the tutorial to make it more dynamic.
            //hardcoding the serviceworker push notification would not be a great practice in a real-world application
            $icon = $_POST['icon'];
            $badge = $_POST['badge'];

            $res = $webPush->sendNotification(
                $subscription,
                '{"status":"ok","title":"'.$_POST['subject'].'","msg":"'.$_POST['body'].'","icon":"'.$icon.'","badge":"'.$badge.'","url":"'.rtrim($_POST['url'],'/').'"}'
            );
            $msg = '';
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();

                if ($report->isSuccess()) {
                    echo '{"status":"ok","function":"sent notification to '.$_POST['uid'].'"}';  exit;
                } else {
                    echo '{"status":"error","error":"Message failed to sent for subscription '.$_POST['uid'].'. '.$report->getReason().'"}';  exit;
                }
            }
            break;
        default:
    } exit;
    }else if(isset($_GET['axn'])){
    switch($_GET['axn']){
        default:
    } exit;
}
?>
<html><head><title></title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
    <link rel="stylesheet" type="text/css" href="<?php echo $hpath; ?>ui/buttons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $hpath; ?>css.css">
    <LINK href="<?php echo $hpath; ?>ui/fonticons/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
    <script src="<?php echo $hpath; ?>js/functions.js" ></script> 
    <script src="<?php echo $hpath; ?>js/jquery.js" ></script>
</head>
<body style="display:flex;flex-flow:column nowrap;justify-content:flex-start;align-items:center">
<a  style="border: none;position:static;top:10px;left:0px;width:100%;display:block;text-align:center;z-index:0" ><img src="<?=$hpath.'logo.png';?>" style="margin:0px auto;" /></a>
<div id="contact" style="display:block;margin:0px 10px">
    <FORM NAME="notificationform" ID="notificationform" METHOD="POST" enctype="multipart/form-data"  ACTION="<?=$self; ?>" TARGET="processor" style="width:90vw;min-width:310px;text-align:left" >
        <span style="font-size:x-small;color:red" >*needs a transparent bw png 192x192 in root directory : notification_icon.png</span><br>
        <input type="text"  placeholder="Subject" name="subject" id="subject" value="notification from <?=$sitename; ?>" /><span style="font-size:x-small;color:#B6B6B6" > subject</span><br>
        <input type="text" placeholder="http://..." name="url" id="url" value="<?php echo $hostroot; ?>" /><span style="font-size:x-small;color:#B6B6B6" > link</span><br>
        <DIV STYLE="display:inline-block" ><IMG SRC="<?php echo $hostroot; ?>logo.png" id="avtimg"  align="left"  STYLE="margin:5px;width:150px" />
            <INPUT TYPE="file" NAME="img" ID="img" onchange="getimg(this)" /><BR />Upload an image
        </DIV><br>
        <input  name="avturl" id="avturl" type="text" placeholder="enter an url of an image here" size="50" onchange="getimg(this)" value=""  ><span class="button button-small button-rounded button-primary" >get image</span><br> 
        
        <span style="font-size:x-small;color:#B6B6B6" >body</span><br>
        <textarea name="note" id="note" style="width:90%;min-width:310px" ><?=randomwords(); ?></textarea>
        <INPUT TYPE="hidden" ID="badge" NAME="badge" VALUE="<?=$hpath; ?>badge.png"  />
        <INPUT TYPE="hidden" ID="icon" NAME="icon" VALUE="<?=$hpath; ?>notification_icon.png"  />
        <INPUT TYPE="hidden" ID="axn" NAME="axn" VALUE="notificate"  />
    </FORM>
</div>
<div style="display:block;min-width:310px;text-align:left;border:solid thin gray" >
    <span style="font-size:small;display:block;width:100%" >click the checkbox of each user to send a notification</span>
    <div style="float:right;margin-right:10vh" ><span class="fa fa-plus-square" onclick="selectall('email')"  ></span> <span class="fa fa-minus-square" onclick="selectnone('email')"  ></span></div><br>
        <div style="display:block;border-top:solid thin black;border-bottom:solid thin black;max-height:90vh;overflow-y:scroll" >
            <?php 
            $myQuery = "SELECT * FROM subscribers WHERE NOT id = '1'";
                try{
                     $result = $db->query($myQuery);
                }
                catch(PDOException $e){
                     exit($e->getMessage());
                }
                $s=0;
                while($results = $result->fetch(PDO::FETCH_ASSOC)){ 
                    $s++; if($s%2 == 0){$eo = 'even'; }else{$eo = 'odd';}
                ?>
                    <div class="<?php echo $eo ?>" style="display:flex;flex-flow:row;justify-content:space-between;align-items:stretch;width:95%" >
                        <span style="display:inline-block" ><?php echo $results['id']; ?> </span>
                        <label class="container">
                          <input type="checkbox" class="chkbx" name="chkbx[<?php echo $results['id']; ?>]" id="chkbx_<?php echo $results['id']; ?>" data-uid="<?php echo $results['id']; ?>">
                          <span class="checkmark" style="border:solid thin black" ></span>
                        </label>
                    </div>
                <?php }
            ?>
        </div>
        <div style="display:block;text-align:right" ><span class="button button-royal"   onclick="sendout()" >SEND</span></div>
        <span style="display:flex;flex-flow:column;justify-content:flex-start;align-items:flex-start;min-height:200px;margin:7px;border:dotted thin gray;" id="output" >
            
        </span>
    </div>
</div>
<script type="text/javascript">
window.selectall = function(lst){
    if(lst == 'email'){
        var x = document.getElementsByClassName("chkbx");
        var i;
        for (i = 0; i < x.length; i++) {
            x[i].setAttribute("checked","checked");
        } 
    }
}
window.selectnone = function(lst){
    if(lst == 'email'){
        var x = document.getElementsByClassName("chkbx");
        var i;
        for (i = 0; i < x.length; i++) {
            x[i].removeAttribute("checked");
        } 
    }
}
window.sendout = function(){
    //get subject, link, icon, description, method of communication, list of recipients
    //perform a looping prmise through each recipients, adding a line to output
    //at the end, give option to email record of actions
    var subj = document.getElementById('subject').value;
    var url = document.getElementById('url').value;
    var note = document.getElementById('note').value;
    var mocomm = $("input[name=mocomm]:checked").val(); //console.log(mocomm); return;
    var users = [];
    var x = document.getElementsByClassName("chkbx");
    var i;
    for (i = 0; i < x.length; i++) {
      if (x[i].type == "checkbox" && x[i].checked == true) {
            users.push(x[i]);
      }
    }
    //console.log(subj,url,note,mocomm,users);
    var p = [];
    for(var u in users){
        if(typeof users[u] == 'function'){continue;}
        //var np = new Promise(function(resolve, reject) {
        var commobj = {"subject":subj,"url":url,"icon":document.getElementById("icon").value,"badge":document.getElementById("badge").value,"body":note,"uid":users[u].dataset.uid,"axn":"sendcomm","icon":document.getElementById("icon").value};
        var np = fetch('<?php echo $_SERVER['PHP_SELF'] ?>', {
            method: 'post',
            headers: {
                'Accept': 'application/json'
            },
            body: JSON.stringify(commobj)
        }).then(function(response3){
            return response3.json();
        }).then(function(response4){
            if(response4.status == 'ok'){
                //console.log(response4.status);
                $('<div/>',{
                    id: makehash(),
                    css: {display:"block",fontSize:"small"},
                    text:response4.function
                }).appendTo('#output');
            }else{
                alert(response4.error);
            }
        }).catch(function(e) {
            alert(e);     // p will be rejected when/if you get here
        });
        p.push(np);
    }
    Promise.all(p).then(function(results) {
        console.log('Promise Then: ', results);
    }).catch(function(err) {
        console.log('Promise Catch: ', err);
    });
}
    window.getimg = function(i){
      if(i.type == 'file'){
        // The Javascript
        var fileInput = document.getElementById('img');
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.set('axn','uplimg');
        formData.append('file', file);
      }
      if(i.type == 'text'){
        if(i.value == '' || i.value == null){return;}
        var formData = new FormData();
        formData.set('axn','uplimg');
        formData.append('imgurl', i.value);        
      }
      fetch('<?=$self; ?>', {
          method: 'post',
          headers: {
            'Accept': 'application/json'
            },
          body: formData
        }).then(function(response3){
          return response3.json();
        }).then(function(response4){
          if(response4.status != 'OK'){alert(response4.error); return;}
          console.log(response4); 
          document.getElementById('avtimg').style.display="inline";//document.getElementById('avticon').style.display="none";
          document.getElementById('avtimg').src=response4.file;
          console.log(document.getElementById('avtimg'));
          document.getElementById('icon').value=response4.file;
          return;
        }).catch(function(e) {
            console.log('error in post : 002 '); console.log(e); return;     // p will be rejected when/if you get here
        });
      
    } 


window.addEventListener('load', (event) => {

});
</script>
</body></html>
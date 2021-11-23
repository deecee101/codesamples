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
echo $docurl.'<br>'; exit;
*/
include_once($spath.'php/functions.php');

//create thumbnails if they dont exist
@mkdir($spath."thumbs/");
$path = realpath($spath.'imgs/');
$exts = array("jpg","jpeg","png","gif","webm");
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

$imgs = array();
foreach($objects as $file => $object){
	$file = str_replace('\\','/', $file);
	/*$e=explode('/', $file);
	$fname = $e[count($e)-1];
	$n = explode('.', $fname);
	$filename = $n[0];
	$ext = strtolower(substr(strrchr($fname,"."),1));*/
	$info = pathinfo($file);
	$fname = $info['basename'];
	$filename = $info['filename'];
	$ext = $info['extension'];
   	if($filename =='.' || $filename =='..' || $filename == '' || is_dir($file)){continue;}
   	if(!in_array($ext,$exts)){continue;}
	  if(!is_file($spath.'thumbs/'.$fname)){
	  	echo "<!-- create thumb for ".$spath.'imgs/'.$fname." --> \n" ;
	  	createthumb($file, $spath.'thumbs/',250,250);
	  }else{
	  	echo '<!-- '.$spath.'imgs/'.$fname." thumbnail already exists --> \n" ;
	  }

    array_push($imgs,str_replace($servroot,$hostroot,$file));
}
if(count($_POST) == 0){
  $_POST = json_decode(file_get_contents('php://input'), true); //for php 7
}
if(isset($_POST['axn'])){
	switch($_POST['axn']){
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
<script src="<?php echo $hpath; ?>js/lazyload.js" ></script> 
<style type="text/css">
.photoimg{
	width: 100%;
}
</style>

</head>
<body>
<?php 
foreach($imgs as $ke => $val){
	$e = explode('/',$val);
	$title = $e[count($e)-1];
	$title = substr($title, 0, strrpos($title, "."));
	$thumburl = str_replace('imgs','thumbs',$val);
	//display thumbnails
	echo "<img loading='lazy' alt='".$title." photo' class='lazy photoimg'  src='".$thumburl."' data-src='".$val."' /><br> \n";
}
?>
<script type="text/javascript">
window.addEventListener('load', (event) => {
	setTimeout('lazyload(document.querySelectorAll(".lazy"))',500); //for some odd reason localhost requires a delay or it autoloads all images immediately
    var elements = document.querySelectorAll('[lazy-load-background]');

    for (var i = 0; i < elements.length; i++) {
        //console.log(elements[i]);
        var element = elements[i];
        if (element.getBoundingClientRect().top < Screen.getViewportHeight()) {
            var background = element.getAttribute('lazy-load-background');
            element.style.backgroundImage = "url('" + background +"')";
            //console.log(background);
            element.removeAttribute('lazy-load-background');
            element.setAttribute('lazy-loaded-background', '');
        }else{
            //console.log(element.getBoundingClientRect().top+' > '+Screen.getViewportHeight());
        }
    }
});

var initVisibleBackgrounds = function(){
    var currentScroll = document.scrollingElement.scrollTop;
    var elements = document.querySelectorAll('[lazy-load-background]');

    for (var i = 0; i < elements.length; i++) {
        //console.log(elements[i]);
        var element = elements[i];
        if ((currentScroll > element.getBoundingClientRect().top - 150)) {
            var background = element.getAttribute('lazy-load-background');
            element.style.backgroundImage = "url('" + background +"')";
            //console.log(background);
            element.removeAttribute('lazy-load-background');
            element.setAttribute('lazy-loaded-background', '');
        }
    }
};
var elements = document.querySelectorAll('[lazy-load-background]');
window.addEventListener('scroll', function (e) {
    initVisibleBackgrounds();
}, false);
</script>
</body></html>
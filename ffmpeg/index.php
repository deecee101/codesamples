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
//shell_exec("ffmpeg -i ./videos/video1.mp4 -ss 00:00:03 -frames:v 1 foobar.jpeg");

$d = shell_exec("ffprobe -show_entries format=duration ./videos/video1.mp4");
$d = preg_replace( "#\n|\r|\r\n#", '', $d );
$duration = str_replace('[FORMAT]duration=','',$d);
$duration = str_replace('[/FORMAT]','',$duration);
$divided = intval($duration)/5; //exit($divided);
shell_exec("ffmpeg -i ./videos/video1.mp4 -vf fps=1/".$divided." -s 640x360 -f image2 ./thumbs/screenshot-%03d.jpg");

?>
<html><head><title></title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
</head>
<body>
This demo takes a video and uses FFMPEG to generate thumbnails.<br>
It is required to have FFMPEG and FFPROBE installed on a windows system and both commands assigned to the system path.<br>
video:<BR>
<video src="<?=$hpath; ?>videos/video1.mp4" style="width:310px" controls></video><br><br>
<?php 
$files = glob('./thumbs/*.{png,jpg,jpeg}', GLOB_BRACE);
foreach($files as $ke => $val){ ?>
<img src="<?=str_replace($servroot,$hostroot,$val); ?>"><br><br>
<?php } ?> 
<script type="text/javascript">
window.addEventListener('load', (event) => {

});
</script>
</body></html>
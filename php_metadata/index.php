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

?>
<html><head><title></title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
</head>
<body>
<h1>Metadata</h1>
this is a demo of using PHP to get metadata from an mp3 file.<br>
	<?php 
	include_once($spath.'php/getid3/getid3.php');
	$localtempfilename = $spath.'file.mp3';
	$getID3 = new getID3;
    $ThisFileInfo = $getID3->analyze($localtempfilename);
    $getID3->CopyTagsToComments($ThisFileInfo);
    //getid3_lib::CopyTagsToComments($ThisFileInfo);
    //
    $mp3image = '';
	if (isset($getID3->info['id3v2']['APIC'][0]['data'])) {
		$cover = $getID3->info['id3v2']['APIC'][0]['data'];
	} elseif (isset($getID3->info['id3v2']['PIC'][0]['data'])) {
		$cover = $getID3->info['id3v2']['PIC'][0]['data'];
	} else {
		$cover = null;
	}
	if (isset($getID3->info['id3v2']['APIC'][0]['image_mime'])) {
		$mimetype = $getID3->info['id3v2']['APIC'][0]['image_mime'];
	} else {
		$mimetype = 'image/jpeg'; // or null; depends on your needs
	}
	if (!is_null($cover)) {
		$imgstr = base64_encode($cover);
		$img = imagecreatefromstring($cover);
		if($img != false && !is_file('image.jpg'))
		{
		   imagejpeg($img, 'image.jpg');
		} 
		$mp3image=$imgstr;
	}    
echo '<img src="data:image/gif;base64,'.$mp3image.'" width="200" height="200" alt="embedded folder icon">'."\n";
echo '<br>'."\n";
echo "<audio src='file.mp3' controls autoplay='false' ></audio> \n";
echo "<br><br> \n";
    //var_dump($ThisFileInfo);
echo "\n artist:<strong>".$getID3->info['id3v2']['comments']['artist'][0]."</strong><br> \n";
echo "\n album:<strong>".$getID3->info['id3v2']['comments']['album'][0]."</strong><br> \n";
echo "\n tracknum:<strong>".$getID3->info['id3v2']['comments']['track_number'][0]."</strong><br> \n";
echo "\n year:<strong>".$getID3->info['id3v2']['comments']['year'][0]."</strong><br> \n";
echo "\n genre:<strong>".$getID3->info['id3v2']['comments']['genre'][0]."</strong><br> \n";
echo "data dump:<br> \n";
echo "<div style='display:block;width:100%;height:350px;overflow:auto;border:dotted thin gray;padding:3px;' > \n";
echo "<pre> \n";	
	var_dump($getID3->info['id3v2']);
echo "</pre>";
echo "</div> \n";
	 ?>
</body></html>
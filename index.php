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
<style type="text/css">
	blockquote{
		margin-top: 5px; margin-bottom: 5px;
	}
</style>
</head>
<body>
<div style="display:block;width:100%;text-align:center;min-width:310px;" ><img src="logo.png" style="width:25%;min-width:200px;max-width:380px" ></div>
<a href="./authorization/" target="_blank" >authorization</a>
<blockquote>
	Demo example of authentication using basic apache authentication, mySQL, and indexedDB.<BR>
	<strong style="color:red" >THE USERNAME IS : default <br> THE PASSWORD IS : default</strong>
</blockquote>
<a href="./background_slideshow/" target="_blank" >background slideshow</a><br>
<blockquote>
	Demo of dynamic image loading and stacking fade animation
</blockquote>
Database<BR>
<blockquote>
	Demo of 3 types of storage methods. Each one scans a folder of videos and populates a database with demo data.<br>
	Each one has examples of Create Read Update & Delete<br>
	<a href="./database/indexeddb/" target="_blank" >indexedDb</a><br>
  &nbsp;&nbsp;&nbsp;&nbsp;Client Side Storage.<br>
	<a href="./database/mysql/" target="_blank" >mySQL</a><br>
  &nbsp;&nbsp;&nbsp;&nbsp;The web standard.<br>
  &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;color:red" >in order to work, edit the file mySQL_AUTH.php to use your own mySQL root authentication credentials</span><br>
	<a href="./database/sqlite/" target="_blank" >sqlite</a><br>
  &nbsp;&nbsp;&nbsp;&nbsp;Really easy to implement but limited in use.<br>
</blockquote>
<a href="./ffmpeg/" target="_blank" >ffmpeg</a><br>
<blockquote>
	Demo of thumbnail generation from a video.<br>
	<strong style="color:red" >MUST HAVE FFMPEG & FFPROBE INSTALLED + ADDED TO THE <SPAN STYLE="text-decoration:underline" >WINDOWS</SPAN> SYSTEM PATH</strong>
</blockquote>
<a href="./geocoding/" target="_blank" >geocoding</a><br>
<blockquote>
	Demo of various location based services : <br>
	<blockquote>
		<strong style="text-decoration:underline" >Algolia/Places</strong> is an online service that enables an input text field to be a dynamic location search bar. Entering into the input field will suggest locations. Choosing a location will return information about the chosen location.<br>
		<strong style="text-decoration:underline" >Geolocation API</strong> is a function in most modern browsers to work with operating system location components. Allowing the browser to access the location of the user device.<br>
		<strong style="text-decoration:underline" >OpenStreetMap</strong> is a free wiki world map. In this case I send latitude + longitude information and the OpenStreetMap API will return an address, known as reverse geocoding<br>
		<strong style="text-decoration:underline" >Google Maps</strong> mysterious prototype location component that will eventually be integrated into skynet and threaten mankind in the future<br>
	</blockquote>
</blockquote>
<a href="./lazyloader/" target="_blank" >lazy load</a><br>
<blockquote>
	Demo lazy loading using javascript. This demo takes a set of images an creates low resolution thumbnails, then swaps out the thumbnail images for the high resolution images as the user scrolls<br>
	<span style="font-size:small" >to see proof of the effect, use the browser console to inspect the image element src during page scroll</span><br>
	<strong style="color:red" >This demo requires IMAGICK installed to the server, in order to generate the thumbnails</strong>
</blockquote>
<a href="./mainmenu/" target="_blank" >Main Menu</a><br>
<blockquote>
	Demo of a responsive Main Menu & scroller
</blockquote>
<a href="./php_metadata/" target="_blank" >PHP Metadata</a>
<blockquote>
	Demo which uses PHP to load and display the Metadata attached to an MP3 file	
</blockquote>
<a href="./serviceworker/" target="_blank" >Service Worker</a>
<blockquote>
	Demo of a page which will work offline once data is stored in the browser cache
</blockquote>
<a href="./svg/" target="_blank" >Snap SVG</a>
<blockquote>
	Demo of SVG images loaded into the browser, plus an animation effect
</blockquote>
<a href="./textanimation/" target="_blank" >Text Animation</a>
<blockquote>
	Demo of dynamic text animation effect
</blockquote>
<a href="./uploader/" target="_blank" >File Uploader</a>
<blockquote>
	Demo of a file uploader
</blockquote>
<a href="./weather/" target="_blank" >Weather Service API</a>
<blockquote>
	Demo of weather service
</blockquote>
<a href="./web_push/" target="_blank" >Web Push Notifications</a>
<blockquote>
	Demo of web push notifications
</blockquote>
<a href="./webcomponents/" target="_blank" >TODO List as a web component</a>
<blockquote>
	Demo of web push notifications
</blockquote>
<a href="./webworker/" target="_blank" >Color Effect utilizing web worker</a>
<blockquote>
	Demo of the utilization of a web worker to process data instead of interrupting the main thread
</blockquote>
<script type="text/javascript">
window.addEventListener('load', (event) => {

});
</script>
</body></html>
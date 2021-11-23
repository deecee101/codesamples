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
?>
<html><head><title></title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
</head>
<body>
<h1>Service Worker</h1>
This page is a demo of a simple 3 page, 7 image service worker.<br>
After each page is loaded once, the urls are all cached into the browser. It will then be possible to go offline and still view the pages and images<br>
<div style="display:flex;flex-flow:row wrap;justify-content:center;align-items:stretch;margin-top:15px" >
	<div id="navmenu" style="padding:10px;border-right:solid thin gray;text-align:right" >
		<a href="#" onclick="navto('./pages/1.html')" >One</a><br>
		<a href="#" onclick="navto('./pages/2.html')" >Two</a><br>
		<a href="#" onclick="navto('./pages/3.html')" >Three</a><br>

	</div>
	<div id="content" >hello world</div>
</div>
<script>
	function navto(pg){
		fetch(pg).then(function(response){
			return response.text();
		}).then(function(response){
			document.getElementById('content').innerHTML = response;
		}).catch(function() {
			/* error :( */
			console.log('error');
		});
	}
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('./sw.js').then(function(registration) {
      // Registration was successful
      console.log('ServiceWorker registration successful with scope: ', registration.scope);
    }, function(err) {
      // registration failed :(
      console.log('ServiceWorker registration failed: ', err);
    });
  });
}
</script>

</body>
</html>
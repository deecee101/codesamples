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
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Web Worker Colorfader</title>
<style type="text/css">
body, html {
    margin:0; padding:0; height:100%; width:100%;
    font-family:Tahoma, Geneva, sans-serif;
} 
</style>
</head>
<body style="display:flex;flex-flow:column wrap;justify-content:center;align-items:center" >
<DIV STYLE="position:fixed;top:0px;left:0px;width:100%;height:100%;z-index:1" ID="colors" ></DIV>
<H1 CLASS="title" STYLE="font-size:xx-large;position:relative;z-index:200;text-shadow: 2px 2px #000000;" ID="ttl" >Code Samples</H1>	
<span style="font-size:small;background:white;color:black;padding:7px;z-index:200;border-radius:8px" >this demo uses a web worker to color cycle through two elements, the background div and the text div</span>
	<script>
if (typeof(Worker) !== "undefined") {
    // Yes! Web worker support!
    // Some code.....
    if (typeof(w) == "undefined") {
	    wbg = new Worker("workers.js");
	    var tt0 = {"tone":5,"time":0.1};
		wbg.addEventListener('message', function(e) {
			//console.log(e);
		  document.getElementById('colors').style.backgroundColor = e.data;
		}, false);

		wbg.postMessage(tt0); // Send data to our worker.
		
		var tt1 = {"tone":1,"time":0.25};
		wbg2 = new Worker("workers.js");

		wbg2.addEventListener('message', function(e) {
		  document.getElementById('ttl').style.color = e.data;
		}, false);

		wbg2.postMessage(tt1); // Send data to our worker.

	}
} else {
    // Sorry! No Web Worker support..
}
// Select fade-effect below:
// Set 1 if the background may fade from dark to medium 
// Set 2 if the background may fade from light to medium 
// Set 3 if the background may fade from very dark to very light light
// Set 4 if the background may fade from light to very light
// Set 5 if the background may fade from dark to very dark 
	</script>
</body>
</html>
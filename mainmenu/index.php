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
echo $docurl.'<br>'; exit;*/
include_once('./php/functions.php');
?>
<html><head><title>Responsive Menu Test</title>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="<?php echo $hpath; ?>js/functions.js" ></script>
<script src="<?php echo $hpath; ?>js/jquery.js" ></script>
<script src="<?=$hpath; ?>js/gsap/gsap.min.js" ></script>  
<LINK href="<?php echo $hpath; ?>ui/fonticons/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<LINK href="<?php echo $hpath; ?>css.css" type="text/css" rel="stylesheet" />
<script src="<?php echo $hpath; ?>js/zenscroll-min.js" ></script> 
<style type="text/css">
	#mainmenudiv{
		display: flex;
		flex-flow: row wrap;
		justify-content: flex-end;
		width:100%;
		height:auto;
	}
	#mainmenudiv a{
		padding: 5px 7px;
	}
	#mainmenutoglbtn{display:none;}
	.mainmenuitem{
		cursor:pointer;
	}
	@media only screen and (min-width: 1212px) {
	}
	@media only screen and (min-width: 767px) and (max-width: 1212px) {
	}
	@media only screen and (max-width: 767px) and (min-width: 480px) {
		#mainmenudiv{
			justify-content: space-around;
			height:auto;
		}
	}
	@media only screen and (max-width: 479px) {
		#mainmenudiv{
			flex-direction: column;
			height:0px; overflow:hidden;
		}
		#mainmenutoglbtn{
			display:inline-block;
		}
		.mainmenuitem{
			display: block;
			width: 100%;
			min-height: 1em;
		}
	}
</style>
</head>
<body>
<div id="mainmenutoglbtn" style="cursor:pointer;background:black;padding:5px;position:fixed;top:5px;right:5px;z-index:999;color:white" onclick="togglemainmenu()" ><span class="fa fa-bars" id="toglicon" ></span></div>
<div id="mainmenudiv" style="position:fixed;top:0px;left:0px;z-index:998;background:black;color:white;">
	<a class="mainmenuitem" onclick="scroll2('aboutdiv')">About</a>
	<a class="mainmenuitem" onclick="scroll2('productsdiv')" >Products</a>
	<a class="mainmenuitem" onclick="scroll2('reviewsdiv')">Reviews</a>
	<a class="mainmenuitem" onclick="scroll2('eventsdiv')">Events</a>
	<a class="mainmenuitem" onclick="scroll2('toolsdiv')">Tools</a>
	<a class="mainmenuitem" onclick="scroll2('contactdiv')">Contact</a>
</div>
<div style="width:80%;margin:0px auto;min-width:310px;text-align:left" >
	<div id="aboutdiv" style="padding:10px" ><img src="./imgs/img01.jpg" align="left" ><?=randomwords(); ?></div>
	<div id="productsdiv" style="padding:10px" ><img src="./imgs/img02.jpg" align="left"  ><?=randomwords(); ?></div>
	<div id="reviewsdiv" style="padding:10px" ><img src="./imgs/img03.jpg" align="left"  ><?=randomwords(); ?></div>
	<div id="eventsdiv" style="padding:10px" ><img src="./imgs/img04.jpg" align="left"  ><?=randomwords(); ?></div>
	<div id="toolsdiv" style="padding:10px" ><img src="./imgs/img05.jpg" align="left"  ><?=randomwords(); ?></div>
	<div id="contactdiv" style="padding:10px" ><img src="./imgs/img06.jpg" align="left"  ><?=randomwords(); ?></div>
</div>
<script type="text/javascript">
window.togglemainmenu = function(){
	var el = document.getElementById("mainmenudiv");
	if(el.clientHeight <= 1){
		TweenMax.set(el, {height:"auto"})
    	TweenMax.from(el, 0.2, {height:0});
    	document.getElementById("toglicon").setAttribute('class','fa fa-close');
	}else{
    	TweenMax.to(el, 0.2, {height:0});
    	document.getElementById("toglicon").setAttribute('class','fa fa-bars');
	}
}
window.scroll2 = function(sxn,e){
	//TweenMax.to(window, 2, {scrollTo:{y:'#'+sxn, offsetY:50}});
	console.log(e);
	zenscroll.to(document.getElementById(sxn));
	if(Screen.getViewportWidth() < 479 && document.getElementById("mainmenudiv").clientHeight > 0){
		togglemainmenu();
	}
}
</script>
</body></html>
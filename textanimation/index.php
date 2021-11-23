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
 ?><html><head><title>Codesample Animate Text</title></head>
<script type="text/javascript" src="<?=$hpath; ?>js/jquery.js" ></script>
<script src="<?=$hpath; ?>js/gsap/gsap.min.js" ></script>  
<script type="text/javascript" src="<?=$hpath; ?>js/txt/lettering.js"></script>
<LINK href="<?php echo $hpath; ?>css.css" type="text/css" rel="stylesheet" />
<style type="text/css">
	#sometitle{
		/*opacity:0;*/
	}
</style>
<body style="display:flex;flex-flow:column wrap;justify-content:center;align-items:center" >
<h1 id="sometitle" >Some&nbsp;Title</h1>
<script type="text/javascript">
window.addEventListener('load', (event) => {
	$("#sometitle").lettering().children("span").css({"opacity":"0","display":"inline-block","transform":"rotate(20deg)"});
	window.letteridx = 0;
	fadeIn(letteridx);
	
	/*for(var a in document.getElementById("sometitle").childNodes){
		var node = document.getElementById("sometitle").childNodes[a];
		//console.log(a);
		if(typeof node != 'object'){continue;}
		//console.log(document.getElementById("sometitle").childNodes[a]);
		TweenMax.to(node,0.5,{opacity:1});
	}*/
	//
});
window.fadeIn = function(a){
	var node = document.getElementById("sometitle").childNodes[a];
	if(typeof node != 'object'){return;}
	if(node.innerHTML == ' '){window.letteridx++;fadeIn(letteridx);return;}
	TweenMax.to(node,0.2,{opacity:1,transform:"rotate(0deg)",onComplete:function(){window.letteridx++;fadeIn(letteridx);}});
}
</script>
</body></html>
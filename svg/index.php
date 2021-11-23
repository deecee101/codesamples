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
?>
<html><head><title></title>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
<script type='text/javascript' src='<?php echo $hpath; ?>js/jquery.js'></script>
<script src="<?php echo $hpath; ?>js/snap.js" ></script>
<script src="<?=$hpath; ?>js/gsap/gsap.min.js" ></script>  
<script src="<?=$hpath; ?>js/SnapPlugin.js"/></script>
<script src="<?=$hpath; ?>js/functions.js"/></script>
</head>
<body>
<svg id="stage" style="position:absolute;top:0px;left:0px;display:block;width:100%;height:600px;background:grey" ></svg>

<div style="position:absolute;top:300px;left:10px" ><button style="font-size:x-large" onclick="m()" >animate</button></div>
<script language="javascript" >
var s = Snap("#stage");
var aro = s.image("arrow.svg", 10, 25, 200,200 );
aro.node.setAttribute('id','aro');
var circl = s.image("circle.svg", 250, 25, 200,200 );
circl.node.setAttribute('id','circl');
var star = s.image("star.svg", 500, 25, 300,300 );
star.node.setAttribute('id','star');

var centerx = Screen.getViewportWidth()/2;
var centery = Screen.getViewportHeight()/2;

var t = new Snap.Matrix()
              .translate(centerx/2, centery/2)
              .rotate(20, 100, 100);
			  
var path1=s.path("M 318.94777,112.82995 242.16438,193.04116 264.71081,301.76644 164.69804,253.52773 68.261383,308.56861 83.233476,198.54424 1.0859229,123.83609 110.35195,104.076 156.01863,2.8629486 208.57666,100.67491 Z").attr({"stroke": "#000", "stroke-width":0,"fill":"none"}).transform(t);
var leng = path1.getTotalLength();
      
var path2=s.path("M 318.94777,112.82995 242.16438,193.04116 264.71081,301.76644 164.69804,253.52773 68.261383,308.56861 83.233476,198.54424 1.0859229,123.83609 110.35195,104.076 156.01863,2.8629486 208.57666,100.67491 Z").attr({"stroke": "#000", "stroke-width":2,"fill":"none","stroke-dasharray": leng+" "+leng,"stroke-dashoffset": leng}).transform(t);
var minus = parseFloat(path2.node.style.strokeDashoffset)-5;

function animate(){
	//console.log(path2.node.style.strokeDashoffset);
	var d = path2.node.style.strokeDashoffset;
	var doffset = d.substring(0, d.length-2);
	
	if(parseFloat(doffset) > 15 ){
		minus -= 25;
		path2.attr({'stroke-dashoffset':minus});
		//console.log(doffset);
	}else{
		path2.attr({'stroke-dashoffset':0,"stroke-dasharray":"none","stroke-dashoffset":"none"});
		path2.animate({fill: "#223fa3", stroke: "#0f0", "stroke-width": 80}, 3000);
		clearInterval(intvl);
	}
}	
var intvl;
function m(){
	intvl = setInterval('animate()',25);	
}
</script>
</body></html>
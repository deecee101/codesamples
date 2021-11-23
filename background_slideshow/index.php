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
 include_once($spath.'php/functions.php');
 $imgs = array();
foreach (new DirectoryIterator($spath."slides/") as $dirxItem) {
  if(is_dir($spath."slides/$dirxItem") || $dirxItem == '.' || $dirxItem == '..' ){
    continue;
  }
  
  if(is_image($spath."slides/$dirxItem")){
    //echo $hostroot."toolbox/slideshow/imgs/$dirxItem <BR> \n";
    //$id++;
    array_push($imgs, $hpath."slides/$dirxItem");
  }
}
 ?><html><head><title></title>
 <script src="<?=$hpath; ?>js/jquery.js" ></script>
 <script src="<?=$hpath; ?>js/imgloadr.js" ></script> 
 <script src="<?=$hpath; ?>js/gsap/gsap.min.js" ></script>  
<style type="text/css">
  .bgslide{
    position:fixed;
    top:0px;
    left:0px;
    width:100%;
    height:100%;
    background-position: center center;
    background-size: cover;
    background-repeat:no-repeat;
  }
</style>
</head>
<body>
<div class="bgslide" id="slide1" style="z-index:1" ></div>
<script>
var zindx = 1;
window.addEventListener('load', (event) => {
  var loader = new ImageLoader('<?=$imgs[0]; ?>');
    //set event handler
    loader.loadEvent = function(url, image){
        //action to perform when the image is loaded
        //document.body.appendChild(image);
        document.getElementById("slide1").style.backgroundImage = 'url("'+url+'")';


    }
    loader.load();
    setTimeout('slidesh()', 3000);
});
var currslide = 1;
var imgs=[{"id":"0","url":"null"},
<?php
  $s = '';
  $id=1;
  foreach ($imgs as $ke => $val){
      $s.="{'id':'_$id','url':'".$val."'},";
      $id++;
  }
  echo rtrim($s, ',');
?>
];
window.slidesh = function(){
  currslide++;
  var prevslide;
  if(currslide >= imgs.length){
    currslide = 1;
  }
  loader = new ImageLoader(imgs[currslide].url);


  loader.loadEvent = function(url, image){
        //zindx++;
        var obj = document.createElement('div');
        obj.setAttribute('class', "bgslide");
        obj.id = 'slide'+currslide;
        obj.style.cssText = 'background:url("'+url+'");background-position: center center;background-size: cover;background-repeat:no-repeat';
        
        if(typeof(document.getElementById("slide"+(imgs.length-1))) != 'undefined' && document.getElementById("slide"+(imgs.length-1)) != null){
          obj.style.zIndex = 1; obj.style.opacity = 1;
          document.body.appendChild(obj);
          setTimeout('slidesh()', 2000);
          var fd = document.getElementById("slide"+(imgs.length-1));
          TweenMax.to(fd, 1, {opacity:0,onComplete:function(){
              document.body.removeChild(fd);
          }});
        }else{
          obj.style.zIndex = currslide; obj.style.opacity = 0;
          document.body.appendChild(obj);
          setTimeout('slidesh()', 2000);
          var fd = document.getElementById("slide"+(currslide-1));
          TweenMax.to(obj, 1, {opacity:1,onComplete:function(){
            if(typeof(document.getElementById("slide"+(currslide-1))) != 'undefined' && document.getElementById("slide"+(currslide-1)) != null){
              document.body.removeChild(fd);
            };
          }});
        }
        
        
  }
  loader.load();  
}
</script></body></html>
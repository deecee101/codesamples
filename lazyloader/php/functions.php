<?php 
function makehash($len=0){
	$char = "0123456789abcdefghijklmnopqrstuvwxyz";
	$ulen = mt_rand(5, 10);
	if($len != 0){$ulen = $len;}
	$hash = '';
	for ($i = 1; $i <= $ulen; $i++) {
		$hash .= substr($char, mt_rand(0, strlen($char)), 1);
	}
	return $hash;
}
function createthumb($img, $location,$w, $h){
	$xx = explode('?', $img);
	$x = $xx[0];
	$expl = explode('/', $x);
	$filename = $expl[count($expl)-1];
	//echo $filename;   exit;
	$ext = strtolower(substr(strrchr($filename,"."),1));
	$fname = str_replace('.'.$ext,'',$filename);
	$location = rtrim($location,'/').'/';
	if(is_file($location.'__'.base64_encode($fname).".".$ext)){
	     return;
	}
	if(filesize($img) <= 0){
	     return;
	}
	if(strlen($fname) > 50){
	     return;
	}
	//echo 'thumb : '. $img." \n";
	$im = new Imagick();
	$svg = file_get_contents($img);
	$img = strtok($img, '?');

	$im->readImageBlob($svg);
	$d = $im->getImageGeometry();
	$width = $d['width'];
	if($width < $w){
		copy($img, rtrim($location,'/').'/'.$filename);
		return;
	}
	if(strstr($ext, 'jpg') || strstr($ext, 'JPG') || strstr($ext, 'jpeg') || strstr($ext, 'JPEG')){
		$im->setImageFormat("jpeg");
		//$im->adaptiveResizeImage(200, 200); 
	}else if(strstr($ext, 'png') || strstr($ext, 'PNG')){
		$im->setImageFormat("png24");
	}else if(strstr($ext, 'gif') || strstr($ext, 'GIF')){
		$im->setImageFormat("gif");
		//$im->adaptiveResizeImage(200, 200);
	}else if(strstr($ext, 'bmp')||strstr($ext, 'svg')||strstr($ext, 'php')){
		//exit('other');
		$other = true;
		$im->setImageFormat("jpeg");
		$fext = 'jpg';
		if(!is_file($dir.str_replace($ext, $fext, $dirxItem))){
			$im->writeImage($dir.str_replace($ext, $fext, $dirxItem));
		}
	}else{
		$im->setImageFormat("jpeg");
	}
	
	$im->setImageResolution(72,72);
	$im->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
	
	//$im->scaleImage($w,0);
	$d = $im->getImageGeometry();
	$hei = $d['height'];
	/*if($d['height'] > 2000){
	     return;
	}else if($d['width'] > 2000){
	     return;
	}*/

	if($hei > $h) {
		$im->scaleImage(0,$h);
	}else{
          $im->scaleImage($w,0);
     }
	$tempimg = explode('/',$x);
	$i=$fname.".".$ext;
	$location = rtrim($location,'/').'/';
	//echo "created img : ".$location.$i."\n";
	$im->writeImage($location.$i);
	$im->clear();
	$im->destroy();	
}
 ?>
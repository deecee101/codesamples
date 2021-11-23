<?php  
function randomwords(){
	$rwordsstr="Lorem ipsum dolor sit amet, consectetur adipiscing elit. In hendrerit scelerisque vulputate. Proin ut orci sapien. Curabitur consectetur ornare est ac eleifend. Vivamus at purus id nibh iaculis tincidunt. Duis id aliquet risus. In vestibulum, sapien eu mattis varius, orci leo feugiat lacus, sed fermentum dui ipsum sit amet urna. Duis in elit id velit sodales elementum vitae sit amet nisi. Vivamus porta lacinia consequat. Mauris nec dui ut quam rutrum ultricies. Nullam ac ornare lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi at odio turpis, at pharetra eros. Proin lacinia egestas suscipit. Donec nibh dui, congue a dictum a, egestas ut risus. Phasellus non neque lorem.Aenean metus leo, lacinia at blandit id, pretium at erat. Maecenas mollis mi vitae erat adipiscing vestibulum adipiscing nulla suscipit. Pellentesque ut ipsum a urna ultrices pellentesque eu nec elit. Vivamus ac dui tortor, vitae gravida mauris. Aenean pharetra mauris eget mi cursus venenatis. Vestibulum neque dolor, venenatis ac gravida sed, porta varius eros. Etiam lobortis, mi at tincidunt dictum, felis felis interdum felis, at vehicula est tellus sed purus. Nulla facilisi. Suspendisse vitae leo enim, nec pharetra justo. Fusce odio mi, convallis ac rutrum sit amet, vehicula nec nunc. ";
	$lines=explode(".",$rwordsstr);

	shuffle($lines);
	$lorem = '';
	foreach($lines as $l => $i){
		$lorem .= $i;
	}

	return $lorem;
}
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
function logerror($err){
	global $spath;
 	$str = '';
 	if(is_file($spath."error.txt")){
	 	$handle = @fopen($spath."error.txt", "r");
	 	if ($handle) {
	 	    while (($buffer = fgets($handle, 4096)) !== false) {
	 	        $str .= $buffer;
	 	    }
	 	    if (!feof($handle)) {
	 	        echo "Error: unexpected fgets() fail\n"; return;
	 	    }
	 	    fclose($handle);
	 	}
	}

 	$str .= "\n".$err.' : '.date('l dS F Y h:i:s a', time());	
 	$fp = fopen($spath."error.txt", "w");
 	fwrite($fp, $str);
 	fclose($fp); 
}
$pattern['script'] = "/<SCRIPT/";
$pattern['uname']= '/^[a-zA-Z0-9_^]{2,50}$/';
$pattern['loose'] = '/^[a-zA-Z0-9 \n \s \t _ !#$%&(\' )*+,.\/;=?@^`|~:\\\-]+$/';
$pattern['alnum'] = '/^[A-Za-z0-9 ]{1,500}+$/';//alpha numeric
$pattern['num'] = '/^[0-9]+$/';//
$pattern['az'] = '/^[a-zA-Z]+$/';//
$pattern['title'] = '/^[^@<>\%\*\?\.~\\\!+=\"\'\$#\`\[\]\|\{\}\/:\,()&^]{2,100}$/';//
$pattern['url'] = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';//url//
$pattern['fileurl'] = '/(?:([^:\/?#]+):)?(?:\/\/([^\/?#]*))?([^?#]*\.(?:jpg|jpeg|gif|gifv|png|PNG|JPG|JPEG|GIF|mp4|mp3|m4v|html|htm|pdf))(?:\?([^#]*))?(?:#(.*))?/';//pic_url//
$err['loose'] = 'something was wrong with your input, please try again';
function validate($var2filter, $regex){ 
	global $pattern ;
    global $err;
   if(isset($var2filter)){
        if(preg_match($pattern[$regex], $var2filter)){
            if(!preg_match($pattern['script'], $var2filter)){
				return true;
				exit("script");
            }else{
				return false;
			   exit();
            }	
			
        }else{
			return false;
			exit();
        }
    }
}
$err;
function is_image($img_name){
	global $_FILES;
	global $err;
	$path = $img_name;
	global $err;
	//$path = $_FILES[$img_name]['tmp_name'];
	$info = pathinfo($path);
	$image_mime_types = array(
		'image/jpeg',
		'image/pjpeg',
		'image/jpeg',
		'image/jpeg',
		'image/pjpeg',
		'image/jpeg',
		'image/pjpeg',
		'image/jpeg',
		'image/pjpeg',
		'image/png',
		'image/gif'
	);
	if (filesize($img_name) == 0){
		$err['flag'] = 'there is something wrong with one of your file sizes -- it is 0 bytes?'; return false;
	}
	//if the file size is larger than 5mb - throw an error and go to prev document
	if (filesize($img_name) > 10000000){
		$err['flag'] = 'there is something wrong with one of your file sizes -- it is over 10 megabytes?'; return false;
	}
	//if the file is not a jpeg - throw an error and go to prev document
	
	/*if (!in_array ($_FILES[$img_name]['type'],$image_mime_types)){
		$err['flag'] = "there is something wrong with one of your files -- it is not an image?";  return false;
	}*/
	if(!class_exists('finfo')) {
		//echo $path.' : image '.mime_content_type($path).'<BR>';
		if(in_array(mime_content_type($path), $image_mime_types)){
			return true;
		}else{
			$err['flag'] = "there is something wrong with one of your files -- it is not an image?";  return false;
		}	
	}else{
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		//$ftype =  finfo_file($finfo, $path);
		$ftype = finfo_file($finfo, $path);
		finfo_close($finfo);
		//echo $ftype.'<BR>';
		if(in_array($ftype , $image_mime_types))
		{
			return true;
		}
			$err['flag'] = "there is something wrong with one of your files -- it is not an image?";  return false;
	}
}
function img_upld($img_name, $img_location, $w, $h, $rename){
	global $_FILES;
	global $servroot;
	global $hostroot;
	global $hpath;
	global $spath;
	global $err;
	$img_err = false;

	if (file_exists($img_location.(str_replace(" ", "_" ,$_FILES[$img_name]['name'])))) {
		$err['flag'] = 'File is already there, please try again.'; return false;
	} 
	//if the file size is less than 0 - throw an error and go to prev document
	if ($_FILES[$img_name]['size'] == 0){
		$err['flag'] = 'there is something wrong with one of your file sizes -- it\'s 0 bytes?'; return false;
	}
	//if the file size is larger than 5mb - throw an error and go to prev document
	if ($_FILES[$img_name]['size'] > 10000000){
		$err['flag'] = 'there is something wrong with one of your file sizes -- it\'s over 10 megabytes?'; return false;
	}
	//if the file is not a jpeg - throw an error and go to prev document
	$allowedmimes = array ("image/pjpeg", "image/jpg", "image/pjpg", "image/jpeg","image/png", "video/flv", "image/gif");
	
	if (!in_array ($_FILES[$img_name]['type'],$allowedmimes)){
		$err['flag'] = "there is something wrong with one of your files -- it's not an image?";  return false;
	}
	if(!is_image($_FILES[$img_name]['tmp_name'], $img_name)){
		$err['flag'] = 'File is not an image? please try again';  return false;
	}
	$prefix = "_";
	if($w==0 && $h==0){$prefix='';}
	if($img_err == false){
		if (!move_uploaded_file($_FILES[$img_name]['tmp_name'], $img_location.($prefix.$_FILES[$img_name]['name']))){
			$err['flag'] = "The files could not be moved ".$img_location.(str_replace(" ", "_" ,$_FILES[$img_name]['name']));
			return false;
		}else{
			//createthumb($img_location."_".$_FILES[$img_name]['name'],$img_location.$_FILES[$img_name]['name'],$w,$h);	
			if($w != 0 && $h != 0){
				$im = new Imagick();
				$svg = file_get_contents($img_location.("_".$_FILES[$img_name]['name']));
				$im->readImageBlob($svg);
				$im->resizeImage($w, $h, imagick::FILTER_LANCZOS, 1, true); 
				$im->writeImage($img_location.$_FILES[$img_name]['name']);/*(or .jpg)*/
				$im->clear();
				$im->destroy();	
				unlink($img_location."_".$_FILES[$img_name]['name']);
			}
			if($rename == true){
				$char = "0123456789abcdefghijklmnopqrstuvwxyz";
				$ulen = mt_rand(5, 10);
				$hash = '';
				for ($i = 1; $i <= $ulen; $i++) {
					$hash .= substr($char, mt_rand(0, strlen($char)), 1);
				}
				//$hash = $hash.'_';
				$ext = strtolower(substr(strrchr($_FILES[$img_name]['name'],"."),1));
				rename($img_location.$_FILES[$img_name]['name'], $img_location.$hash.'.'.$ext);
				return($img_location.$hash.'.'.$ext);
			}else{
				return($img_location.$_FILES[$img_name]['name']);
			}
		}
	}else{
			$err['flag'] = '??';
			return false;
			exit();
	}
}
function truncate($string, $limit, $break, $pad) { 
	// return with no change if string is shorter than $limit  
	if(strlen($string) <= $limit) return $string; 
	// is $break present between $limit and the end of the string?  
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
		if($breakpoint < strlen($string) - 1) { 
			$string = substr($string, 0, $breakpoint) . $pad; 
		} 
	} 
	return $string; 
}
function get_user_browser()
{
	if(!isset($_SERVER['HTTP_USER_AGENT'])){$ub = "chrome"; return;}
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = '';
    if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]) == true){
		$ub = 'mobile';
	}
    if(preg_match('/(msie|edge|trident)/i',$u_agent))
    {
		$ub = "ie";
	}
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $ub = "firefox";
    }
	elseif(preg_match('/(Opera|opr)/i',$u_agent))
    {
        $ub = "opera";
    }
    elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/(opera|chrome|opr|firefox)/i',$u_agent))
    {
    	//exit('Safari');
        $ub = "safari";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $ub = "chrome";
    }
    return $ub;
}
$ub = get_user_browser(); //exit($ub);
function createthumb($name,$filename,$new_w,$new_h)
{
	//$system=explode(".",$name);
	$system = strtolower(substr(strrchr($filename,"."),1));
	//exit($system);
	if (preg_match("/jpg|jpeg|JPG|JPEG/",$system)){$src_img=imagecreatefromjpeg($name);}
	if (preg_match("/png|PNG/",$system)){$src_img=imagecreatefrompng($name);}
	if (preg_match("/gif|GIF/",$system)){$src_img=imagecreatefromgif($name);}
	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);

	if ($old_x > $old_y) 
	{
		$thumb_w=$new_w;
		$thumb_h=$old_y*($new_w/$old_x);
	}
	if ($old_x < $old_y) 
	{
		$thumb_w=$old_x*($new_h/$old_y);
		$thumb_h=$new_h;
	}
	if ($old_x == $old_y) 
	{
		$thumb_w=$new_w;
		$thumb_h=$new_h;
	}
	$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	if (preg_match("/png|PNG/",$system))
	{
		imagepng($dst_img,$filename); 
	} else if(preg_match("/jpg|jpeg|JPG|JPEG/",$system)){
		imagejpeg($dst_img,$filename); 
	}else if(preg_match("/gif|GIF/",$system)){
		imagegif($dst_img,$filename); 
	}
	imagedestroy($dst_img); 
	imagedestroy($src_img);  
}
?>
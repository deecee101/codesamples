<?php
if(!isset($_SESSION)){session_start();}
function findTempDirectory()
  {
    if(isset($_ENV["TMP"]) && is_writable($_ENV["TMP"])) return $_ENV["TMP"];
    elseif( is_writable(ini_get('upload_tmp_dir'))) return ini_get('upload_tmp_dir');
    elseif(isset($_ENV["TEMP"]) && is_writable($_ENV["TEMP"])) return $_ENV["TEMP"];
    elseif(is_writable("/tmp")) return "/tmp";
    elseif(is_writable("/windows/temp")) return "/windows/temp";
    elseif(is_writable("/winnt/temp")) return "/winnt/temp";
    else return null;
  }
function trace($txt, $isArray=false){
	//creating a text file that we can log to
	// this is helpful on a remote server if you don't
	//have access to the log files
	//
	$log = new cLOG("upload.txt", false);
	//$log->clear();
	if($isArray){
		$log->printr($txt);
	}else{
		$log->write($txt);
	}

	//echo "$txt<br>";
}
function is__image($path){
	$info = pathinfo($path);
	$file_name =  basename($path,'.'.$info['extension']);
	if(!class_exists('finfo')) {
		if(strstr($fname, '.jpg')||strstr($fname, '.JPG')||strstr($fname, '.jpeg')||strstr($fname, '.JPEG')||strstr($fname, '.png')||strstr($fname, '.PNG')||strstr($fname, '.gif')||strstr($fname, '.GIF')||strstr($fname, '.php')){
			return true;
		}else{
			include('mimetypes.php');
			//echo $path.' : image '._mime_content_type($path).'<BR>';
			if(in_array(_mime_content_type($path), $image_mime_types)){
				return true;
			}else{
				return false;	
			}	
		}
	}else{
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		//$ftype =  finfo_file($finfo, $path);
		$ftype = finfo_file($finfo, $path);
		finfo_close($finfo);
		//echo $ftype.'<BR>';
		if(in_array($ftype , array('image/jpeg' , 'image/png' ,'image/gif')))
		{
			return true;
		}
		return false;
	}
}
function is__audio($path){
	if(!class_exists('finfo')) {
		if(strstr($path, '.mp3')||strstr($path, '.ogg')||strstr($path, '.MP3')||strstr($path, '.OGG')){
			return true;
		}else{
			include('mimetypes.php');
			if(in_array(_mime_content_type($path), $audio_mime_types)){
				return true;
			}else{
				//echo $audio_mime_types.' : audio<BR>';
				return false;	
			}
		}
	}else{
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		//$ftype =  finfo_file($finfo, $path);
		$ftype = finfo_file($finfo, $path);
		finfo_close($finfo);
		//echo $ftype.'<BR>';
		if(in_array($ftype , array('audio/x-wav' , 'application/ogg' ,'audio/mpeg')))
		{
			return true;
		}
		return false;
	}
}
function is__video($path){
	if(!class_exists('finfo')) {
		if(strstr($path, '.mp4')||strstr($path, '.ogv')||strstr($path, '.webm')||strstr($path, '.MP4')||strstr($path, '.OGV')||strstr($path, '.WEBM')){
			return true;
		}else{
			include('mimetypes.php');
			//echo $video_mime_types.' : video <BR>';
			if(in_array(_mime_content_type($path), $video_mime_types)){
				return true;
			}else{
				return false;	
			}
		}
	}else{
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		//$ftype =  finfo_file($finfo, $path);
		$ftype = finfo_file($finfo, $path);
		finfo_close($finfo);
		//echo $ftype.'<BR>';
		if(in_array($ftype , array('video/mp4' , 'application/ogg' ,'application/octet-stream')))
		{
			return true;
		}
		return false;
	}
}
function create_thumb($name,$filename,$new_w,$new_h)
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
function validate_upl($path){
	$valid = false;
	if(is__video($path)||is__image($path)||is__audio($path)||_mime_content_type($path)=='application/pdf'){
		$valid = true;
	}
	return $valid;
}
function _mime_content_type($filename) {
    $result = new finfo();

    if (is_resource($result) === true) {
        return $result->file($filename, FILEINFO_MIME_TYPE);
    }

    return false;
}
?>
<?php  
$err = Array();;
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
function is_audio($path){
	if(!class_exists('finfo')) {
		if(strstr($path, '.mp3')||strstr($path, '.ogg')||strstr($path, '.MP3')||strstr($path, '.OGG')){
			return true;
		}else{
			include('mimetypes.php');
			if(in_array(mime_content_type($path), $audio_mime_types)){
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
function is_video($path){
	if(!class_exists('finfo')) {
		if(strstr($path, '.mp4')||strstr($path, '.ogv')||strstr($path, '.webm')||strstr($path, '.MP4')||strstr($path, '.OGV')||strstr($path, '.WEBM')){
			return true;
		}else{
			include('mimetypes.php');
			//echo $video_mime_types.' : video <BR>';
			if(in_array(mime_content_type($path), $video_mime_types)){
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
function validateupl($path,$file_name='img'){
	global $err;
	$valid = false;
	if(is_video($path)||is_image($path)||is_audio($path)||mime_content_type($path)=='application/pdf'){
		$valid = true;
	}
	return $valid;
}
?>
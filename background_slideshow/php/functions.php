<?php 
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
		return false;
	}
	//if the file size is larger than 5mb - throw an error and go to prev document
	if (filesize($img_name) > 10000000){
		$err['flag'] = 'there is something wrong with one of your file sizes -- it is over 10 megabytes?'; return false;
		return false;
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
			return false;
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
		return false;
	}
}
?>
<?php  
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
?>
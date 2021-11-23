<?php  
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
?>
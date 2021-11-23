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
function crypt_apr1_md5($plainpasswd, $salt) {
    if($salt == NULL){
        $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
    }else{
    	$salt = base64_encode($salt);
    }
	$len = strlen($plainpasswd);
	$text = $plainpasswd.'$apr1$'.$salt;
	$bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
	for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
	for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd[0]; }
	$bin = pack("H32", md5($text));
	for($i = 0; $i < 1000; $i++) {
		$new = ($i & 1) ? $plainpasswd : $bin;
		if ($i % 3) $new .= $salt;
		if ($i % 7) $new .= $plainpasswd;
		$new .= ($i & 1) ? $bin : $plainpasswd;
		$bin = pack("H32", md5($new));
	}
	$tmp = '';
	for ($i = 0; $i < 5; $i++) {
		$k = $i + 6;
		$j = $i + 12;
		if ($j == 16) $j = 5;
		$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
	}
	$tmp = chr(0).chr(0).$bin[11].$tmp;
	$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
	"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
	"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
	return "$"."apr1"."$".$salt."$".$tmp;
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
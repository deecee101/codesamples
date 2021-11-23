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
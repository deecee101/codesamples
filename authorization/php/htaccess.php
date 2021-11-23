<?php

/**
* Class for handling htaccess of Apache
* @author    Sven Wagener <sven.wagener@intertribe.de>
* @copyright Intertribe - Internetservices Germany
* @include 	 Funktion:_include_
*/

class htaccess{
    var $fHtaccess=""; // path and filename for htaccess file
    var $fHtgroup="";  // path and filename for htgroup file
    var $fPasswd="";   // path and filename for passwd file
    
    var $authType="Basic"; // Default authentification type
    var $authName="Internal area"; // Default authentification name

    /**
    * Initialising class htaccess
    */
    function htaccess(){
    }

    /**
    * Sets the filename and path of .htaccess to work with
    * @param string	$filename    the name of htaccess file
    */
    function setFHtaccess($filename){
        $this->fHtaccess=$filename;
    }
    
    /**
    * Sets the filename and path of the htgroup file for the htaccess file
    * @param string	$filename    the name of htgroup file
    */
    function setFHtgroup($filename){
        $this->fHtgroup=$filename;
    }
    
    /**
    * Sets the filename and path of the password file for the htaccess file
    * @param string	$filename    the name of htgroup file
    */
    function setFPasswd($filename){
        $this->fPasswd=$filename;
    }

    /**
    * Adds a user to the password file
    * @param string $username     Username
    * @param string $password     Password for Username
    * @param string $group        Groupname for User (optional)
    * @return boolean $created         Returns true if user have been created otherwise false
    */
    function addUser($username,$password){//,$group){
        // checking if user already exists
        $file=@fopen($this->fPasswd,"r");
        $isAlready=false;
        while($line=@fgets($file,200)){
            $lineArr=explode(":",$line);
            if($username==$lineArr[0]){
                $isAlready=true;
             }
        }
        
        if($isAlready==false){
			
            $file=fopen($this->fPasswd,"a");
			$pword = $this->crypt_apr1_md5($password);
            //$password=crypt($password);
            /*$fp = fopen("textfile.txt", "w");
            fwrite($fp, 'add user : username : '.$username.' passowrd : '.$password);
            fclose($fp); */
            $newLine=$username.":".$pword;
			 //$newLine=$username.":".$pword."\n"; since i am manually putting in the first user
            fputs($file,"\n".$newLine);//the newline is switched to here
            fclose($file);
            return true;
        }else{
            return false;
        }
    }

    /**
    * Adds a group to the htgroup file
    * @param string $groupname     Groupname
    */
    function addGroup($groupname){
        $file=fopen($this->fHtgroup,"a");
        fclose($file);
    }

    /**
    * Deletes a user in the password file
    * @param string $username     Username to delete
    * @return boolean $deleted    Returns true if user have been deleted otherwise false
    */
    function delUser($username){
        // Reading names from file
        $file=fopen($path.$this->fPasswd,"r");
        $i=0;
        while($line=fgets($file,200)){
            $lineArr=explode(":",$line);
            if($username!=$lineArr[0]){
                $newUserlist[$i][0]=$lineArr[0];
                $newUserlist[$i][1]=$lineArr[1];
                $i++;
            }else{
                $deleted=true;
            }
        }
        fclose($file);

        // Writing names back to file (without the user to delete)
        $file=fopen($path.$this->fPasswd,"w");
        for($i=0;$i<count($newUserlist);$i++){
            if(trim($newUserlist[$i][0]) == ''){continue;}
            $ln = trim($newUserlist[$i][0].":".$newUserlist[$i][1]);
            if($i != (count($newUserlist)-1)){ $ln .= "\n"; }
            fputs($file,$ln);
        }
        fclose($file);
        
        if($deleted==true){
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * Returns an array of all users in a password file
    * @return array $users         All usernames of a password file in an array
    * @see setFPasswd()
    */
    function getUsers(){
    }
    
    /**
    * Sets a password to the given username
    * @param string $username     The name of the User for changing password
    * @param string $password     New Password for the User
    * @return boolean $isSet      Returns true if password have been set
    */    
    function setPasswd($username,$new_password){
        // Reading names from file
		//echo $username.','.$new_password; exit;
        $newUserlist;
        $isSet=false;
        $file=fopen($this->fPasswd,"r");
        $x=0;
        $unmlst = '';
        for($i=0;$line=fgets($file,200);$i++){
			
            $lineArr=explode(":",$line);
			if(trim($lineArr[0]) == ''){continue;}
			//var_dump($lineArr); echo "\n \n";
            if($username!=$lineArr[0] && $lineArr[0]!="" && $lineArr[1]!=""){
                $newUserlist[$i][0]=$lineArr[0];
                $newUserlist[$i][1]=$lineArr[1];
                $x++;
            }else if($lineArr[0]!="" && $lineArr[1]!=""){
                $newUserlist[$i][0]=$lineArr[0];
                $newUserlist[$i][1]=$this->crypt_apr1_md5($new_password)."\n";
                $isSet=true;
                $x++;
            }
            $unmlst .= $lineArr[0].":".$lineArr[1]."\n";
        }
        fclose($file);
		//var_dump(); exit;
        unlink($this->fPasswd);
		//var_dump($newUserlist); exit;
        /// Writing names back to file (with new password)
        $file=fopen($this->fPasswd,"w");
		//echo count($newUserlist); exit;
        for($i=0;$i<=count($newUserlist);$i++){
			if(!isset($newUserlist[$i])){continue;}
            $content=$newUserlist[$i][0].":".$newUserlist[$i][1];
			//echo $content;
            fputs($file,$content);
        }
        fclose($file);
            /*$fp = fopen("textfile.txt", "w");
            fwrite($fp, $unmlst."\n".'change password user : '.$username.' : '.$new_password.', isset : '.strval($isSet) );
            fclose($fp);*/
        if($isSet==true){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Sets the Authentification type for Login
    * @param string $authtype     Authentification type as string
    */
    function setAuthType($authtype){
        $this->authType=$authtype;
    }

    /**
    * Sets the Authentification Name (Name of the login area)
    * @param string $authname     Name of the login area
  	*/
    function setAuthName($authname){
        $this->authName=$authname;
    }

    /**
    * Writes the htaccess file to the given Directory and protects it
    * @see setFhtaccess()
  	*/
    function addLogin(){
       $file=fopen($this->fHtaccess,"w+");
       fputs($file,"Order allow,deny\n");
       fputs($file,"Allow from all\n");
       fputs($file,"AuthType        ".$this->authType."\n");
	   
       fputs($file,"AuthUserFile    ".$this->fPasswd."\n");
       fputs($file,"AuthName        \"".$this->authName."\"\n");
       fputs($file,"require valid-user\n");
       fclose($file);
            /*$fp = fopen("textfile.txt", "w");
            fwrite($fp, 'add login ');
            fclose($fp);*/
    }

    /**
    * Deletes the protection of the given directory
    * @see setFhtaccess()
    */
    function delLogin(){
        unlink($this->fHtaccess);
    }
	function crypt_apr1_md5($plainpasswd) {
		$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
		$len = strlen($plainpasswd);
		$text = $plainpasswd.'$apr1$'.$salt;
		$bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
		for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
		for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd{0}; }
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
}
?>

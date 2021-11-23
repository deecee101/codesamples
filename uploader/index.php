<?php 
if(!isset($_SESSION)){session_start();}
//date_default_timezone_set('America/New_York');
$spath = str_replace("\\","/",getcwd()).'/';
$servroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/';
if(isset($_SERVER['HTTPS'])){$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';}else{$protocol = 'http';}
$hostroot = $protocol.'://'.$_SERVER['HTTP_HOST'].'/';
$hpath = str_replace($servroot, $hostroot, $spath);
$name = basename(__FILE__, '.php'); 
$fname = basename(__FILE__); 
$docurl = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$self = $_SERVER['PHP_SELF'];
/*echo getcwd().'<br>'; 
echo $spath.'<br>';
echo $servroot.'<br>';
echo $hostroot.'<br>';
echo $hpath;
echo $fname.'<br>';
echo $name.'<br>';
echo $docurl.'<br>'; exit;*/
@mkdir($spath.'files');
include_once($spath.'php/functions.php');
if(isset($_FILES['file'])){
	/*ob_start();
	var_dump($_FILES);
	$data = ob_get_clean();
	$fp = fopen($spath."log.txt", "w");
	fwrite($fp, $data);
	fclose($fp); */

	/*
	 * All of your application logic with $_FILES["file"] goes here.
	 * It is important that nothing is outputted yet.
	 */
	$err = Array();;
	foreach ($_FILES['file']['tmp_name'] as $ke => $val) {
		//echo $val.'<br>';
		if(validateupl($val) == true){
			$success = move_uploaded_file($val,  $spath.'files/'. $_FILES['file']['name'][$ke]);
			//echo '{"status":"ok","function":"completed file upload"}';  exit;
			$output = array("success" => true, "message" => "Success!");
		}else{
			$output = array("success" => false, "error" => "Failure! ".$err['flag']);
			//echo '{"status":"error","error":"file format error- '.$err['flag'].'"}';  exit;
		}
		/*if($moved == true){
			trace("uploaded ".$_FILES['file']['name'][$ke]);
		}else{
			trace("failed to upload ".$_FILES['file']['name'][$ke]);
		}*/
		/*if ($success) {
			$output = array("success" => true, "message" => "Success!");
		} else {
			$output = array("success" => false, "error" => "Failure!");
		}*/
	}
	
	// $output will be converted into JSON
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);	

	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
.progressBar {
	background-color: #3E6FAD;
	width: 0px;
	height: 30px;
	margin-top: 10px;
	margin-bottom: 10px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
	border-radius: 5px;
	-moz-transition: .25s ease-out;
	-webkit-transition: .25s ease-out;
	-o-transition: .25s ease-out;
	transition: .25s ease-out;
}
</style>
<script type="text/javascript" src="<?php echo $hpath; ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $hpath; ?>upload/simpleUpload.min.js"></script>

<script type="text/javascript">

$(document).ready(function(){

	$('input[type=file]').change(function(){

		$(this).simpleUpload("<?=$self; ?>?getFormat=1", {
			xhrFields: {"this":"here"},
			/*
			 * Each of these callbacks are executed for each file.
			 * To add callbacks that are executed only once, see init() and finish().
			 *
			 * "this" is an object that can carry data between callbacks for each file.
			 * Data related to the upload is stored in this.upload.
			 */

			start: function(file){
				//upload started
				console.log('start');
				$("#errmsg").empty();
				this.block = $('<div class="block"></div>');
				this.progressBar = $('<div class="progressBar"></div>');
				this.block.append(this.progressBar);
				$('#uploads').append(this.block);
				console.log(this.progressBar);
			},

			progress: function(progress){
				//received progress
				console.log('progress : '+ progress + "%");
				this.progressBar.width(progress + "%");
			},

			success: function(data){
				//upload successful
				console.log('complete');
				this.progressBar.remove();

				/*
				 * Just because the success callback is called doesn't mean your
				 * application logic was successful, so check application success.
				 *
				 * Data as returned by the server on...
				 * success:	{"success":true,"format":"..."}
				 * error:	{"success":false,"error":{"code":1,"message":"..."}}
				 */

				if (data.success) {
					console.log(this.upload.file.name);
					//now fill the block with the format of the uploaded file
					var format = data.format;
					var formatDiv = $('<div class="format"></div>').text(this.upload.file.name+' has been uploaded');
					this.block.append(formatDiv);
					//console.log(formatDiv);
				} else {
					//our application returned an error

					var error = data.error;
					console.log(error);
					$('<div/>',{
					    text:error
					}).appendTo('#errmsg');
				}

			},

			error: function(error){
				//upload failed
				console.log('error');
				//this.progressBar.remove();
				var error = data.error;
				$('<div/>',{
				    text:error
				}).appendTo('#errmsg');
			}

		});

	});

});
</script>
</head>
<body>
<div id="uploads"></div>
<div id="errmsg" ></div>
<input type="file" name="file[]" id="file[]" size="25" multiple />
</body>
</html>
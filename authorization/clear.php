<?php 	
if(!isset($_SESSION)){session_start();}
	$spath = str_replace("\\","/",getcwd()).'/';
	$servroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/';
	if(isset($_SERVER['HTTPS'])){$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';}else{$protocol = 'http';}
	$hostroot = $protocol.'://'.$_SERVER['HTTP_HOST'].'/';
	$hpath = str_replace($servroot, $hostroot, $spath);
	$name = basename(__FILE__, '.php'); 
	$fname = basename(__FILE__); 
	$docurl = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$self = $_SERVER['PHP_SELF'];
	include_once('db.php');

 	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);
	session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_start();
    session_regenerate_id(true);
 ?>
 <html><head><title></title></head>
 <body>
<script type="text/javascript">
	var DB = [];
	DB['user'] = new Array();
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
	window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"}; // This line should only be needed if it is needed to support the object's constants for older browsers
	window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
	if (!window.indexedDB) {
	    window.alert("Your browser doesn't support a stable version of IndexedDB. This website won't work on your browser.");
	}else{
		var dbExists = true;
		var request = window.indexedDB.open("<?php echo $idxdbname; ?>");
		request.onupgradeneeded = function (e){
			console.log('need database');
		    e.target.transaction.abort();
		    dbExists = false;
		}
		request.onerror = function(e){
		    console.log('no database');
		    setTimeout("window.location='<?=$hpath; ?>login/'",1000);
		}
		request.onsuccess = function(e){
			console.log('has db');
			window.db = e.target.result;
			clearusertoken();
		}
	}
	window.deletedb = function(){
	    if(db){
	        db.close();
	        var req = indexedDB.deleteDatabase('<?php echo $idxdbname; ?>');
	        req.onsuccess = function () {
	            console.log("Deleted database successfully");
	        };
	        req.onerror = function () {
	            console.log("Couldn't delete database");
	        };
	        req.onblocked = function () {
	            console.log("Couldn't delete database due to the operation being blocked");
	        };
	        delete db; 
	    }              
	}
window.clearusertoken = function(){
    if(!db.objectStoreNames.contains('user')){console.log("objectstore not found");setTimeout("deletedb()",400);return;}
    db.transaction(['user'], 'readonly').objectStore('user').count().onsuccess = function(e) {/*console.log(e.target.result+' records');*/ }
    var objectStore = db.transaction('user').objectStore('user');
    objectStore.openCursor().onsuccess = function(event) {
        var cursor = event.target.result;
        var re = db.transaction('user').objectStore('user').get(cursor.value.id);
        re.onsuccess = function(evt) {
            if(evt.target.result['token'] != null && evt.target.result['token'] != ''){
                console.log('has a token');
                var obj = Object.assign(evt.target.result);
                obj['token'] = '';
                console.log(obj);
	            db.transaction(['user'], "readwrite").objectStore('user').put(obj);
                window.location = '<?=$hpath; ?>login';
            	return;
            }else{
                console.log('does NOT have a token');
                window.location = '<?=$hpath; ?>login';
            	return;
            }
            window.DB['user'] = evt.target.result;
              return true;
        };
        re.onerror = function(e){
            console.log('no match');
            return false;
        }
    };          
} 
</script>
 </body></html>
 <?php 

 ?>
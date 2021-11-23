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
$dbtables = array("videos"=>'auto'); //dbtables**
include_once($spath.'../php/functions.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Indexed DB</title>
</head>
<body onload="dbcnx()">
<h1>IndexedDB Demo</h1>
<span style="display:block;width:100%" >this demonstrates the employment of client side storage, indexedDB<br>
there are functions to <br>
1) read the table, 2) list the table(s), 3) empty a table, 4) delete the database, 5) a function to query the database<br>
6) update the table, 7) delete an item<br>
<strong style="color:red" >the responses are listed in the browser console.</strong></span>
<button onclick="readObj('videos')" >read objectstore</button> <button onclick="listObjStores()" >list objectstores</button> <button onclick="clearObjStore('videos')" >empty table</button> <button onclick="deletedb()" >delete db</button><br>
<br>
<div style="display:block;width:100%" id="querydiv" >
<input type="checkbox" id="table" name="table" value="videos" disabled checked />
<label for="table"> videos</label><br>
<form id="videoChoiceForm" name="videoChoiceForm" style="display:block;width:100%"  ></form>
<form id="attributeChoiceForm" name="attributeChoiceForm" style="display:block;width:100%"  ></form><br>
<button onclick="qdb('videos',document.forms.videoChoiceForm.videoChoice.value,document.forms.attributeChoiceForm.attributeChoice.value)" >query db</button>
</div>
<h1>videos</h1>
<div id="forms" ></div>
<IFRAME SRC="<?php echo $hostroot; ?>blank.html" NAME="processor" ID="processor" STYLE="width:0px;height:0px;position:absolute;top:0px; left:0px" FRAMEBORDER="no" allowtransparency="yes" ></IFRAME>
<script>
window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"}; // This line should only be needed if it is needed to support the object's constants for older browsers
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
if (!window.indexedDB) {
    window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
}else{
	window.db; //indexedb object
	window.DB = []; //window objectt to hold indexeddb data
}
window.dbtables = [<?php
	foreach($dbtables as $ke => $val){
		echo '{"'.$ke.'":"'.$val.'"},';
	}
?>];

var currtblidx = 0;
window.dbcnx = function(){
	//forms.site_data and site_data
	//forms.business and business
	//forms.links nad links
	if(!window.db){
		console.log("connecting to db"); 
		//this connects to the database if it exists. if i does not exist, it throws an error
		var request = window.indexedDB.open('video_idxdb', 1);
		request.onupgradeneeded = function(e) {
			console.log("running onupgradeneeded");
			window.db = e.target.result; 
			<?php
			foreach($dbtables as $ke => $val){ ?>
				var <?php echo $ke; ?>_objStore = db.createObjectStore("<?php echo $ke; ?>", { <?php
				if($val == 'auto'){ ?> keyPath: "id", autoIncrement: true <?php }else{ ?> keyPath: "<?php echo $val; ?>" <?php } ?> });  
			<?php 
				//if($ke == 'msxns'){echo $ke.'_objStore.createIndex("title", "title", { unique: false });';}
			} ?>
		}

		request.onsuccess = function(e) {
			console.log("connected to video_idxdb");
			window.db = e.target.result; 
			//console.log(db.objectStoreNames);
			for(var q in db.objectStoreNames){
				var sig = db.objectStoreNames[q];
				if (typeof sig=="string"){
					//console.log(q+ " : sig = "+sig);
					window.DB[db.objectStoreNames[q]] = [];
					var c = db.transaction(db.objectStoreNames[q]).objectStore(db.objectStoreNames[q]).openCursor();
					c.onsuccess = function(evt){
						  var cursor = evt.target.result;
						  //console.log(evt.target.result);
						  if (cursor) {
							//console.log(" cursor source name = "+cursor.source.name); //tables / object store names
							//console.log(cursor.value);
							window.DB[cursor.source.name].push(cursor.value);
							for(var field in cursor.value) {
								//console.log(cursor.source.name+" : "+field);
								//console.log(cursor.value[field]);
								//console.log(field);
							}
							cursor.continue();
						  }else {
							currtblidx++;
							//alert("No more entries!");
							//console.log("end "+dbtables.length+" "+currtblidx);
							if(dbtables.length == currtblidx){
								for(var i in DB){
									//console.log(i);
									//loadview(i);
								}
								db.transaction(['videos'], 'readonly').objectStore('videos').count().onsuccess = function(event) {
									if(event.target.result === undefined || event.target.result == '' || event.target.result <= 0){
										startdb();
										return;
									}else{console.log('video files already in db'); createforms();}
								}
							}
						  }
					}
					
					c.onerror = function () {
						console.log("Couldn't load database");
					};
				}
			}	
		}
		request.onerror = function(e) {
			console.log("Error");
			console.dir(e);
		}
	}
}
var videofiles = new Array();
<?php
$path = realpath('../items/');
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
$i=1;
foreach($objects as $file => $object){
	$file = str_replace("\\",'/', $file);
	$e=explode('/', $file);
	$fname = $e[count($e)-1];
	$n = explode('.', $fname);
	$filename = $n[0];
	$ext = $n[1];
	if($filename =='.' || $filename =='..' || $filename == ''){continue;}
    //echo "$file <BR /> \n";
    echo "videofiles.push({'id':'".$i."','title':'".addslashes($filename)."','description':'".randomwords()."','idx':'".$i."','url':'".str_replace($servroot,$hostroot,$file)."'}); \n";
    $i++;
}
?>
window.startdb = function(){
	var vidObjectStore = db.transaction("videos", "readwrite").objectStore("videos");
    for (var i in videofiles) {
      vidObjectStore.add(videofiles[i]);
      DB['videos'].push(videofiles[i]);
    }
    console.log('added video files');
    createforms();
}
window.createforms = function(){
	var frm;
	for(var v in DB['videos']){
		frm = document.createElement("DIV");
		frm.setAttribute('id','_'+DB['videos'][v].id+'div');
		frm.setAttribute('style','display:block;border-bottom:1px solid gray;margin-bottom:25px');
		var div = document.createElement("video");
		div.setAttribute("id", "video_"+v);
		div.src = DB['videos'][v].url;
		div.style.width = "300px";
		div.setAttribute("controls", "true");
		frm.appendChild(div);

		div = document.createElement("br"); frm.appendChild(div);

		div = document.createElement("input");
		div.setAttribute("id", "idx"); div.setAttribute("name", "idx");
		div.setAttribute("onchange", "updatedb(this)"); div.setAttribute("size", "2"); 
		div.setAttribute("data-itmid", DB['videos'][v].id); 
		div.setAttribute("value", DB['videos'][v].idx); 
		frm.appendChild(div);

		div = document.createElement("input");
		div.setAttribute("id", "title"); div.setAttribute("name", "title");
		div.setAttribute("onchange", "updatedb(this)"); div.setAttribute("size", "50"); 
		div.setAttribute("data-itmid", DB['videos'][v].id); 
		div.setAttribute("value", DB['videos'][v].title); 
		frm.appendChild(div);

		div = document.createElement("br"); frm.appendChild(div);

		div = document.createElement("textarea");
		div.setAttribute("id", "description"); div.setAttribute("name", "description");
		div.setAttribute("onchange", "updatedb(this)"); div.setAttribute("cols", "40"); div.setAttribute("rows", "10");
		div.setAttribute("data-itmid", DB['videos'][v].id); 
		div.setAttribute("value", DB['videos'][v].description);
		div.innerHTML = DB['videos'][v].description; 
		frm.appendChild(div);

		div = document.createElement("button");
		div.setAttribute("onclick", "deletevideo('"+DB['videos'][v].id+"')");
		//div.onclick = function() {deletevideo(window.DB['videos'][v].id);}; 
		div.style.cursor = "pointer";
		div.style.zIndex="";
		div.innerHTML = 'delete video';
		frm.appendChild(div);

		document.getElementById('forms').appendChild(frm);
		//div = document.createElement("hr"); document.getElementById('forms').appendChild(div);
	
		var rb = document.createElement("input");
		rb.setAttribute("type", "radio");
		rb.setAttribute("id", "video_"+(parseInt(v)+1));
		rb.setAttribute("name", "videoChoice");
		rb.setAttribute("value", parseInt(v)+1);
		document.getElementById("videoChoiceForm").appendChild(rb);
		if(v == 0){
			rb.setAttribute("checked","true");
		}

		var lb = document.createElement("label");
		lb.setAttribute("for", "video_"+(parseInt(v)+1));
		lb.innerHTML = "video "+(parseInt(v)+1);
		document.getElementById("videoChoiceForm").appendChild(lb);
	}
	var attributes = ['title','description','url'];
		for(var a in attributes){
			rb = document.createElement("input");
			rb.setAttribute("type", "radio");
			rb.setAttribute("id", "attribute_"+(parseInt(a)+1));
			rb.setAttribute("name", "attributeChoice");
			rb.setAttribute("value", attributes[a]);
			document.getElementById("attributeChoiceForm").appendChild(rb);

			lb = document.createElement("label");
			lb.setAttribute("for", "attribute_"+(parseInt(a)+1));
			lb.innerHTML = attributes[a];
			document.getElementById("attributeChoiceForm").appendChild(lb);
			if(a == 0){
				rb.setAttribute("checked","true");
			}
		}
}

window.qdb = function(tbl, id, attr){
    //query the database for table, primary key id, attribute
    db.transaction(tbl).objectStore(tbl).get(id).onsuccess = function(event) {
          //alert(attr+" for item is " + event.target.result[attr]);
          console.log(attr+" for item is " + event.target.result[attr]);
          return event.target.result[attr];
    };
}
window.deletedb = function(){
    input_box=confirm("Are you SURE you want to DELETE ALL THE SITE DATA?!");
    if (input_box==true){
        if(db){
            db.close();
            var req = indexedDB.deleteDatabase('video_idxdb');
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
    }else{
        return;
    }               
}

window.clearObjStore = function(objs){
    var objectStore = db.transaction([objs], "readwrite").objectStore(objs).clear();
    DB[objs] = Array();
}
window.readObj = function(obj){
    if(!db.objectStoreNames.contains(obj)){console.log("objectstore not found");return;}
    db.transaction([obj], 'readonly').objectStore(obj).count().onsuccess = function(e) {console.log(e.target.result+' records'); }
    var objectStore = db.transaction(obj).objectStore(obj);
    objectStore.openCursor().onsuccess = function(event) {
      var cursor = event.target.result;
      if (cursor) {
        //console.log(cursor.value);
        for(var field in cursor.value) {
            console.log(field+"="+cursor.value[field]);
        }
        cursor.continue();
      }else{
        //alert("No more entries!");
      }
    };          
}
//(tbl,ke, keval, col, colval)
window.updatedb = function(ipt){
	//tbl, ke, keval, col, colval, newobjval
	//console.log(ipt); return;
	var tbl = 'videos';
	var ke = 'id';
	var keval = ipt.dataset.itmid;
	var col = ipt.id;
	var colval = ipt.value;
	var newobjval;
	for(var v in DB[tbl]){
		if(DB[tbl][v].id == keval){
			DB[tbl][v][col] = colval;
			newobjval = DB[tbl][v];

		}
	}
    db.transaction(tbl).objectStore(tbl).get(keval).onsuccess = function(event) {
        var data = event.target.result;
        if(data === undefined){
            console.log("add "+col+" : "+colval);
            var dataObjectStore = db.transaction([tbl], "readwrite").objectStore(tbl).add(newobjval);
            if(DB[tbl] === undefined){
                DB[tbl] = [];
                DB[tbl][0] = newobjval;
            }else{
                DB[tbl].push(newobjval);
            }
            return;
        }else{
            console.log("update "+tbl+" - "+col+" : "+colval);
               data = newobjval;
            var dataObjectStore = db.transaction([tbl], "readwrite").objectStore(tbl);
            var requestUpdate = dataObjectStore.put(data);
            //DB[tbl][0] = newobjval;
            //console.log(data);
            for(var o in DB[tbl]){
                if(DB[tbl][o][ke] === undefined){continue;}
                if(DB[tbl][o][ke] == keval){
                    console.log("update "+col+" "+ke+" "+keval);
                    DB[tbl][o] = newobjval;
                }else{
                    console.log("-- "+DB[tbl][o][ke]);
                }
            }
            requestUpdate.onerror = function(event) {
                // Do something with the error
                console.log(col+" update error");
            };
            requestUpdate.onsuccess = function(event) {
                // Success - the data is updated!
                console.log(col+" update success");
            };
        }
    }
}
window.listObjStores = function(){
	console.log(db.objectStoreNames);
}
window.deletevideo = function(p){
	var i = 0;
	for(var v in DB['videos']){
		if(DB['videos'][v].id == Number(p)){
			DB['videos'].splice(i,1);
			document.getElementById('_'+p+'div').style.display = 'none';
			var request = db.transaction(["videos"], "readwrite").objectStore("videos").delete(p);
			request.onsuccess = function(event) {
			  // It's gone!
			  console.log("success");
			};
			request.onerror = function(event) {
			  // It's gone!
			  console.log("error");
			};
		}
		i++;
	}
}
</script>
</body>
</html>
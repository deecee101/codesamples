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
$apikey = 'AIzaSyBKuwkFiu_RFWZhDql648Uk-ssCG84tBVg';

include_once($spath.'php/functions.php');
if(isset($_POST['axn']) && $_POST['axn'] != NULL){
	switch($_POST['axn']){
		case "setlocation":
			$err['flag'] = NULL;
			if(validate($_POST['lat'], 'loose') == false){$err['flag']=$err['loose']." : latitude" ;};
			if(validate($_POST['lon'], 'loose') == false){$err['flag']=$err['loose']." : longitude" ;};
			if(validate($_POST['locationpermission'], 'title') == false){$err['flag']=$err['loose']." : locationpermission" ;};
			if(validate($_POST['address'], 'loose') == false){$err['flag']=$err['loose']." : address" ;};
			if($err['flag'] != NULL){
				echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
			}
			/*$my_query = "UPDATE users SET address = '".$_POST['address']."', latlng = '".$_POST['lat'].",".$_POST['lon']."' WHERE id = '".$_SESSION['user']['id']."';";
			
			try {
			    $db->exec($my_query);
			    echo '{"status":"ok","function":"update location on"}';  exit;
			    }
			catch(PDOException $e)
			    {
			    echo '{"status":"error","error":"'.$e->getMessage().'"}';
			    }*/
			echo '{"status":"ok","address":"current address : '.$_POST['address'].'"}';  exit;
			break;
		default:
	}
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Geocoding</title>
	<script src="https://cdn.jsdelivr.net/npm/places.js@1.4.18"></script>
	<script src="https://maps.google.com/maps/api/js?key=<?=$apikey; ?>&sensor=false" type="text/javascript"></script>
	<style type="text/css">
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
} 		
	</style>
</head>
<body style="text-align:center">
<h1>Geolocation/GeoCoding/Maps API</h1>
1) toggle the switch to get your current location<br>
2) enter in a destination and choose<br>
3) click the GET DIRECTIONS button<br>
<div style="margin:0px auto;text-align:left;width:80%;min-width:310px" >
	<div style="display:block;width:100%" >
		get current location : 
		<label class="switch">
		  <input type="checkbox" onclick="toggleLocation()" value="false" id="startAddressCheckBox" >
		  <span class="slider round"></span>
		</label><br>
	 	<div style="display:block;width:100%" id="startAddressLocationOutput"></div>
	 	<div style="display:block;width:100%" id="lat"></div>
	 	<div style="display:block;width:100%" id="lng"></div>
	</div>
	<span style="margin:3px;display:inline-block;background:white;width:100%" >
		destination:<br><input name="destinationaddress" id="destinationaddress" type="text" placeholder="Street Address" style="width:100%" value="" >
	 	<div style="display:block;width:100%" id="destinationLocationOutput"></div>
	 	<div style="display:block;width:100%" id="dlat"></div>
	 	<div style="display:block;width:100%" id="dlng"></div>
	</span>
</div>
<button onclick="showDirections()"   STYLE="font-size: x-large;cursor:pointer" title="get directions" >Get Directions </button><br>
<div style="margin:0px auto;text-align:left;width:80%;min-width:310px" >
    <div id="map_canvas" STYLE="height:300px;max-width:500px;min-width:300px;width:100%" ></div> &nbsp;&nbsp;&nbsp;
    <DIV ID="directionsPanel" STYLE="max-width:500px;min-width:300px;width:100%;background:white"></DIV>
</div>
<script>
var startAddress = new Object();
var destination = new Object();
var placesAutocomplete = places({
	container: document.querySelector('#destinationaddress')
});
placesAutocomplete.on('change', function showResults(e){
	document.getElementById("destinationLocationOutput").innerHTML = e.suggestion.value;
	document.getElementById("dlat").innerHTML = "latitude: "+e.suggestion.latlng.lat;
	document.getElementById("dlng").innerHTML = "longitude: "+e.suggestion.latlng.lng;
	destination.address = e.suggestion.value;
	destination.lat = e.suggestion.latlng.lat;
	destination.lng = e.suggestion.latlng.lng;

});
var locationpermission = false;
var locationoptions = {
	enableHighAccuracy: true,
	timeout: 5000,
	maximumAge: 0
};

function locationsuccess(pos) {
  var crd = pos.coords;

  /*console.log('Your current position is:');
  console.log(`Latitude : ${crd.latitude}`);
  console.log(`Longitude: ${crd.longitude}`);
  console.log(`More or less ${crd.accuracy} meters.`);*/
  //console.log(pos);
  fetch('https://nominatim.openstreetmap.org/reverse?lat='+crd.latitude+'&lon='+crd.longitude+'&format=jsonv2&email=admin@dart.gallery').then(function(response3){
  	return response3.json();
  }).then(function(response4){
  		console.log(response4);
  		if(typeof response4.error != 'object'){
  			//console.log(response4);
  			startAddress.address = response4.display_name;
  			startAddress.lat = crd.latitude;
  			startAddress.lng = crd.longitude;
  			//if we want to validate the address + coordingates and save them to a user profile in a database :
  			fetch('<?=$self; ?>', {
	  			method: 'post',
	  		    headers: {
	  		      'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
	  		      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
	  		      },
	  		    body:'axn=setlocation&locationpermission=true&lat='+crd.latitude+'&lon='+crd.longitude+'&address='+response4.display_name
	  		}).then(function(response_3){
	  			return response_3.json();
	  		}).then(function(response_4){
	  			if(response_4.status.toLowerCase() == 'ok'){
	  				console.log(response_4);
	  				document.getElementById("startAddressLocationOutput").innerHTML = 'Current Address : '+response_4.address;
	  				document.getElementById("lat").innerHTML = "latitude: "+crd.latitude;
	  				document.getElementById("lng").innerHTML = "longitude: "+crd.longitude;
	  			}else{
	  				console.log(response_4.error);
	  			}
	  		});
  		}else{
  			console.log('error');
  		}
  });
}

function locationerror(err) {
  console.warn(`ERROR(${err.code}): ${err.message}`);
  document.getElementById("startAddressLocationOutput").innerHTML = err.message;
}
window.toggleLocation = function(){
	document.getElementById("startAddressLocationOutput").innerHTML = 'loading...';
	if(locationpermission == false){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(locationsuccess, locationerror, locationoptions);
		} else {
			x.innerHTML = "Geolocation is not supported by this browser.";
		}
		locationpermission = true;
	}else{
		locationpermission = false;
	}
}
if(typeof google != 'undefined'){
  //console.log('got google');
  var myMap;
  var app;
  var geocoder;
  var markersArray = [];
  var infowindow;
  var directionDisplay;
  var directionsService = new google.maps.DirectionsService();

  function init() {
    app = new App();
  }
  function App() {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': '44702'}, function(results, status){
      if (status == google.maps.GeocoderStatus.OK) {
        var centerstart =  new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
        this.myMap = null;
        directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers:true});
        
        var myOptions = {
          zoom: 15,
          center: centerstart,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
        };
        this.myMap = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        window.myMap = this.myMap;
        directionsDisplay.setMap(this.myMap);
        var pcenterstart =  new google.maps.LatLng(40.855562,-81.422835);
        var panoramaOptions = {
          position: pcenterstart,
          pov: {
          heading: 47,
          pitch: 0,
          zoom: 1
          }
        };
        var panorama = new  google.maps.StreetViewPanorama(document.getElementById("sv"),panoramaOptions);
        this.myMap.setStreetView(panorama);
      }
    }); 
    App.prototype.getdirxns = function() {
      //var start = document.getElementById("start").value;
      //var end = document.getElementById("end").value;
      //console.log(strt+" : "+fin);
      this.deleteOverlays();
      var request = {
        origin:startAddress.address, 
        destination:destination.address,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
      };

      directionsService.route(request, function(result, status) {
        directionsDisplay.setPanel(document.getElementById("directionsPanel"));
        if(status == google.maps.DirectionsStatus.OK) {
          console.log('status ok');
          document.getElementById("directionsPanel").innerHTML = '';
          directionsDisplay.setDirections(result);
          if(result.routes.length > 0) {
            console.log(' routlength exists');
            var starta = new google.maps.MarkerImage("<?php echo $hpath; ?>mapmarkers/letter_a.png",
              new google.maps.Size(50,50),
              new google.maps.Point(0,0),
              new google.maps.Point(25,50)
            );
            var startMarker = new google.maps.Marker({
                        position: result.routes[0].legs[0].start_location,
                        map: myMap,
                        icon: starta
                    });
            markersArray.push(startMarker); 
            var finb = new google.maps.MarkerImage("<?php echo $hpath; ?>mapmarkers/letter_b.png",
              new google.maps.Size(50,50),
              new google.maps.Point(0,0),
              new google.maps.Point(25,50)
            );
            var endMarker = new google.maps.Marker({
                        position: result.routes[0].legs[result.routes[0].legs.length-1].end_location,
                        map: myMap,
                        icon: finb
                    }); 
            markersArray.push(endMarker); 
            
          }else{
            console.log(' routlength  !exists');  
          }
        }else{
          console.log('error1');  
        }
      }); 
    }

    App.prototype.deleteOverlays = function(){
      //infowindow.close();

      if (markersArray && markersArray.length > 0) {
        for (i in markersArray) {
          if(typeof markersArray[i] == 'object'){
            //console.log(markersArray[i]);
            markersArray[i].setMap(null);
          }
          
          //markersArray[i].setMap(null);
        }
        markersArray.length = 0;
      }
    }
  }
  /*App.prototype.findaddress = function(addr){
    this.deleteOverlays();
    directionsDisplay.set('directions', null);
    scroll2('map_canvas');
    var address = addr;
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status){
      if (status == google.maps.GeocoderStatus.OK) {
        //console.log(results[0].geometry.location.lat()+', '+results[0].geometry.location.lng());
        //console.log(results[0].geometry.location.toString());
        //alert("Geocode was not successful for the following reason: " + status);
        //return(results[0].geometry.location.toString());
        myMap.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
          map: myMap, 
          title:'El Campesino',
          icon: '<?php echo $hostroot; ?>imgs/mapmarkers/letter_x.png',
          position: results[0].geometry.location
        });
        infowindow = new google.maps.InfoWindow({
          content: addr
        });
        directionsDisplay.setMap(this.myMap);
        infowindow.open(myMap,marker);
        //---streetview stuff
        //document.getElementById(div).innerHTML = "<A onclick=\"showstreetview('streetview"+String(window.sv_idx)+"', coords, "+String(point.lat())+", "+String(point.lng())+")\" STYLE='cursor:pointer' >see streetview</A>";
        //document.getElementById(div+'arrow').src= './imgs/arrr_.png';
        //window.sv_idx++;  
        markersArray.push(marker);
        endaddr = address;
      }
    }); 
  }*/

}
window.showDirections = function(){
  document.getElementById("directionsPanel").innerHTML = 'loading...';
  if(typeof startAddress.lat != 'number' || typeof startAddress.lng != 'number'){
  	alert('need a start address'); return;
  }
  if(typeof destination.lat != 'number' || typeof destination.lng != 'number'){
  	alert('need a destination address'); return;
  }
    App.prototype.getdirxns();
    return;
}
window.addEventListener('load', (event) => {
	init();
	document.getElementById("startAddressCheckBox").checked = false;
	document.getElementById("destinationaddress").value = '';

});
</script>
</body>
</html>
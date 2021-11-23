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
$keywords = '';
include_once($spath.'php/functions.php');
if(!isset($_SESSION['weather_user'])){
  $_SESSION['weather_user'] = array('id'=>rand(500000, 1000000),'uname'=>'user','email'=>'null@null.com','avatar'=>'imgs/user.png','address'=>'null','locationpermission'=>'null','access'=>'g','rating'=>'g','age'=>'0','latlng'=>'40.689247,-74.044502','locationpermission'=>false); //is this a public user?
}
$_SESSION['apis']['openweather']['api_key'] = "d691aa03c3068570eb850998c48e3f81";
if(count($_POST) == 0){
  $_POST = json_decode(file_get_contents('php://input'), true); //for php 7
}

if(isset($_POST['axn']) && $_POST['axn'] != NULL){
  switch($_POST['axn']){
    case "setlocation":
      $err['flag'] = NULL;
      if(validate($_POST['lat'], 'loose') == false){$err['flag']=$err['loose']." : latitude" ;};
      if(validate($_POST['lon'], 'loose') == false){$err['flag']=$err['loose']." : longitude" ;};
      if(validate($_POST['locationpermission'], 'az') == false){$err['flag']=$err['loose']." : locationpermission" ;};
      if(validate($_POST['address'], 'loose') == false){$err['flag']=$err['loose']." : address" ;};
      if($err['flag'] != NULL){
        echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
      }
      if($_POST['locationpermission'] == 'true'){
            $_SESSION['weather_user']['locationpermission'] = 'true';
            $_SESSION['weather_user']['address'] = $_POST['address']; $_SESSION['weather_user']['latlng'] = $_POST['lat'].",".$_POST['lon'];
            echo '{"status":"ok","function":"update location on","location":"'.addslashes($_POST['address']).'"}';  exit;
        }else if($_POST['locationpermission'] == 'false'){
              $_SESSION['weather_user']['locationpermission'] = 'false';
              $_SESSION['weather_user']['address'] = ''; $_SESSION['weather_user']['latlng'] = '';
              echo '{"status":"ok","function":"update location off","location":""}';  exit;
              //}
        }else{exit;}
      break;
    case "getweather":
      $err['flag'] = NULL;
      if(validate($_POST['lat'], 'loose') == false){$err['flag']=$err['loose'].":latitude";};
      if(validate($_POST['long'], 'loose') == false){$err['flag']=$err['loose'].":longitude";};
      if(isset($_POST['cast']) && $_POST['cast'] != ''){if(validate($_POST['cast'], 'az') == false){$err['flag']=$err['loose'].":forecast";};}
      if($err['flag'] != NULL){
        echo '{"status":"error","error":"'.$err['flag'].'"}';  exit;
      }
      $uptodate = false;
      switch($_POST['cast']){
        case "fiveday":
          $weatherfilename = $spath.'weatherdata/'.$_POST['lat'].','.$_POST['long'].'_fiveday.json';
          $url = 'https://api.openweathermap.org/data/2.5/forecast?lat='.$_POST['lat'].'&lon='.$_POST['long'].'&units=imperial&appid='.$_SESSION['apis']['openweather']['api_key'];
          break;
        case "hourly":
          $weatherfilename = $spath.'weatherdata/'.$_POST['lat'].','.$_POST['long'].'_hourly.json';
          if(is_file($weatherfilename)){
            $strJsonFileContents = file_get_contents($weatherfilename);
            // Convert to array 
            $array = json_decode($strJsonFileContents, true);
            $stime = strtotime('-3 hour');
            $etime = time();
            $ctime = $array['timedate'];
            $range = (($ctime >= $stime) && ($ctime <= $etime));
            $cast = $array['forecasttype'];
            if($range == true && $cast == $_POST['cast']){
              $uptodate = true;
            }else{
              unlink($weatherfilename);
              $uptodate = false;
            }
          }
          if($uptodate == false){
            $UserAgent = "dcwill dcwill@dart.gallery";
            $opts = array(
             'http'=>array(
             'method'=>"GET",
             'header'=>"Accept: application/geo+json;version=1\r\n" .
             "User-agent: $UserAgent\r\n"
             )
            );
            $context = stream_context_create($opts);
            $lat = $_POST['lat'];
            $lng = $_POST['long'];
            $pointMetaUrl = "https://api.weather.gov/points/$lat,$lng";
            $pointMetaFile = file_get_contents($pointMetaUrl, false, $context);
            $pointMetaArray = json_decode($pointMetaFile, true);
            @mkdir($spath.'weatherdata/');
            
            $hourlyData = $pointMetaArray["properties"]["forecastHourly"];
            $hourlyDataFile = file_get_contents($hourlyData, false, $context);
            $hourlyDataArray = json_decode($hourlyDataFile, true);
            if(count($hourlyDataArray) != 4){
              echo '{"status":"error","error":"site down"}';  exit;
            }
            $hourlyDataArray['timedate'] = time(); $hourlyDataArray['forecasttype'] = $_POST['cast'];
            $json = json_encode($hourlyDataArray);

            $fp = fopen($weatherfilename,"w"); 
            fwrite($fp, $json);
            fclose($fp); 

            echo '{"status":"ok","file":"weatherdata/'.$_POST['lat'].','.$_POST['long'].'_hourly.json'.'"}';  exit;
          }
          break;
        default:
          $weatherfilename = $spath.'weatherdata/'.$_POST['lat'].','.$_POST['long'].'_current.json';
          $url = 'https://api.openweathermap.org/data/2.5/weather?lat='.$_POST['lat'].'&lon='.$_POST['long'].'&units=imperial&appid='.$_SESSION['apis']['openweather']['api_key'];
      }
      if(is_file($weatherfilename)){
        $strJsonFileContents = file_get_contents($weatherfilename);
        // Convert to array 
        $array = json_decode($strJsonFileContents, true);
        $stime = strtotime('-3 hour');
        $etime = time();
        $ctime = $array['timedate'];
        $range = (($ctime >= $stime) && ($ctime <= $etime));
        $cast = $array['forecasttype'];
        if($range == true && $cast == $_POST['cast'] && $_POST['cast'] != 'current'){
          $uptodate = true;
        }else{
          unlink($weatherfilename);
          $uptodate = false;
        }
      }
      if($uptodate == false){
        $data = file_get_contents($url); // put the contents of the file into a variable
        //echo $data; exit;
        @mkdir($spath.'weatherdata/');
        $arr = json_decode($data, TRUE);
        $arr['timedate'] = time(); $arr['forecasttype'] = $_POST['cast'];
        $json = json_encode($arr);

        $fp = fopen($weatherfilename,"w"); 
        fwrite($fp, $json);
        fclose($fp); 
      }
      echo '{"status":"ok","file":"'.str_replace($spath,'',$weatherfilename).'"}';  exit;
      break;
    default:
  }
  exit;
}
 ?><html><head><title>Codesamples Weather Service</title>
<script src="<?=$hpath; ?>js/jquery.js" ></script>  
<script src="<?=$hpath; ?>js/gsap/gsap.min.js" ></script>  
<script src="<?=$hpath; ?>js/functions.js" ></script>  
<script src="https://cdn.jsdelivr.net/npm/places.js@1.4.18"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $hpath; ?>ui/buttons.css">
<link rel="stylesheet" type="text/css" href="<?php echo $hpath; ?>ui/fonticons/css/font-awesome.min.css">
<style type="text/css">  
</style>
</head>
<body>
<div  style="display:block;width:100%"><h1>Weather Codesample</h1><br>
enter an address into the input box, or click the map button to get the current geolocation<br>
there are buttons to display the current, hourly, and 5 day weather forecasts</div>
<center><div id="dateDisplay" class="dateStyle" ><br></div><div id="clockDisplay" class="clockStyle"></div></center>
<div id="weatherdiv" style="display:block;text-align:center">
  <input name="useraddress" id="useraddress" type="text" placeholder="Street Address" style="width:100%" value="" ><button class="button button-block button-inverse button-rounded" onclick="getLocation()" ><i class="fa fa-map-marker" ></i></button><br>
  <div id="error" style="color:red;font-size:small;display:block;width:100%"></div>
  <div id="currweatherdiv" style="display:none;opacity:0;margin:0px auto">
    the weather in : <div id="currweatherlocation" ></div><br>
    <div style="display:inline-block" ><div id="currtemp" style="font-size:x-large;display:inline" ></div>°</div>
    <div id="currweathericon" ></div> <div id="currweatherdescr" ></div><br>
    <div style="font-size:x-small;display:block" >todays high : <span id="currtemphigh" ></span> &nbsp; &nbsp; todays low : <span id="currtemplow" ></span></div>
    <div style="font-size:x-small;display:block" >sun up : <span id="sunup" ></span> &nbsp; &nbsp; sun down : <span id="sundown" ></span></div>
  </div>
</div>
<div id="forecastdiv" style="display:block;text-align:center">
  <button class="button button-small button-rounded button-primary" onclick="weatherforecast(currlocation)" >current</button> &nbsp; <button class="button button-small button-rounded button-primary" onclick="weatherforecast(currlocation,'hourly')" >hourly</button> &nbsp; <button class="button button-small button-rounded button-primary" onclick="weatherforecast(currlocation,'fiveday')" >5 day</button>
  <div style="display:flex;flex-flow:row wrap;justify-content:space-around;align-items:center" id="forecast" ></div>
</div><br>

<script type="text/javascript">
window.togglefade = function(div,dstyle='inline',ovfl=false){
  var d = document.getElementById(div);
  if(d.style.display == 'none'){
    TweenMax.to(d, 1, {opacity:1,onStart:function(){d.style.display=dstyle;if(ovfl==true){document.body.style.overflow="hidden";}}}); 
  }else{
    TweenMax.to(d, 1, {opacity:0,onComplete:function(){d.style.display='none';if(ovfl==true){document.body.style.overflow="auto";}}});
  }
}
function geoerror(err) {
  console.warn(`ERROR(${err.code}): ${err.message}`);
  document.getElementById("error").innerHTML = err.message;
  setTimeout('getLocation()',1000);
}
var options = {
  enableHighAccuracy: true,
  timeout: 5000,
  maximumAge: 0
};
window.currlocation;
window.getLocation = function(ipt=null) {
  if(ipt == null){
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(assignlocation,geoerror,options);
    } else {
      console.log("Geolocation is not supported by this browser.");
    }
  }else{
    currlocation = {coords:{latitude:ipt.lat,longitude:ipt.lng}};
    //console.log(clocation);
    weatherforecast(currlocation);
  }
  
}
window.assignlocation = function(location){
  currlocation = location;
  locationsuccess(location);
  weatherforecast(currlocation);
}
window.weatherforecast = function(location,cast='current'){
  //console.log(location);
  //console.log(location.coords.latitude+' : '+location.coords.longitude);
  var lat = location.coords.latitude; var long = location.coords.longitude;
  //fetch('https://samples.openweathermap.org/data/2.5/forecast/hourly/?lat='+lat+'&lon='+long+'&appid=<?=$_SESSION['apis']['openweather']['api_key']; ?>').
  fetch('<?=$self; ?>', {
    method: 'post',
      headers: {
        'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        },
      body: 'axn=getweather&lat='+lat+'&long='+long+'&cast='+cast
  }).then(function(response3){
    return response3.json();
  }).then(function(response4){
    if(response4.status == 'ok'){
      //console.log('<?=$hpath; ?>'+response4.file);
      displayweather('<?php echo $hpath; ?>'+response4.file);
    }else{
      console.log(response4.error);
      document.getElementById("error").innerHTML = response4.error;
    }
  });
}
window.displayweather = function(wf){
  //console.log(wf);
  document.getElementById("forecast").innerHTML = '';
  document.getElementById("currweathericon").innerHTML = '';
  fetch(wf, {
    method: 'get',
    headers: {
      'Accept': 'application/json'
    }
  }).then(response => {
      return response.json()
  }).then(response4 => {
      // Work with JSON data here
      //console.log(response4);
      wicon = 'sunny';


    //console.log(response4.forecasttype);
    switch(response4.forecasttype){
      case "current":
          var currweathercode = String(response4.weather[0].id).substring(0,1);
        var currcloudcode = response4.weather[0].id;
        //console.log(response4.sys.sunrise+' : '+response4.sys.sunset);
        var wicon = weathericon(currweathercode,currcloudcode);
        document.getElementById("currweatherlocation").innerHTML = response4.name;
        document.getElementById("currweatherdescr").innerHTML = response4.weather[0].description;
        document.getElementById("currtemp").innerHTML = response4.main.temp;
        document.getElementById("currtemphigh").innerHTML = response4.main.temp_max;
        document.getElementById("currtemplow").innerHTML = response4.main.temp_min;
        var su = new Date(parseFloat(response4.sys.sunrise)*1000);
        var sd = new Date(parseFloat(response4.sys.sunset)*1000);
        document.getElementById("sunup").innerHTML =  su.to12HourString();
        document.getElementById("sundown").innerHTML =  sd.to12HourString();

        $('<img/>',{
            id: 'currweathericon',
            src:'<?=$hpath; ?>imgs/weather/'+wicon+'.png',
            css:{maxWidth:"75px",maxHeight:"75px"}
        }).appendTo('#currweathericon');
        if(document.getElementById("currweatherdiv").style.display == 'none'){togglefade('currweatherdiv','block');}
        break;
      case "fiveday":
        //console.log(response4.list);
        for(var a in response4.list){
          //console.log(response4.list[a].dt_txt.substring(11));
          if(response4.list[a].dt_txt.substring(11) != '12:00:00'){continue;/*console.log(response4.list[a].dt_text.substring(-8));*/}
          //console.log(response4.list[a].main.temp);
          //console.log(response4.list[a].main.temp);
                var dt = new Date(response4.list[a].dt_txt);
                //var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear()+'  '+dt.to12HourString();
                //console.log(dt);
                var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear();//+'  '+dt.to12HourString();
                var tempreadout = response4.list[a].main.temp+'° F '+response4.list[a].weather[0].description;
                
            var currweathercode = String(response4.list[a].weather[0].id).substring(0,1);
          var currcloudcode = response4.list[a].weather[0].id;
          //console.log(currweathercode+','+currcloudcode);
                var wicon = weathericon(currweathercode,currcloudcode);

                $('<div/>',{
              id: 'weatheroutput_'+a,
              class:"forecastpanel",
              css: {display:"inline-block"}
          }).appendTo('#forecast');
            $('<span/>',{text:datereadout,css:{fontSize:"0.75em",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            //$('<span/>',{text:timereadout,css:{fontSize:"0.75em",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            $('<span/>',{text:tempreadout,css:{display:"block"}}).appendTo('#weatheroutput_'+a);
                $('<img/>',{
              id: a+'_hourlyweathericon',
              src:'<?=$hpath; ?>imgs/weather/'+wicon+'.png',
              css:{maxWidth:"50px",maxHeight:"50px"}
          }).appendTo('#weatheroutput_'+a);

                //console.log(datereadout);
                //console.log(tempreadout);
                
                //var wicon = weathericon(spl);
                //console.log('icon : '+wicon);
                //console.log();
        }
        break;
      case "hourly":
        //console.log(response4);
        //console.log('response4:');
        var date = new Date();
                var tzoffset = (new Date()).getTimezoneOffset() * 60000;
                var n = new Date(Date.now()).setMinutes(0,0,0); //console.log(n); return;
                var currdate = (new Date(n - tzoffset)).toISOString().slice(0, -5);
                var hnum = 0;
                for(var a in response4.properties.periods){
                  //console.log(response4.properties.periods[a].startTime.substring(0,19)+','+currdate);
                  if(response4.properties.periods[a].startTime.substring(0,19) == currdate){
                    //console.log(response4.properties.periods[a].startTime);
                    var dt = new Date(response4.properties.periods[a].startTime);
                    //var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear()+'  '+dt.to12HourString();
                    //console.log(dt,dt.to12HourString());
                    var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear();
                    var timereadout = dt.to12HourString();
                    var tempreadout = response4.properties.periods[a].temperature+'° '+response4.properties.periods[a].temperatureUnit;
                    //console.log(response4.properties.periods[a].icon);
                    var tempdescription = response4.properties.periods[a].shortForecast;

                    var spl = response4.properties.periods[a].icon.split(',')[0];
                    //console.log('//'+spl);
                    //console.log(spl.includes('/day'));
                    //console.log(spl.includes('/night'));
                    if(spl.includes('day/') == true){
                      spl = spl.split('day/')[1];
                      //console.log(spl.split('day/'));
                    }
                    if(spl.includes('night/') == true){
                      spl = spl.split('night/')[1];
                      //console.log(spl.split('night/'));
                    }
                    spl = spl.split('?')[0];
                    //console.log(datereadout);
                    //console.log(tempreadout);
                    
                    var wicon = weathericon(spl);
                    //console.log('icon : '+wicon);
                  $('<div/>',{
                id: 'weatheroutput_'+a,
                class:"forecastpanel",
                css: {display:"inline-block"}
            }).appendTo('#forecast');
            $('<span/>',{text:datereadout,css:{fontSize:"0.5em",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            $('<span/>',{text:timereadout,css:{fontWeight:"bold",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            $('<span/>',{text:tempreadout,css:{fontSize:"1.25em",display:"block"}}).appendTo('#weatheroutput_'+a);
            $('<span/>',{text:tempdescription,css:{fontSize:"0.5em",display:"block"}}).appendTo('#weatheroutput_'+a);
                  $('<img/>',{
                id: a+'_hourlyweathericon',
                src:'<?=$hpath; ?>imgs/weather/'+wicon+'.png',
                css:{maxWidth:"50px",maxHeight:"50px"}
            }).appendTo('#weatheroutput_'+a,);
                    //console.log(hnum);
                    hnum++;
                  }else if(hnum > 0){
                    //console.log(response4.properties.periods[a]);
                    var dt = new Date(response4.properties.periods[a].startTime);
                    //console.log(dt);
                    var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear();
                    var timereadout = dt.to12HourString();
                    var tempreadout = response4.properties.periods[a].temperature+'° '+response4.properties.periods[a].temperatureUnit;
                    //console.log(response4.properties.periods[a].icon);
                    var tempdescription = response4.properties.periods[a].shortForecast;
                    var spl = response4.properties.periods[a].icon.split(',')[0];
                    //console.log('//'+spl);
                    if(spl.includes('day/')){
                      spl = spl.split('day/')[1];
                    }else if(spl.includes('night/')){
                      spl = spl.split('night/')[1];
                    }
                    spl = spl.split('?')[0];
                    //console.log(datereadout);
                    //console.log(tempreadout);
                    //console.log(spl);
                    var wicon = weathericon(spl);
                    //console.log('icon : '+wicon);
                    //console.log(hnum);
                  $('<div/>',{
                id: 'weatheroutput_'+a,
                class:"forecastpanel",
                css: {display:"inline-block"}
            }).appendTo('#forecast');
            $('<span/>',{text:datereadout,css:{fontSize:"0.5em",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            $('<span/>',{text:timereadout,css:{fontWeight:"bold",display:"block"}}).appendTo('#weatheroutput_'+a);
            //$('<br/>').appendTo('#weatheroutput_'+a);
            $('<span/>',{text:tempreadout,css:{fontSize:"1.25em",display:"block"}}).appendTo('#weatheroutput_'+a);
            $('<span/>',{text:tempdescription,css:{fontSize:"0.5em",display:"block"}}).appendTo('#weatheroutput_'+a);
                  $('<img/>',{
                id: a+'_hourlyweathericon',
                src:'<?=$hpath; ?>imgs/weather/'+wicon+'.png',
                css:{maxWidth:"50px",maxHeight:"50px"}
            }).appendTo('#weatheroutput_'+a,);
                    hnum++;
                    if(hnum == 12){break;}
                  }else{
                    //console.log('no match : '+response4.properties.periods[a].startTime.substring(0,19)+'!='+currdate);
                  }
                  //console.log();
                }
        break;
      default:
    }
  }).catch(err => {
      // Do something for an error here
      console.log(err);
      document.getElementById("error").innerHTML = err;
  })
}

window.renderTime = function() {
  var currentTime = new Date();
  var diem = "AM";
  var h = currentTime.getHours();
  var m = currentTime.getMinutes();
    var s = currentTime.getSeconds();
  setTimeout('renderTime()',1000);
    if (h == 0) {
    h = 12;
  } else if (h > 12) { 
    h = h - 12;
    diem="PM";
  }
  if (h < 10) {
    h = "0" + h;
  }
  if (m < 10) {
    m = "0" + m;
  }
  if (s < 10) {
    s = "0" + s;
  }
    var myClock = document.getElementById('clockDisplay');
  myClock.textContent = h + ":" + m + ":" + s + " " + diem;
  myClock.innerText = h + ":" + m + ":" + s + " " + diem;
}
renderTime();
var locationpermission = '<?=$_SESSION['weather_user']['locationpermission']; ?>';
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
  fetch('https://nominatim.openstreetmap.org/reverse?lat='+crd.latitude+'&lon='+crd.longitude+'&format=jsonv2&email=<?=$_SESSION['weather_user']['email']; ?>').then(function(response3){
    return response3.json();
  }).then(function(response4){
    //if(response4.status.toLowerCase() == 'ok'){
      console.log(response4);
      if(typeof response4.error != 'object'){
        //console.log(response4);
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
            document.getElementById("useraddress").value = response_4.location;
          }else{
            console.log(response_4.error);
            document.getElementById("error").innerHTML = response_4.error;
          }
        });
      }else{
        console.log('error');
        document.getElementById("error").innerHTML = error;
      }
  });
}

function locationerror(err) {
  console.warn(`ERROR(${err.code}): ${err.message}`);
  document.getElementById("error").innerHTML = err.message;
}

window.addEventListener('load', (event) => {
  //getLocation();
  var dt = new Date();
    var datereadout = dt.getDayName()+' '+dt.getMonthName()+' '+dt.getDate()+' '+dt.getFullYear();
    document.getElementById("dateDisplay").innerHTML = datereadout;
    <?php if(isset($_SESSION['weather_user']['address']) &&$_SESSION['weather_user']['address'] != NULL ){ 
      $x =explode(',',$_SESSION['weather_user']['latlng']);
      ?>
      window.currlocation = {"address":"<?=$_SESSION['weather_user']['address']; ?>","coords":{"latitude": <?=$x[0]; ?>,"longitude":<?=$x[1]; ?>}}
      weatherforecast(currlocation);
    <?php } ?>
});
window.weathericon = function(currweathercode,currcloudcode=null){
  currweathercode = String(currweathercode);
  currcloudcode = String(currcloudcode);

    //console.log(currweathercode+','+currcloudcode);
    /*
    skc : Fair/clear
    wind_skc : Fair/clear and windy
    hot : hot
    --Fair/clear and windy

    few : A few clouds
    sct : Partly cloudy
    wind_few : A few clouds and windy
    wind_sct : Partly cloudy and windy

    bkn : Mostly cloudy
    wind_bkn : Mostly cloudy and windy
    wind_ovc : Overcast and windy
    fog : Fog/mist

    snow : Snow
    rain_snow : Rain/snow
    rain_sleet : Rain/sleet
    snow_sleet : Rain/sleet
    fzra : Freezing rain
    rain_fzra : Rain/freezing rain
    snow_fzra : Freezing rain/snow
    sleet : Sleet
    cold : Cold
    clizzard : Blizzard

    rain : Rain
    rain_showers : Rain showers (high cloud cover)
    rain_showers_hi : Rain showers (low cloud cover)
    tsra : Thunderstorm (high cloud cover)
    tsra_sct : Thunderstorm (medium cloud cover)
    tsra_hi : Thunderstorm (low cloud cover)

    tornado : Tornado
    hurricane : Hurricane conditions
    tropical_storm : Tropical storm conditions
    dust : Dust
    smoke : Smoke
    haze : Haze
    */


  switch(currweathercode){
    case "2":
      wicon = 'stormy';
      break;
    case "3":
      wicon = 'partlyrainy';
      break;
    case "5":
    case "rain" :
    case "rain_showers" :
    case "rain_showers_hi" :
    case "tsra" :
    case "tsra_sct" :
    case "tsra_hi" :
      wicon = 'rainy';
      break;
    case "6":
    case  "snow":
    case  "rain_snow":
    case  "rain_sleet":
    case  "snow_sleet":
    case  "fzra":
    case  "rain_fzra":
    case  "snow_fzra":
    case  "sleet":
    case  "cold":
    case  "blizzard":
      wicon = 'snowy';
      break;
    case "7":
    case "tornado" :
    case "hurricane" :
    case "tropical_storm" :
    case "dust" :
    case "smoke" :
    case "haze" :
      wicon = 'disaster';
      break;
    case "skc":
    case "wind_skc":
    case "hot":
      wicon = 'sunny';
      break;
    case "few":
    case "sct":
    case "wind_few":
    case "wind_sct":
      wicon ='partlycloudy';
      break;
    case "8":
      switch(currcloudcode){
        case "800":
          wicon = 'sunny';
          break;
        case "801":
        case "802":
          wicon = 'partlycloudy';
          break;
        case "803":
        case "804":
          wicon = 'cloudy';
          break;
      }
      break;
    default:
      wicon = 'partlycloudy';
  }
  return wicon;
}
var placesAutocomplete = places({
  container: document.querySelector('#useraddress')
});
placesAutocomplete.on('change', function showResults(e){
    currlocation = {coords:{latitude:e.suggestion.latlng.lat,longitude:e.suggestion.latlng.lng}};
    //console.log(clocation);
    weatherforecast(currlocation);
});
</script>
 </body></html>
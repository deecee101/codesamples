function sendpostonly(serverPage, dat){
	//alert(dat);
	var url = serverPage;
	
    //Look up the node we'll stick the text under.
//Create a boolean variable to check for a valid Internet Explorer instance.
		var xmlhttp = false;
		//Check if we are using IE.
		try {
		//If the Javascript version is greater than 5.
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			//alert ("You are using Microsoft Internet Explorer.");
		} catch (e) {
		//If not, then use the older active x object.
			try {
			//If we are using Internet Explorer.
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		//alert ("You are using Microsoft Internet Explorer");
			} catch (E) {
				//Else we must be using a non-IE browser.
			xmlhttp = false;
			}
		}
		//If we are using a non-IE browser, create a javascript instance of the object.
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
			xmlhttp = new XMLHttpRequest();
			//alert ("You are not using Microsoft Internet Explorer");
		}
		
		//var params = 'stat=sent&stat=sent';
	//alert(params.stat.value);
		xmlhttp.open("POST", url, true);
		
		//alert();
	//Send the proper header information along with the request
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//xmlhttp.setRequestHeader("Content-length", dat.length);
	//xmlhttp.setRequestHeader("Connection", "close");
	
	xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {		
			//alert(xmlhttp.responseText);
			//alert('hey');			
		}
	}
	//alert(params.elements);
	xmlhttp.send(dat);
}
// Global lookup arrays for base64 conversions
var enc64List, dec64List;
   
// Load the lookup arrays once
function initBase64( ) {
    enc64List = new Array( );
    dec64List = new Array( );
    var i;
    for (i = 0; i < 26; i++) {
        enc64List[enc64List.length] = String.fromCharCode(65 + i);
    }
    for (i = 0; i < 26; i++) {
        enc64List[enc64List.length] = String.fromCharCode(97 + i);
    }
    for (i = 0; i < 10; i++) {
        enc64List[enc64List.length] = String.fromCharCode(48 + i);
    }
    enc64List[enc64List.length] = "+";
    enc64List[enc64List.length] = "/";
    for (i = 0; i < 128; i++) {
        dec64List[dec64List.length] = -1;
    }
    for (i = 0; i < 64; i++) {
        dec64List[enc64List[i].charCodeAt(0)] = i;
    }
}
   
// Encode a string
function base64Encode(str) {
    var c, d, e, end = 0;
    var u, v, w, x;
    var ptr = -1;
    var input = str.split("");
    var output = "";
    while(end == 0) {
        c = (typeof input[++ptr] != "undefined") ? input[ptr].charCodeAt(0) : 
            ((end = 1) ? 0 : 0);
        d = (typeof input[++ptr] != "undefined") ? input[ptr].charCodeAt(0) : 
            ((end += 1) ? 0 : 0);
        e = (typeof input[++ptr] != "undefined") ? input[ptr].charCodeAt(0) : 
            ((end += 1) ? 0 : 0);
        u = enc64List[c >> 2];
        v = enc64List[(0x00000003 & c) << 4 | d >> 4];
        w = enc64List[(0x0000000F & d) << 2 | e >> 6];
        x = enc64List[e & 0x0000003F];
        
        // handle padding to even out unevenly divisible string lengths
        if (end >= 1) {x = "=";}
        if (end == 2) {w = "=";}
        if (end < 3) {output += u + v + w + x;}
    }
    // format for 76-character line lengths per RFC
    var formattedOutput = "";
    var lineLength = 76;
    while (output.length > lineLength) {
        formattedOutput += output.substring(0, lineLength) + "\n";
        output = output.substring(lineLength);
    }
    formattedOutput += output;
    return formattedOutput;
}
   
// Decode a string
function base64Decode(str) {
    var c=0, d=0, e=0, f=0, i=0, n=0;
    var input = str.split("");
    var output = "";
    var ptr = 0;
    do {
        f = input[ptr++].charCodeAt(0);
        i = dec64List[f];
        if ( f >= 0 && f < 128 && i != -1 ) {
            if ( n % 4 == 0 ) {
                c = i << 2;
            } else if ( n % 4 == 1 ) {
                c = c | ( i >> 4 );
                d = ( i & 0x0000000F ) << 4;
            } else if ( n % 4 == 2 ) {
                d = d | ( i >> 2 );
                e = ( i & 0x00000003 ) << 6;
            } else {
                e = e | i;
            }
            n++;
            if ( n % 4 == 0 ) {
                output += String.fromCharCode(c) + 
                          String.fromCharCode(d) + 
                          String.fromCharCode(e);
            }
        }
    } while (typeof input[ptr] != "undefined");
    output += (n % 4 == 3) ? String.fromCharCode(c) + String.fromCharCode(d) : 
              ((n % 4 == 2) ? String.fromCharCode(c) : "");
    return output;
}
   
// Self-initialize the global variables
initBase64( );
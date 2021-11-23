function makehash(){
	var char = "0123456789abcdefghijklmnopqrstuvwxyz";
	var ulen = Math.floor(Math.random( ) * (10 - 5 + 1)) + 5;
	//document.write(ulen);
	
	var hash = '';
	for (var i = 1; i <= ulen; i++) {
		var n = Math.floor(Math.random( ) * (char.length - 1 + 1)) + 1;
		
		hash += char.substring(n, n+1);
	}
	//hash = hash+'_';
	return hash;
}
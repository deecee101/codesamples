	// CREDITS:
// Gradient Background Fader, random colors
// by Peter Gehrig 
// Copyright (c) 2003 Peter Gehrig. All rights reserved.
// Permission given to use the script provided that this notice remains as is.
// Additional scripts can be found at http://www.fabulant.com
// info@fabulant.com
// 11/20/2003

// IMPORTANT: 
// If you add this script to a script-library or a script-archive 
// you are required to insert a highly visible link to http://www.fabulant.com
// right into the webpage where the script
// will be displayed.
/*
// Select fade-effect below:
// Set 1 if the background may fade from dark to medium 
// Set 2 if the background may fade from light to medium 
// Set 3 if the background may fade from very dark to very light light
// Set 4 if the background may fade from light to very light
// Set 5 if the background may fade from dark to very dark 

// What type of gradient should be applied Internet Explorer 5x or higher?
// Set "none" or "horizontal" or "vertical"

// Speed higher=slower
*/
///////////////////////////////////////////////////////////////////////
//////////////////WIF MAJER MODFICATIONS FROM BYTESHAMAN
//////////////////////////////////////////////////////////////////
// JavaScript Document

var coldivs = new Array();

function colordiv(obj, fefx, gtype, speed){
	this.obj = obj;
	this.fade_effect=fefx; 
	this.gradient_effect=gtype;
	this.speed=speed;
	this.darkmax=1;
	this.lightmax=127;
	this.hexc=['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F'];
	this.newred='';
	this.newgreen='';
	this.newblue='';
	this.oldred='';
	this.oldgreen='';
	this.oldblue='';
	this.redcol_1='';
	this.redcol_2 ='';
	this.greencol_1 ='';
	this.greencol_2 ='';
	this.bluecol_1 ='';
	this.bluecol_2 ='';
	this.oldcolor='';
	this.newcolor='';
	this.stepred=1;
	this.stepgreen=1;
	this.stepblue=1;
	this.range=(1-127);
	this.firsttime=true;
	this.setrandomcolor=setrandomcolor;
	this.fadebg=fadebg;
	function setrandomcolor(){
		if (this.fade_effect==1) {
			this.darkmax=1
			this.lightmax=127
		}
		if (this.fade_effect==2) {
			this.darkmax=127
			this.lightmax=254
		}
		if (this.fade_effect==3) {
			this.darkmax=1
			this.lightmax=254
		}
		if (this.fade_effect==4) {
			this.darkmax=190
			this.lightmax=254
		}
		if (this.fade_effect==5) {
			this.darkmax=1
			this.lightmax=80
		}
	
		this.range=(this.lightmax-this.darkmax)
		if (this.firsttime==true) {
			this.newred=Math.ceil(this.range*Math.random())+this.darkmax
			this.newgreen=Math.ceil(this.range*Math.random())+this.darkmax
			this.newblue=Math.ceil(this.range*Math.random())+this.darkmax
			this.firsttime=false
		}
		
		this.oldred=Math.ceil(this.range*Math.random())+this.darkmax
		this.oldgreen=Math.ceil(this.range*Math.random())+this.darkmax
		this.oldblue=Math.ceil(this.range*Math.random())+this.darkmax
		
		this.stepred=this.newred-this.oldred
		if (this.oldred>this.newred) {this.stepred=1}
		else if (this.oldred<this.newred) {this.stepred=-1}
		else {this.stepred=0}
		
		this.stepgreen=this.newgreen-this.oldgreen
		if (this.oldgreen>this.newgreen) {this.stepgreen=1}
		else if (this.oldgreen<this.newgreen) {this.stepgreen=-1}
		else {this.stepgreen=0}
		
		this.stepblue=this.newblue-this.oldblue
		if (this.oldblue>this.newblue) {this.stepblue=1}
		else if (this.oldblue<this.newblue) {this.stepblue=-1}
		else {this.stepblue=0}
		//console.log(obj);
		this.fadebg()
	}
	function fadebg() {
		if (this.newred==this.oldred) {this.stepred=0}
		if (this.newgreen==this.oldgreen) {this.stepgreen=0}
		if (this.newblue==this.oldblue) {this.stepblue=0}
		this.newred+=this.stepred
		this.newgreen+=this.stepgreen
		this.newblue+=this.stepblue
		if (this.stepred!=0 || this.stepgreen!=0 || this.stepblue!=0) {
			this.redcol_1 = this.hexc[Math.floor(this.newred/16)];
			this.redcol_2 = this.hexc[this.newred%16];
			this.greencol_1 = this.hexc[Math.floor(this.newgreen/16)];
			this.greencol_2 = this.hexc[this.newgreen%16];
			this.bluecol_1 = this.hexc[Math.floor(this.newblue/16)];
			this.bluecol_2 = this.hexc[this.newblue%16];
			this.newcolor="#"+this.redcol_1+this.redcol_2+this.greencol_1+this.greencol_2+this.bluecol_1+this.bluecol_2

			self.postMessage(this.newcolor);
				
			this.timer=setTimeout(this.obj+".fadebg()",this.speed*1000);
		} 
		else {
			clearTimeout(this.timer)
			this.newred=this.oldred
			this.newgreen=this.oldgreen
			this.newblue=this.oldblue
			this.oldcolor=this.newcolor
			//console.log('setrandomcolor');
			this.setrandomcolor()
		}
	}
	this.setrandomcolor();
}
/*if (browserok) {
// Select fade-effect below:
// Set 1 if the background may fade from dark to medium 
// Set 2 if the background may fade from light to medium 
// Set 3 if the background may fade from very dark to very light light
// Set 4 if the background may fade from light to very light
// Set 5 if the background may fade from dark to very dark 


// What type of gradient should be applied Internet Explorer 5x or higher?
// Set "none" or "horizontal" or "vertical"

// Speed higher=slower
//colordiv(divid, window obj ref, type, fade_effect, gradienttype, speed)
	window.onload=function(){
		window.t=new colordiv('colors', 't', 'bg', 3, 'horizontal', 60);t.setrandomcolor();
		window.l=new colordiv('ttl', 'l', 'txt', 4, 'horizontal', 60);l.setrandomcolor();
	}
} */
self.addEventListener('message', function(e) {
	this.t=new colordiv('t',  e.data.tone, 'horizontal', e.data.time);//t.setrandomcolor();
	//colordiv(obj, fefx, gtype, speed)
	//this.l=new colordiv('ttl', 'l', 'txt', 5, 'horizontal', 60);//l.setrandomcolor();
}, false);
	

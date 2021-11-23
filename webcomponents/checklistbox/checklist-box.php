<?php
header('Content-Type: text/javascript');
?>
const template = document.createElement('template');
template.innerHTML = `
<style>
/* Include the padding and border in an element's total width and height */
* {
  box-sizing: border-box;
}

/* Remove margins and padding from the list */
ul {
  margin: 0;
  padding: 0;
}

/* Style the list items */
ul li {
  cursor: pointer;
  position: relative;
  padding: 12px 8px 12px 40px;
  background: #eee;
  font-size: 18px;
  transition: 0.2s;
  list-style:none;
  /* make the list items unselectable */
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Set all odd list items to a different color (zebra-stripes) */
ul li:nth-child(odd) {
  background: #f9f9f9;
}

/* Darker background-color on hover */
ul li:hover {
  background: #ddd;
}

/* When clicked on, add a background color and strike out text */
ul li.checked {
  background: #888;
  color: #fff;
  text-decoration: line-through;
}

/* Add a "checked" mark when clicked on */
ul li.checked::before {
  content: '';
  position: absolute;
  border-color: #fff;
  border-style: solid;
  border-width: 0 2px 2px 0;
  top: 10px;
  left: 16px;
  transform: rotate(45deg);
  height: 15px;
  width: 7px;
}

/* Style the close button */
.close {
  position: absolute;
  right: 0;
  top: 0;
  padding: 12px 16px 12px 16px;
}

.close:hover {
  background-color: #f44336;
  color: white;
}

/* Style the header */
.header {
  background-color: #f44336;
  color: white;
  display:flex;
  flex-flow:column nowrap;
  justify-content:flex-start;
  align-items:stretch;
  text-align:center;
  min-width:310px;
}

/* Clear floats after the header */
.header:after {
  content: "";
  display: table;
  clear: both;
}

/* Style the input */
input {
  margin: 0;
  border: none;
  border-radius: 0;
  width: 75%;
  padding: 10px;
  float: left;
  font-size: 16px;
}
@media only screen and (max-width: 767px) and (min-width: 480px) {
  input{padding:3px;}
  .header{padding:15px 20px;}
}
@media only screen and (max-width: 479px) {
  input{padding:3px;}
  .header{padding:5px 10px;}
}
/* Style the "Add" button */
.addbtn {
  padding: 10px;
  width: 25%;
  background: #d9d9d9;
  color: #555;
  float: left;
  text-align: center;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
  border-radius: 0;
}
.addbtn:hover {
  background-color: #bbb;
}
</style>
<div id="mychecklistbox" style="min-width:310px">
	 <div id="header" class="header" >
	 	<h2 id="title" class="checklistboxtitle" ></h2>
    <span style="display:inline-block;width:100%;padding:3px" >
	 	    <input type="text" name="myinput" id="myinput" placeholder="Title...">
	 	    <div id="addbtn" class="addbtn" >Add</div>
      </span>
	 </div>
	 <ul id="checklist"></ul> 
   <div style="display:block;width:100%;text-align:right;padding:3px" id="deletebtn" ></div>
</div>`;
class ChecklistBox extends HTMLElement {

    constructor() {
        //when element is created or upgraded

        super();
    }

		connectedCallback() {

		    this.attachShadow({mode: 'open'});
		    this.shadowRoot.appendChild(template.content.cloneNode(true));
		    this.clb = this.shadowRoot.querySelector("#mychecklistbox");
        //Called every time the element is inserted into the DOM.
        if(this.hasAttribute("width")){
          	this.clb.style.width = this.getAttribute("width");
        }else{
        	this.clb.style.width = "100%";
			  }
        this.listItems = this.items;
        this.render();
        this.shadowRoot.querySelector("#addbtn").addEventListener("click",this.newElement.bind(this.shadowRoot));
  			if(this.hasAttribute("title")){
          this.listname = this.getAttribute("title");
  				this.shadowRoot.querySelector("#title").innerHTML = this.getAttribute("title");
  			}else{
          this.listname = "todo list component";
  				this.shadowRoot.querySelector("#title").innerHTML = "todo list component";
  			}
    }

    disconnectedCallback() {
      //Called every time the element is removed from the DOM.
    }

    render() {
  		var i;
  		for (i = 0; i < this.listItems.length; i++) {
  		  this.newElement(this.shadowRoot, this.listItems[i]);
  		}
      var b = document.createElement('span');
      b.setAttribute('style','padding:4px 10px;background:black;color:white;');
      b.addEventListener("click",this.removechecklist.bind(this.shadowRoot));
      b.innerHTML = '&times';
      this.shadowRoot.querySelector("#deletebtn").appendChild(b);
    }
    static get observedAttributes() {
         
    }
    attributeChangedCallback() {
         
    }
  	newElement(e,taskval=null) {
  	  var dom;
  	  if(typeof e.querySelector == 'function'){dom = e;}else{dom = this;}
  	  var li = document.createElement("li");
  	  var inputValue;
  	  if(taskval != null){
  	  	inputValue = taskval;
  	  }else{
        var dt = new Date();
        var ts = (parseInt(dt.getMonth())+1) + '/' + (dt.getDate()) + '/' + (dt.getUTCFullYear()) + " " + dt.getHours() + ":" + ('0'+dt.getMinutes()).slice(-2);
  	  	inputValue = '0|'+this.querySelector("#myinput").value+'|'+ts+'|true';
      } 
      if (inputValue === '') {
        alert("You must write something!"); return;
      } else {
        dom.querySelector("#checklist").appendChild(li);
      }
      var s = inputValue.split("|");
      /*
      s[0] = id
      s[1] = description
      s[2] = timedate
      s[3] = active
      */
  	  var t = document.createTextNode(s[1]);
  	  li.appendChild(t);

  	  dom.querySelector("#myinput").value = "";

  	  li.onclick = function(e){
  	    e.target.classList.toggle('checked');
        var active='true';
        if ( (" " + e.target.className + " ").replace(/[\n\t]/g, " ").indexOf("checked") > -1 ){active='false';}
        window.top.sendpostonly(window.location.href,'axn=forgetreminder&active='+active+'&listname='+dom.host.listname+'&timedate='+s[2]);
  	  }
  	  if(s[3] != null && s[3] == 'false'){
  	    
  	  	li.setAttribute('class','checked');
  	  }
  	  var span = document.createElement("SPAN");
  	  var txt = document.createTextNode("\u00D7");
  	  span.className = "close";
  	  span.appendChild(txt);
  	  li.appendChild(span);
  	  span.onclick = function() {
  	      var div = this.parentElement;
  	      div.style.display = "none";
          window.top.sendpostonly(window.location.href,'axn=removereminder&listname='+dom.host.listname+'&timedate='+s[2]);
  	  }
      if(taskval == null){window.top.sendpostonly(window.top.location.href, 'axn=addreminder&reminder='+window.top.base64Encode(dom.host.listname+"|"+s[1]));}
  	} 
  	get items() {
  	  const items = [];
      [...this.attributes].forEach(attr => {
        //console.log(attr);
        if (attr.name.includes('reminder')) {
          items.push(attr.value);
        }
      });
      return items;
    }
    removechecklist(e){
      window.top.removechecklist(this.host.listname);
    }
}

window.customElements.define('checklist-box', ChecklistBox); 
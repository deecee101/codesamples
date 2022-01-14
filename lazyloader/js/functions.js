var Screen = (function() {
	var screen = {};

	// Get a reference to the body
	// --------------------------------------------------------------------
	screen.getBody = function() {
		if (document.body) {
			return document.body;
		}
		if (document.getElementsByTagName) {
			var bodies = document.getElementsByTagName("BODY");
			if (bodies!=null && bodies.length>0) {
				return bodies[0];
			}
		}
		return null;
	};

	// Get the amount that the main document has scrolled from top
	// --------------------------------------------------------------------
	screen.getScrollTop = function() {
		if (document.documentElement && defined(document.documentElement.scrollTop) && document.documentElement.scrollTop>0) {
			return document.documentElement.scrollTop;
		}
		if (document.body && defined(document.body.scrollTop)) {
			return document.body.scrollTop;
		}
		return null;
	};
	
	// Get the amount that the main document has scrolled from left
	// --------------------------------------------------------------------
	screen.getScrollLeft = function() {
		if (document.documentElement && defined(document.documentElement.scrollLeft) && document.documentElement.scrollLeft>0) {
			return document.documentElement.scrollLeft;
		}
		if (document.body && defined(document.body.scrollLeft)) {
			return document.body.scrollLeft;
		}
		return null;
	};
	
	// Util function to default a bad number to 0
	// --------------------------------------------------------------------
	screen.zero = function(n) {
		return (!defined(n) || isNaN(n))?0:n;
	};

	// Get the width of the entire document
	// --------------------------------------------------------------------
	screen.getDocumentWidth = function() {
		var width = 0;
		var body = screen.getBody();
		if (document.documentElement && (!document.compatMode || document.compatMode=="CSS1Compat")) {
		    var rightMargin = parseInt(CSS.get(body,'marginRight'),10) || 0;
		    var leftMargin = parseInt(CSS.get(body,'marginLeft'), 10) || 0;
			width = Math.max(body.offsetWidth + leftMargin + rightMargin, document.documentElement.clientWidth);
		}
		else {
			width =  Math.max(body.clientWidth, body.scrollWidth);
		}
		if (isNaN(width) || width==0) {
			width = screen.zero(self.innerWidth);
		}
		return width;
	};
	
	// Get the height of the entire document
	// --------------------------------------------------------------------
	screen.getDocumentHeight = function() {
		var body = screen.getBody();
		var innerHeight = (defined(self.innerHeight)&&!isNaN(self.innerHeight))?self.innerHeight:0;
		if (document.documentElement && (!document.compatMode || document.compatMode=="CSS1Compat")) {
		    var topMargin = parseInt(CSS.get(body,'marginTop'),10) || 0;
		    var bottomMargin = parseInt(CSS.get(body,'marginBottom'), 10) || 0;
			return Math.max(body.offsetHeight + topMargin + bottomMargin, document.documentElement.clientHeight, document.documentElement.scrollHeight, screen.zero(self.innerHeight));
		}
		return Math.max(body.scrollHeight, body.clientHeight, screen.zero(self.innerHeight));
	};
	
	// Get the width of the viewport (viewable area) in the browser window
	// --------------------------------------------------------------------
	screen.getViewportWidth = function() {
		if (document.documentElement && (!document.compatMode || document.compatMode=="CSS1Compat")) {
			return document.documentElement.clientWidth;
		}
		else if (document.compatMode && document.body) {
			return document.body.clientWidth;
		}
		return screen.zero(self.innerWidth);
	};
	
	// Get the height of the viewport (viewable area) in the browser window
	// --------------------------------------------------------------------
	screen.getViewportHeight = function() {
		if (!window.opera && document.documentElement && (!document.compatMode || document.compatMode=="CSS1Compat")) {
			return document.documentElement.clientHeight;
		}
		else if (document.compatMode && !window.opera && document.body) {
			return document.body.clientHeight;
		}
		return screen.zero(self.innerHeight);
	};

	return screen;
})();
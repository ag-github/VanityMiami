LiveAgentChatBaseObject = function() {
};

LiveAgentChatBaseObject.prototype.windowWidth;
LiveAgentChatBaseObject.prototype.windowHeight;
LiveAgentChatBaseObject.prototype.windowPosition;

LiveAgentChatBaseObject.prototype.initHtml = function(html) {
	if ("https:" == document.location.protocol) {
		html = html.replace('http://', 'https://');
	}
	return html;
}

LiveAgentChatBaseObject.prototype.setChatWindowParams = function(width, height, position) {
	this.windowWidth = width;
	this.windowHeight = height;
	this.windowPosition = position;
}

LiveAgentChatBaseObject.prototype.createChatWindowParams = function(x, y) {
	var size = this.getWindowSize();
	var width = this.windowWidth;
	var height = this.windowHeight;
	if (this.windowWidth > size[0]) {
		width = size[0];
	}		
	if (this.windowHeight > size[1]) {
		height = size[1];
	}
	var left = 0;
	var top = size[1] / 2 - height / 2;
	if (this.windowPosition == 'R') {
		left = size[0] - width;
	}
	if (this.windowPosition == 'C') {
		left = size[0] / 2 - width / 2;
	}
	if (this.windowPosition == 'O') {
		left = x;
		top = y;
	}
	return 'width=' + width + ',height=' + height
			+ ',left=' + left + ',top=' + top + ',scrollbars=yes';
}


LiveAgentChatBaseObject.prototype.openPopupWindow = function(serverUrl, params, x, y, isMobile) {
	if (isMobile) {
		window.open(serverUrl + '?' + params, "_blank");
	} else {
		window.open(serverUrl + '?' + params, "_blank", this.createChatWindowParams(x, y));
	}
}

LiveAgentChatBaseObject.prototype.getWindowSize = function() {
	var wWidth = 0, wHeight = 0;
	if (typeof (window.innerWidth) == 'number') {
		// Non-IE
		wWidth = window.innerWidth;
		wHeight = window.innerHeight;
	} else if (document.documentElement
			&& (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		// IE 6+ in 'standards compliant mode'
		wWidth = document.documentElement.clientWidth;
		wHeight = document.documentElement.clientHeight;
	} else if (document.body
			&& (document.body.clientWidth || document.body.clientHeight)) {
		// IE 4 compatible
		wWidth = document.body.clientWidth;
		wHeight = document.body.clientHeight;
	}
	return [wWidth, wHeight];
}

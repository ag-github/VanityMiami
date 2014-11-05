LiveAgentInvitation = function(cid, iaid, dateChanged) {		
	this.id = cid;
	this.iaid = iaid;
	this.width = "200";
	this.height = "200";
	this.position = "C";
	this.animation = "N";
	this.scrollX = 0; 
	this.scrollY = 0;
	this.element;
	this.chatAttributes = null;
	this.chatIframe = null;
	this.chatIframeElement = null;
	this.runningEmbeddedChat = false;
	this.dateChanged = dateChanged;
	
	this.horizontalConstant = 0;
	this.verticalConstant = 0;
};

LiveAgentInvitation.prototype = new LiveAgentChatBaseObject;
LiveAgentInvitation.prototype.constructor = LiveAgentInvitation;

LiveAgentInvitation.prototype.insertInvitationElement = function() {
	if (this.element == undefined || this.element == null) {		
		this.element = document.createElement('div');
		this.element.setAttribute('id', '_invitationcode');		
		document.body.appendChild(this.element);
	}
}

LiveAgentInvitation.prototype.setHtml = function(html) {
	this.element.innerHTML = this.initHtml(html);
}

LiveAgentInvitation.prototype.setInvitationParams = function(width, height, position, animation) {
	this.width = width;
	this.height = height;
	this.position = position;
	this.animation = animation;
}

LiveAgentInvitation.prototype.setPositionConstants = function(horizontalConstant, verticalConstant) {
	this.horizontalConstant = horizontalConstant;
	this.verticalConstant = verticalConstant;
}

LiveAgentInvitation.prototype.setValidTo = function(validTo) {
	var self = this;
	setTimeout(function(){self.hide();}, validTo.getTime() - new Date().getTime());
}

LiveAgentInvitation.prototype.open = function(x, y) {
	if (this.chatIframeElement != null) {
		this.setChatIframeStyle(this.chatIframeElement);
		LiveAgentTrackerXD.postMessage("start", this.chatIframeElement.src, this.chatIframe);
		this.runningEmbeddedChat = true;
	}
	if (this.chatAttributes != null && this.chatAttributes['type'] == 'P') {
		this.openPopupWindow(this.chatAttributes['chaturl'], this.createChatWindowServerParams('iaid=' + this.iaid + '&t=' + this.dateChanged), x, y);
	}
	this.hide();
}

LiveAgentInvitation.prototype.hide = function() {
	if (this.element != undefined) {		
		document.body.removeChild(this.element);
		this.element = null;
	}
}

LiveAgentInvitation.prototype.show = function(html, x, y) {
	this.insertInvitationElement();
	this.setHtml(html);
	if (this.animation == 'Y') {
	    this.animate(1000, 1, x, y);
	} else {
		this.setStyle(100, x, y);
	}
}

LiveAgentInvitation.prototype.animate = function(time, step, x, y) {
	this.setStyle(step, x, y);
	if (step < 100) {      	
		this.setAnimateTimeout(time, ++step, x, y);
	}
}

LiveAgentInvitation.prototype.setAnimateTimeout = function(time, step, x, y) {
	var self = this;
	setTimeout(function(){self.animate(time, step, x, y);}, time / 100);
}

LiveAgentInvitation.prototype.setStyle = function(step, x, y) {
	this.element.style.position = 'fixed';
    this.element.style.zIndex = '99999';
    
	if (this.position == 'TL') {
		this.setPosition(Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', null, null, (0 + this.horizontalConstant) + 'px');
	}
	if (this.position == 'TC') {
		this.setPosition(Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', null, null, '50%');
		this.setMargin(null, Math.round(- this.width / 2) + 'px');
	}
	if (this.position == 'TR') {
		this.setPosition(Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', (0 + this.horizontalConstant) + 'px', null, null);
	}
	if (this.position == 'BL') {
		this.setPosition(null, null, Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', (0 + this.horizontalConstant) + 'px');
	}
	if (this.position == 'BC') {
		this.setPosition(null, null, Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', '50%');
		this.setMargin(null, Math.round(- this.width / 2) + 'px');
	}
	if (this.position == 'BR') {
		this.setPosition(null, (0 + this.horizontalConstant) + 'px', Math.round(-this.height + this.height / 100 * step + this.verticalConstant) + 'px', null);
	}
	if (this.position == 'O') {	
		this.initScrollXY();
		xAnimation = -this.height / 25 + this.height / 100 * step / 25;	
		this.setPosition((y - this.scrollY + this.verticalConstant) + 'px', null, null, Math.round(x - this.scrollX + (step % 2 == 0 ? +xAnimation : -xAnimation) + this.horizontalConstant) + 'px');	
	}
	if (this.position == 'CL') {
		this.setPosition('50%', null, null, Math.round(-this.width + this.width / 100 * step + this.horizontalConstant) + 'px');
		this.setMargin(Math.round(- this.height / 2) + 'px', null);
	} 
	if (this.position == 'CR') {
		this.setPosition('50%', Math.round(-this.width + this.width / 100 * step + this.horizontalConstant) + 'px', null, null);
		this.setMargin(Math.round(- this.height / 2) + 'px', null);
	} 
	if (this.position == 'C') {
		this.setPosition('50%', null, null, '50%');
		xAnimation = -this.height / 25 + this.height / 100 * step / 25;	
		this.setMargin(Math.round(- this.height / 2) + 'px', Math.round(- this.width / 2 + (step % 2 == 0 ? +xAnimation : -xAnimation)) + 'px');
	}
}

LiveAgentInvitation.prototype.setPosition = function(top, right, bottom, left) {	
    if (top != null) {
    	this.element.style.top = top;
    }
    if (right != null) {
    	this.element.style.right = right;
    }
    if (bottom != null) {
    	this.element.style.bottom = bottom;
    }
    if (left != null) {
    	this.element.style.left = left;
    }    
}

LiveAgentInvitation.prototype.setMargin = function(top, left) {
    if (top != null) {
    	this.element.style.marginTop = top;
    }
    if (left != null) {
    	this.element.style.marginLeft = left;
    }
}

LiveAgentInvitation.prototype.initScrollXY = function() {	  
	if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		this.scrollY = window.pageYOffset;
		this.scrollX = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
	    //DOM compliant
		this.scrollY = document.body.scrollTop;
		this.scrollX = document.body.scrollLeft;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
	    //IE6 standards compliant mode
		this.scrollY = document.documentElement.scrollTop;
		this.scrollX = document.documentElement.scrollLeft;
	}
}

LiveAgentInvitation.prototype.initChat = function(chatUrl, type, width, height,
		position) {
	this.chatAttributes = new PostAssoc();
	this.chatAttributes['chaturl'] = chatUrl;
	this.chatAttributes['type'] = type;
	this.chatAttributes['width'] = width;
	this.chatAttributes['height'] = height;
	this.chatAttributes['position'] = position;
	
		if (this.chatAttributes['type'] == 'E') {
			if (this.chatIframeElement == null) {
				this.chatIframeElement = document.createElement('iframe');
				this.setInvisibleStyle(this.chatIframeElement);
				this.chatIframeElement.setAttribute('src',
						this.chatAttributes['chaturl'] + "?iaid=" + this.iaid + "&t=" + this.dateChanged
								+ "&ie=" + encodeURIComponent(LiveAgentTrackerXD.getIeVersion())
								+ "&pt=" + encodeURIComponent(document.title)
								+ "#" + encodeURIComponent(document.location.href));
				document.body.appendChild(this.chatIframeElement);
				this.chatIframe = frames[frames.length - 1];
			}		
	}		
}

LiveAgentInvitation.prototype.setInvisibleStyle = function(element) {
	element.style.display = 'none';
}

LiveAgentInvitation.prototype.setChatIframeStyle = function(element) {
	LiveAgentTracker.setChatIframeStyle(element, '999999', this.chatAttributes['width'], this.chatAttributes['height'], this.chatAttributes['position']);
}

LiveAgentInvitation.prototype.createChatWindowServerParams = function(params) {
	params += "&pt=" + encodeURIComponent(document.title);
	return params;
}

LiveAgentInvitation.prototype.isRunningEmbeddedChat = function() {
	return this.runningEmbeddedChat;
}

LiveAgentInvitation.prototype.getChatIframeStyle = function() {
	if (this.chatIframeElement != null) {
		return this.chatIframeElement.style.cssText;
	}
	return '';
}

LiveAgentInvitation.prototype.getId = function() {
	return this.id;
}

LiveAgentInvitation.prototype.getIAId = function() {
	return this.iaid;
}

LiveAgentInvitation.prototype.closeChatIframe = function() {
	if (this.chatIframeElement != null) {
		var element = this.chatIframeElement;
		this.chatIframeElement = null;
		this.runningEmbeddedChat = false;
		
		element.style.display = 'none';
		//element can not be removed as IE throws flash error
		//var delay = function() { document.body.removeChild(element); };
		//setTimeout(delay, 5000);
	}
}

LiveAgentInvitation.prototype.createChatWindowParams = function(x, y) {
	var size = this.getWindowSize();
	var width = this.chatAttributes['width'];
	var height = this.chatAttributes['height'];
	if (this.chatAttributes['width'] > size[0]) {
		width = size[0];
	}
	if (this.chatAttributes['height'] > size[1]) {
		height = size[1];
	}
	var left = 0;
	var top = size[1] / 2 - height / 2;
	if (this.chatAttributes['position'] == 'R') {
		left = size[0] - width;
	}
	if (this.chatAttributes['position'] == 'C') {
		left = size[0] / 2 - width / 2;
	}
	if (this.chatAttributes['position'] == 'O') {
		left = x;
		top = y;
	}
	return 'width=' + width + ',height=' + height + ',left=' + left + ',top='
			+ top + ',scrollbars=yes';
}
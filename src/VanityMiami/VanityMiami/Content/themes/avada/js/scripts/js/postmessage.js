var LiveAgentTrackerXD = function() {
  
    var interval_id,
    last_hash,
    cache_bust = 1,
    attached_callback,
    window = this;
    
    var ieVersion = -1;
    if (navigator.appName == 'Microsoft Internet Explorer') {
      var ua = navigator.userAgent;
      var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null) {
    	  ieVersion = parseFloat( RegExp.$1 );
      	  if (document.documentMode < 8) {
      		  ieVersion = 7;
      	  }
      }
    }
    var regex = new RegExp("[\\?&]ie=([^&#]*)");  
    var results = regex.exec(window.location.href); 
    if (results != null) {
    	ieVersion = results[1];
    }
    
    return {
    	
    	getIeVersion : function() {
    		return ieVersion;
    	},
    	
        postMessage : function(message, target_url, target) {
            if (!target_url) { 
                return; 
            }
            target = target || parent; 
            if (ieVersion != 7 && window['postMessage']) {
                target['postMessage'](message, target_url.replace( /([^:]+:\/\/[^\/]+).*/, '$1'));
            } else if (target_url) {
            	target.location = target_url.replace(/#.*$/, '') + '#' + (+new Date) + (cache_bust++) + '&' + message;
            }
        },
  
        receiveMessage : function(callback, source_origin) {
            if (ieVersion != 7 && window['postMessage']) {
                if (callback) {
                    attached_callback = function(e) {
                        if ((typeof source_origin === 'string' && e.origin !== source_origin)
                        || (Object.prototype.toString.call(source_origin) === "[object Function]" && source_origin(e.origin) === !1)) {
                            return !1;
                        }
                        callback(e);
                    };
                }
                if (window['addEventListener']) {
                    window[callback ? 'addEventListener' : 'removeEventListener']('message', attached_callback, !1);
                } else {
                    window[callback ? 'attachEvent' : 'detachEvent']('onmessage', attached_callback);
                }
            } else {
                interval_id && clearInterval(interval_id);
                interval_id = null;

                if (callback) {
                    interval_id = setInterval(function(){
                        var hash = document.location.hash;
                        if (hash === last_hash) {
                        	return;
                        }
                        re = /^#?\d+&/;
                        if (re.test(hash)) {
                            callback({data: hash.replace(re, '')});
                            document.location.hash = '';
                        }
                        last_hash = document.location.hash;
                    }, 100);
                }
            }   
        }
    };
}();
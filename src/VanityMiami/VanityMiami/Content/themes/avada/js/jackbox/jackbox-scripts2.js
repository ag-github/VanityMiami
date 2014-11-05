;var jackbox;

(function($) {
	
	var domain = jackboxOptions.domain, scripts, count, total;
	
	function init() {
		
		var header = $("head"),
		url = document.URL, splits = url.split("?url=");
		if(splits.length === 2) window.location = splits[0] + "#/" + splits[1];
		
		if(url.search("https") === -1 && domain.search("https") !== -1) {
			domain = jackboxOptions.domain = domain.split("https").join("http");	
		}
	
		var style = $("<link />").attr("rel", "stylesheet").attr("type", "text/css").appendTo(header);
		style.attr("href", domain + "jackbox/css/jackbox.css");
		
		var custom = jackboxOptions["custom-css"];
		if(custom) $("<style />").attr("type", "text/stylesheet").html(custom).appendTo(header);
		
		if(jackboxOptions["minified-scripts"] === "yes") {
			
			$.getScript(domain + "jackbox/js/jackbox-packed.js");
			
		}
		else {
			
			scripts = [
				
				"jackbox/js/libs/jquery.address-1.5.min.js",
				"jackbox/js/libs/Jacked.js",
				"jackbox/js/jackbox-swipe.js",
				"jackbox/js/libs/StackBlur.js",
				"jackbox/js/jackbox.js"
			
			]
			
			count = 0;
			total = 5;
			loadScript();	
			
		}
		
		$("<img />").attr("src", domain + "jackbox/img/graphics/panel_left_over.png");
		$("<img />").attr("src", domain + "jackbox/img/graphics/panel_right_over.png");
		
	}
	
	function loadScript() {
		
		if(count < 5) {
			
			$.getScript(domain + scripts[count], loadScript);
			count++;
			
		}
		
	}
	
	$(document).ready(function() {
	
		jackbox = $(".jackbox[data-jbgroup]");
	
		// scripts and css loaded asynchronously only if page contains a jackbox item
		if(jackbox.length) init();
	
	});
	
})(jQuery);
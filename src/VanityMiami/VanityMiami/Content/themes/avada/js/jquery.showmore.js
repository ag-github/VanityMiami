$(document).ready(function () {
		$('.showmore').showMore({
		speedDown: 300,
	        speedUp: 300,
	        height: '70px',
	        showText: 'More +',
	        hideText: 'Less -'
	});	      
});

(function(a){a.fn.showMore=function(b){var c={speedDown:300,speedUp:300,height:"265px",showText:"Show",hideText:"Hide"};var b=a.extend(c,b);return this.each(function(){var e=a(this),d=e.height();if(d>parseInt(b.height)){e.wrapInner('<div class="showmore_content" />');e.find(".showmore_content").css("height",b.height);e.append('<div class="showmore_trigger"><span class="more">'+b.showText+'</span><span class="less" style="display:none;">'+b.hideText+"</span></div>");e.find(".showmore_trigger").on("click",".more",function(){a(this).hide();a(this).next().show();a(this).parent().prev().animate({height:d},b.speedDown)});e.find(".showmore_trigger").on("click",".less",function(){a(this).hide();a(this).prev().show();a(this).parent().prev().animate({height:b.height},b.speedUp)})}})}})(jQuery);
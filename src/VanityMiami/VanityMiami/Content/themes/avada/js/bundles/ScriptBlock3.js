st_go({ v: 'ext', j: '1:2.7.2', blog: '59039376', post: '4844', tz: '-4' });
var load_cmc = function () { linktracker_init(59039376, 4844, 2); };
if (typeof addLoadEvent != 'undefined') addLoadEvent(load_cmc);
else load_cmc();

if (document.getElementById("laPlaceholder")) {
    (function (d, t) {
        var script = d.createElement(t); script.id = 'la_x2s6df8d'; script.async = true;
        script.src = '//myvanity.us/liveagent/scripts/track.js';
        var image = d.createElement('img'); script.async = true;
        image.src = '/Content/themes/avada/assets/pix.gif';
        script.onload = script.onreadystatechange = function () {
            var rs = this.readyState; if (rs && (rs != 'complete') && (rs != 'loaded')) return;
            LiveAgentTracker.createForm('b61acd01', this);
        };
        var placeholder = document.getElementById('laPlaceholder');
        placeholder.parentNode.insertBefore(script, placeholder);
        placeholder.parentNode.insertBefore(image, placeholder);
        placeholder.parentNode.removeChild(placeholder);
    })(document, 'script');
}
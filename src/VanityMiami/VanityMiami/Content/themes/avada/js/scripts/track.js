
var LiveAgentTrackerXD=function(){var interval_id,last_hash,cache_bust=1,attached_callback,window=this;var ieVersion=-1;if(navigator.appName=='Microsoft Internet Explorer'){var ua=navigator.userAgent;var re=new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");if(re.exec(ua)!=null){ieVersion=parseFloat(RegExp.$1);if(document.documentMode<8){ieVersion=7;}}}
var regex=new RegExp("[\\?&]ie=([^&#]*)");var results=regex.exec(window.location.href);if(results!=null){ieVersion=results[1];}
return{getIeVersion:function(){return ieVersion;},postMessage:function(message,target_url,target){if(!target_url){return;}
target=target||parent;if(ieVersion!=7&&window['postMessage']){target['postMessage'](message,target_url.replace(/([^:]+:\/\/[^\/]+).*/,'$1'));}else if(target_url){target.location=target_url.replace(/#.*$/,'')+'#'+(+new Date)+(cache_bust++)+'&'+message;}},receiveMessage:function(callback,source_origin){if(ieVersion!=7&&window['postMessage']){if(callback){attached_callback=function(e){if((typeof source_origin==='string'&&e.origin!==source_origin)||(Object.prototype.toString.call(source_origin)==="[object Function]"&&source_origin(e.origin)===!1)){return!1;}
callback(e);};}
if(window['addEventListener']){window[callback?'addEventListener':'removeEventListener']('message',attached_callback,!1);}else{window[callback?'attachEvent':'detachEvent']('onmessage',attached_callback);}}else{interval_id&&clearInterval(interval_id);interval_id=null;if(callback){interval_id=setInterval(function(){var hash=document.location.hash;if(hash===last_hash){return;}
re=/^#?\d+&/;if(re.test(hash)){callback({data:hash.replace(re,'')});document.location.hash='';}
last_hash=document.location.hash;},100);}}}};}();

if(LiveAgentTracker==undefined){LiveAgentVisitor=function(email,firstName,lastName,phone){this.email=email;this.firstName='';this.lastName='';this.phone='';if(typeof firstName!='undefined'){this.firstName=firstName;}
if(typeof lastName!='undefined'){this.lastName=lastName;}
if(typeof phone!='undefined'){this.phone=phone;}};LiveAgentVisitor.prototype.constructor=LiveAgentVisitor;LiveAgentVisitor.prototype.toJson=function(){return'{"e":"'+this.email+'","f":"'+this.firstName+'","l":"'+this.lastName+'","p":"'+this.phone+'"}';}
PostAssoc=function(){};var LiveAgentTracker=new function(){var integrationElementId='la_x2s6df8d';var x=0;var y=0;this.setCookie=function(name,value,expireDays){var expireString='';if(typeof expireDays!=='undefined'){var exdate=new Date();exdate.setDate(exdate.getDate()+expireDays);expireString="; expires="+exdate.toUTCString();}
document.cookie=name+"="+escape(value)+"; path=/"+expireString;}
this.getCookie=function(name){var i,x,y,ARRcookies=document.cookie.split(";");for(i=0;i<ARRcookies.length;i++){x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);x=x.replace(/^\s+|\s+$/g,"");if(x==name){return unescape(y);}}
return null;}
function computeServerUrl(){var url=new String(document.getElementById(integrationElementId).src);if(url.substr(0,1)=='/'&&url.substr(1,1)!='/'){url=document.location.protocol+'//'+document.location.host+url;}
return url.substr(0,url.lastIndexOf('scripts/'));}
function registerMouseMoveHandler(){if(!document.all){document.captureEvents(Event.MOUSEMOVE);}
document.onmousemove=function(e){if(document.all){LiveAgentTracker.x=event.clientX+document.body.scrollLeft;LiveAgentTracker.y=event.clientY+document.body.scrollTop;}else{LiveAgentTracker.x=e.pageX;LiveAgentTracker.y=e.pageY;}
if(LiveAgentTracker.x<0){LiveAgentTracker.x=0;}
if(LiveAgentTracker.y<0){LiveAgentTracker.y=0;}};}
registerMouseMoveHandler();this.serverUrl=computeServerUrl();this.trackUrl=this.serverUrl+'scripts/track.php';this.widgets=new PostAssoc();this.virtualButtons=new PostAssoc();this.scriptTag=null;this.busScriptTag=null;this.inviteScriptTag=null;this.updateVisitScriptTag=null;this.invitationTimers=new PostAssoc();this.timeOffset=null;this.requestId=0;this.invitation=null;this.chatIframeElement=null;this.runningButton=null;this.userDetails=null;this.visitorId=null;this.startTime=new Date();this.busTimer=false;this.sid='';this.fastBus=false;this.visitorsTrackingDisabled=false;this.lastEventId=-1;this.initLastEventId=function(){var lastEventIdCookie=this.getCookie("laEventId");if(lastEventIdCookie!=null&&lastEventIdCookie!=""){this.lastEventId=lastEventIdCookie;}}
this.initLastEventId();this.getTrackingParams=function(){var title=document.title;if(typeof(document.title)=="string"){title=document.title.replace(/\|/g," ");}
return"&pt="+encodeURIComponent(title)+"&ref="+this.fullEncodeUrl(document.referrer)+"&sr="
+screen.width+'x'+screen.height+"&lrc="+escape(this.getCookie('LaRunningChat'))
+"&ci="+escape(this.getCookie('closedInvitations'))+"&vn="+escape(this.getCookie('LaVisitorNew'))
+'&vid='+escape(this.getCookie('LaVisitorId'))+(document.compatMode=='CSS1Compat'?'':'&qm=Y');}
this.disableOnlineVisitorsTacking=function(){this.visitorsTrackingDisabled=true;}
this.setVisitorId=function(id){this.visitorId=id;this.setCookie('LaVisitorId',id);}
this.getVisitorId=function(){return this.visitorId;}
this.setSid=function(id){this.sid=id;}
this.setFastBusEnabled=function(s){this.fastBus=(s=="Y");}
this.getServerUrl=function(){return this.serverUrl;}
this.fullEncodeUrl=function(url){if(url==null||url==""){return'';}
url=url.replace(new RegExp("http://",'g'),"_|H|_");url=url.replace(new RegExp("https://",'g'),"_|S|_");return escape(url);}
this.encodeUrl=function(url){if(url==null||url==""){return'';}
if(url.substring(0,7)=='http://'){return escape('H_'+url.substring(7));}
if(url.substring(0,8)=='https://'){return escape('S_'+url.substring(8));}
return'A_'+escape(url);}
this.getUpdateParams=function(){var widgetsString="[";for(var name in this.widgets){widgetsString+=this.widgets[name].toJson()+",";}
if(widgetsString.length>1){widgetsString=widgetsString.substring(0,widgetsString.length-1)
+"]";}else{widgetsString+="]";}
var params="&wds="+widgetsString;if(this.userDetails!=null){params+="&ud="+this.userDetails.toJson();}
return params;}
this.startTimer=function(timeout){clearTimeout(this.timer);this.timer=setTimeout("LiveAgentTracker.sendRequest()",timeout);}
this.sendRequest=function(){this.removeScript();this.addScript();this.requestId++;}
this.initBusReader=function(){clearTimeout(this.busTimer);this.busTimer=setTimeout("LiveAgentTracker.readBus()",5000);}
this.initBusReader();this.readBus=function(){if(this.visitorsTrackingDisabled){return;}
this.removeBusScript();this.addBusScript();var timeOnPage=Math.floor(new Date().getTime()/60000)-Math.floor(this.startTime/60000);var timeout=180;if(this.getCookie('LaRunningChat')!=null&&this.getCookie('LaRunningChat')!=''){this.startTime=new Date();timeout=10;}else if(timeOnPage<1){timeout=10;}else if(timeOnPage<2){timeout=20;}else if(timeOnPage<30){timeout=40;}else if(timeOnPage<60){timeout=60;}else if(timeOnPage<120){timeout=120;}else{timeout=180;}
clearTimeout(this.busTimer);this.busTimer=setTimeout("LiveAgentTracker.readBus()",timeout*1000);}
this.removeBusScript=function(){var headTag=document.getElementsByTagName("head").item(0);if(this.busScriptTag!=null){try{headTag.removeChild(this.busScriptTag);}catch(err){}
this.busScriptTag=null;}}
this.addBusScript=function(){var headTag=document.getElementsByTagName("head").item(0);this.busScriptTag=document.createElement("script");this.busScriptTag.setAttribute("type","text/javascript");if(this.fastBus){this.busScriptTag.setAttribute("src",this.serverUrl+"accounts/default1/cache/bus/"+this.sid+".js?r="+Math.floor(Math.random()*10000));}else{this.busScriptTag.setAttribute("src",this.serverUrl+"scripts/bus.php?sid="+this.sid);}
headTag.appendChild(this.busScriptTag);}
this.isEventProcessed=function(id){return id<=this.lastEventId;}
this.confirmEvent=function(id){this.lastEventId=id;var headTag=document.getElementsByTagName("head").item(0);scriptTag=document.createElement("script");scriptTag.setAttribute("type","text/javascript");scriptTag.setAttribute("src",this.serverUrl+"scripts/bus_confirm.php?sid="+this.sid+"&id="+id);headTag.appendChild(scriptTag);this.setCookie("laEventId",id,"");}
setTimeout("LiveAgentTracker.updateVisit()",120000);this.updateVisit=function(){if(this.visitorsTrackingDisabled){return;}
this.removeUpdateVisitScript();this.addUpdateVisitScript();setTimeout("LiveAgentTracker.updateVisit()",120000);}
this.removeUpdateVisitScript=function(){var headTag=document.getElementsByTagName("head").item(0);if(this.updateVisitScriptTag!=null){try{headTag.removeChild(this.updateVisitScriptTag);}catch(err){}
this.updateVisitScriptTag=null;}}
this.addUpdateVisitScript=function(){var headTag=document.getElementsByTagName("head").item(0);this.updateVisitScriptTag=document.createElement("script");this.updateVisitScriptTag.setAttribute("type","text/javascript");this.updateVisitScriptTag.setAttribute("src",this.serverUrl+"scripts/update_visit.php?sid="+this.sid);headTag.appendChild(this.updateVisitScriptTag);}
this.removeScript=function(){var headTag=document.getElementsByTagName("head").item(0);if(this.scriptTag!=null){try{headTag.removeChild(this.scriptTag);}catch(err){}
this.scriptTag=null;}}
this.addScript=function(){var headTag=document.getElementsByTagName("head").item(0);this.scriptTag=document.createElement("script");this.scriptTag.setAttribute("type","text/javascript");this.scriptTag.setAttribute("src",this.getUrl());headTag.appendChild(this.scriptTag);}
this.getUrl=function(){var url=this.trackUrl+"?rc="+this.requestId+"&bu="+this.encodeUrl(this.serverUrl)+"&pu="+this.encodeUrl(document.location.href)
+"&chs="+escape(this.getCharset())+"&ieold="+this.isMSIE9orLower();if(this.requestId==0){url+=this.getTrackingParams();}
return url+this.getUpdateParams();}
this.getCharset=function(){return(document.characterSet)?document.characterSet:document.charset;}
this.isMSIE9orLower=function(){if(!window.opera&&/MSIE\s(\d+\.\d+);/.test(navigator.userAgent)){var ieVer=new Number(RegExp.$1)
if(ieVer<9.0){return 1;}}
return 0;}
this.setUserDetails=function(email,firstName,lastName,phone){this.userDetails=new LiveAgentVisitor(email,firstName,lastName,phone);this.startTimer(500);}
this.createButton=function(bid,element,note){if(bid==undefined){bid="";}
var button=new LiveAgentButton(bid,element,note);this.widgets[button.elementId]=button;this.startTimer(500);return button;}
this.createForm=function(fid,element,note){var form=new LiveAgentInPageForm(fid,element,note);this.widgets[form.elementId]=form;this.startTimer(500);return form;}
this.createVirtualButton=function(bid,element,note){if(bid==undefined){bid="";}
if(this.virtualButtons[bid]!=null){return;}
var button=new LiveAgentVirtualButton(bid,element,note);this.widgets[button.elementId]=button;this.virtualButtons[bid]=button;this.startTimer(500);return button;}
this.clickVirtualButton=function(bid){if(this.virtualButtons[bid]==null){return;}
this.virtualButtons[bid].onClick();}
this.createKbSearchWidget=function(id,element){var widget=new LiveAgentKbSearchWidget(id,element);this.widgets[widget.elementId]=widget;this.startTimer(500);}
this.getWidget=function(elementId){return this.widgets[elementId];}
this.openButtonChat=function(elementId){if(this.chatIframeElement==null&&this.runningButton==null){this.widgets[elementId].open(this.serverUrl,x,y);this.clearInvitationTimes();if(this.invitation!=null){this.invitation.hide();}
this.initBusReader();}}
this.openInvitationChat=function(){this.invitation.open(x,y);}
this.initTimeOffset=function(year,month,day,hod,min,sec){var serverDate=new Date(year,month-1,day,hod,min,sec);var curDate=new Date();this.timeOffset=curDate.getTime()-serverDate.getTime();}
this.createInvitation=function(cid,iaid,dateChanged){this.invitation=new LiveAgentInvitation(cid,iaid,dateChanged);this.clearInvitationTimes();}
this.setInvitationValidTo=function(year,month,day,hod,min,sec){this.invitation.setValidTo(new Date(new Date(year,month-1,day,hod,min,sec).getTime()+this.timeOffset));}
this.setInvitationParams=function(width,height,position,animation){this.invitation.setInvitationParams(width,height,position,animation);}
this.initInvitationChat=function(chatUrl,type,width,height,position){this.invitation.initChat(chatUrl,type,width,height,position);}
this.showInvitation=function(html){this.invitation.show(html,x,y);}
this.hideInvitation=function(invitationAgentId){if(this.invitation.getIAId()===invitationAgentId){this.invitation.hide();}}
this.closeInvitation=function(){this.invitation.hide();var closedInvitations=this.getCookie("closedInvitations");if(closedInvitations!=null&&closedInvitations!=""){closedInvitations+=','+this.invitation.getId();}else{closedInvitations=this.invitation.getId();}
this.setCookie("closedInvitations",closedInvitations);this.removeInviteScript();this.addInviteRefuseScript(this.invitation.getId());}
this.closeBubbleButton=function(buttonId){if(buttonId=="{$buttonid}"){return;}
var closedBubbleButtons=this.getCookie('closedBubbleButtons');if(closedBubbleButtons!=null&&closedBubbleButtons!=""){closedBubbleButtons+=','+buttonId;}else{closedBubbleButtons=buttonId;}
this.setCookie("closedBubbleButtons",closedBubbleButtons,60);}
this.initInvitation=function(invitationId,timeoutInSec){this.invitationTimers[timeoutInSec]=setTimeout("LiveAgentTracker.sendInviteRequest('"+invitationId+"')",timeoutInSec*1000);}
this.clearInvitationTimes=function(){for(timeoutInSec in this.invitationTimers){clearTimeout(this.invitationTimers[timeoutInSec]);}}
this.sendInviteRequest=function(invitationId){this.removeInviteScript();this.addInviteScript(invitationId);}
this.removeInviteScript=function(){var headTag=document.getElementsByTagName("head").item(0);if(this.inviteScriptTag!=null){try{headTag.removeChild(this.inviteScriptTag);}catch(err){}
this.inviteScriptTag=null;}}
this.addInviteScript=function(invitationId){var headTag=document.getElementsByTagName("head").item(0);this.inviteScriptTag=document.createElement("script");this.inviteScriptTag.setAttribute("type","text/javascript");this.inviteScriptTag.setAttribute("src",this.serverUrl+'scripts/invite.php?sid='+this.sid+'&iid='+invitationId+"&chs="+escape(this.getCharset())+"&bu="+this.encodeUrl(this.serverUrl));headTag.appendChild(this.inviteScriptTag);}
this.addInviteRefuseScript=function(invitationId){var headTag=document.getElementsByTagName("head").item(0);this.inviteScriptTag=document.createElement("script");this.inviteScriptTag.setAttribute("type","text/javascript");this.inviteScriptTag.setAttribute("src",this.serverUrl+'scripts/invite.php?sid='+this.sid+'&a=r&iid='+invitationId+"&chs="+escape(this.getCharset())+"&bu="+this.encodeUrl(this.serverUrl));headTag.appendChild(this.inviteScriptTag);}
this.setRunningButton=function(button){this.runningButton=button;}
this.closeEmbeddedChat=function(){if(this.chatIframeElement!=null){var element=this.chatIframeElement;this.chatIframeElement=null;element.style.display='none';}
if(this.runningButton!=null){this.runningButton.closeChatIframe();this.runningButton.initialize();this.runningButton=null;}
if(this.isRunningEmbeddedChatFromIvitation()){this.invitation.closeChatIframe();}
this.setCookie('LaRunningChat','');}
this.onEmbeddedChatOpened=function(cid){if(this.getCookie('LaRunningChat')!=null&&this.getCookie('LaRunningChat')!=''){return;}
if(this.runningButton!=null){this.setCookie('LaRunningChat',cid+"||"+this.runningButton.getId()+"||"+this.runningButton.getChatIframeStyle());}
if(this.isRunningEmbeddedChatFromIvitation()){this.setCookie('LaRunningChat',cid+"||"+this.invitation.getId()+"||"+this.invitation.getChatIframeStyle());}}
this.isRunningEmbeddedChatFromIvitation=function(){if(this.invitation!=null&&this.invitation.isRunningEmbeddedChat()){return true;}
return false;}
this.getHostname=function(url){var list=url.split('/');if(list[0].substring(0,4)=='http'){return list[0]+'//'+list[2]}
return'http://'+list[0];}
this.startTimer(250);this.loginUserOnServer=function(authHash){var curTime=new Date();var elementId='LaRemoteAuthIFrame_'+curTime.getTime();var authFrame=this.getRemoteAuthIFrame(elementId);document.body.appendChild(authFrame);authFrame.setAttribute('src',this.serverUrl+"scripts/apiAuthUser.php?ahash="+authHash+"&act=login");setTimeout("try{document.body.removeChild(document.getElementById('"+elementId+"'));}catch(e){}",5000);}
this.getRemoteAuthIFrame=function(){var authFrame=document.createElement('iframe');authFrame.style.width='1px';authFrame.style.height='1px';authFrame.style.cssFloat='left';authFrame.style.left='-1000px';var curTime=new Date();var elementId='LaRemoteAuthIFrame_'+curTime.getTime();authFrame.id=elementId;return authFrame;}
this.logoutUserOnServer=function(authHash){var curTime=new Date();var elementId='LaRemoteAuthIFrame_'+curTime.getTime();var authFrame=this.getRemoteAuthIFrame(elementId);document.body.appendChild(authFrame);authFrame.setAttribute('src',this.serverUrl+"scripts/apiAuthUser.php?ahash="+authHash+"&act=logout");setTimeout("try{document.body.removeChild(document.getElementById('"+elementId+"'));}catch(e){}",5000);}
this.initRunningChat=function(cid,chatRunningUrl,chatStyle,dateChanged){if(this.chatIframeElement==null){this.chatIframeElement=document.createElement('iframe');document.body.appendChild(this.chatIframeElement);}
this.chatIframeElement.style.cssText=chatStyle;this.chatIframeElement.setAttribute('src',chatRunningUrl+"?cid="+cid+"&ie="+encodeURIComponent(LiveAgentTrackerXD.getIeVersion())+"&t="+dateChanged+"#"+encodeURIComponent(document.location.href));}
this.initInviteChat=function(cid,invitationId,chatRunningUrl,width,height,position){if(this.chatIframeElement==null){this.chatIframeElement=document.createElement('iframe');document.body.appendChild(this.chatIframeElement);}
this.setChatIframeStyle(this.chatIframeElement,'999999',width,height,position);this.chatIframeElement.setAttribute('src',chatRunningUrl+"?cid="+cid+"&fi=Y#"+encodeURIComponent(document.location.href));this.setCookie('LaRunningChat',cid+"||"+invitationId+"||"+this.chatIframeElement.style.cssText);}
this.startChatFromContactForm=function(cid){if(this.runningButton!=null){this.runningButton.closeFormIframe();this.runningButton.startChat(cid,x,y);}}
this.popOutChat=function(cid){this.closeEmbeddedChat();}
this.closeForm=function(){if(this.runningButton!=null){this.runningButton.closeFormIframe();this.runningButton.initialize();this.runningButton=null;}}
this.setChatIframeStyle=function(element,zIndex,width,height,position){element.style.display='block';element.style.position='fixed';element.style.zIndex=zIndex;element.style.border='0px';element.style.overflow='hidden';element.style.width=width+'px';element.style.height=height+'px';if(position=='BR'){element.style.bottom='0px';element.style.right='0px';element.style.marginRight='2px';}
if(position=='BL'){element.style.bottom='0px';element.style.left='0px';element.style.marginLeft='2px';}
if(position=='TR'){element.style.top='0px';element.style.right='0px';element.style.smarginRight='2px';}
if(position=='TL'){element.style.top='0px';element.style.left='0px';element.style.marginLeft='2px';}}
LiveAgentTrackerXD.receiveMessage(function(message){if(message.data=='closeEmbeddedChat'){LiveAgentTracker.closeEmbeddedChat();}else if(message.data.substring(0,20)=='onEmbeddedChatOpened'){LiveAgentTracker.onEmbeddedChatOpened(message.data.substring(20));}else if(message.data.substring(0,9)=='startChat'){LiveAgentTracker.startChatFromContactForm(message.data.substring(9));}else if(message.data=='closeForm'){LiveAgentTracker.closeForm();}else if(message.data.substring(0,18)=='popOutEmbeddedChat'){LiveAgentTracker.popOutChat(message.data.substring(18));}else if(message.data.substring(0,10)=='openButton'){LiveAgentTracker.clickVirtualButton(message.data.substring(10));}else if(message.data.substring(0,6)=='widget'){var regex=/widget\((.*?)\)_(.*)/g;var parts=regex.exec(message.data);if(LiveAgentTracker.widgets[parts[1]]!=undefined){LiveAgentTracker.widgets[parts[1]].recieveMessage(parts[2]);}}},this.getHostname(this.serverUrl));window.onresize=function(){if(LiveAgentTracker.runningButton!=null){LiveAgentTracker.runningButton.onWindowResize();}}}}

LiveAgentChatBaseObject=function(){};LiveAgentChatBaseObject.prototype.windowWidth;LiveAgentChatBaseObject.prototype.windowHeight;LiveAgentChatBaseObject.prototype.windowPosition;LiveAgentChatBaseObject.prototype.initHtml=function(html){if("https:"==document.location.protocol){html=html.replace('http://','https://');}
return html;}
LiveAgentChatBaseObject.prototype.setChatWindowParams=function(width,height,position){this.windowWidth=width;this.windowHeight=height;this.windowPosition=position;}
LiveAgentChatBaseObject.prototype.createChatWindowParams=function(x,y){var size=this.getWindowSize();var width=this.windowWidth;var height=this.windowHeight;if(this.windowWidth>size[0]){width=size[0];}
if(this.windowHeight>size[1]){height=size[1];}
var left=0;var top=size[1]/2-height/2;if(this.windowPosition=='R'){left=size[0]-width;}
if(this.windowPosition=='C'){left=size[0]/2-width/2;}
if(this.windowPosition=='O'){left=x;top=y;}
return'width='+width+',height='+height
+',left='+left+',top='+top+',scrollbars=yes';}
LiveAgentChatBaseObject.prototype.openPopupWindow=function(serverUrl,params,x,y,isMobile){if(isMobile){window.open(serverUrl+'?'+params,"_blank");}else{window.open(serverUrl+'?'+params,"_blank",this.createChatWindowParams(x,y));}}
LiveAgentChatBaseObject.prototype.getWindowSize=function(){var wWidth=0,wHeight=0;if(typeof(window.innerWidth)=='number'){wWidth=window.innerWidth;wHeight=window.innerHeight;}else if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){wWidth=document.documentElement.clientWidth;wHeight=document.documentElement.clientHeight;}else if(document.body&&(document.body.clientWidth||document.body.clientHeight)){wWidth=document.body.clientWidth;wHeight=document.body.clientHeight;}
return[wWidth,wHeight];}

LiveAgentButton=function(id,element,note){this.id=id;this.note=note;if(this.note==undefined){this.note='';}
this.elementId="b_"+id+"_"
+Math.round((Math.random()*1000)).toString();this.initialized='N';this.formAttributes=null;this.chatAttributes=null;this.formIframeElement=null;this.formBlockingElement=null;this.chatIframeElement=null;this.lock=0;this.lockState={lock:0,unlock:1,waitForUnlock:-1};this.buttonDiv=null;if(id!==undefined){this.insertButtonElement(element);}
try{this.originalOverflowSetting=document.body.style.overflow;if(this.originalOverflowSetting==""){this.originalOverflowSetting="auto";}}catch(e){this.originalOverflowSetting=undefined;}
this.winW=0;this.winH=0;this.dateChanged=0;};LiveAgentButton.prototype=new LiveAgentChatBaseObject;LiveAgentButton.prototype.constructor=LiveAgentButton;LiveAgentButton.prototype.initHtml=function(html){if("https:"==document.location.protocol){html=html.replace('http://','https://');}
html=html.replace('{$buttonid}',this.id);closedBubbleButtons=LiveAgentTracker.getCookie('closedBubbleButtons');if(closedBubbleButtons!=null&&closedBubbleButtons.indexOf(this.id)!=-1){return html.replace('{$display}','none');}
return html.replace('{$display}','block');}
LiveAgentButton.prototype.isLocked=function(){return(this.lock==this.lockState['lock'])?true:false;}
LiveAgentButton.prototype.isWaiting=function(){return(this.lock==this.lockState['waitForUnlock'])?true:false;}
LiveAgentButton.prototype.insertButtonElement=function(element){this.buttonDiv=document.createElement('div');this.buttonDiv.setAttribute('id',this.elementId);var me=this;this.buttonDiv.onclick=function(){if(me.isLocked()||me.isWaiting()){me.lock=me.lockState['waitForUnlock'];}else{LiveAgentTracker.openButtonChat(this.getAttribute('id'));}};this.buttonDiv.style.cursor='pointer';if(element==undefined){element=document.getElementById('la_x2s6df8d');element.parentNode.insertBefore(this.buttonDiv,element.nextSibling);}else{element.parentNode.insertBefore(this.buttonDiv,element.nextSibling);element.parentNode.removeChild(element);}}
LiveAgentButton.prototype.onClick=function(){LiveAgentTracker.openButtonChat(this.elementId);}
LiveAgentButton.prototype.recomputeWindowSize=function(){if(document.body&&document.body.offsetWidth){this.winW=document.body.offsetWidth;this.winH=document.body.offsetHeight;}
if(document.compatMode=='CSS1Compat'&&document.documentElement&&document.documentElement.offsetWidth){this.winW=document.documentElement.offsetWidth;this.winH=document.documentElement.offsetHeight;}
if(window.innerWidth&&window.innerHeight){this.winW=window.innerWidth;this.winH=window.innerHeight;}}
LiveAgentButton.prototype.initContactForm=function(formUrl,width,height){this.formAttributes=new PostAssoc();this.formAttributes['formurl']=formUrl;this.formAttributes['width']=width;this.formAttributes['height']=height;}
LiveAgentButton.prototype.initChat=function(chatUrl,type,width,height,position){this.chatAttributes=new PostAssoc();this.chatAttributes['chaturl']=chatUrl;this.chatAttributes['type']=type;this.chatAttributes['width']=width;this.chatAttributes['height']=height;this.chatAttributes['position']=position;if(type=='P'||type=='M'){this.lock=this.lockState['unlock'];}}
LiveAgentButton.prototype.setHtml=function(html){if(document.getElementById(this.elementId)!=null){document.getElementById(this.elementId).innerHTML=this.initHtml(html);}}
LiveAgentButton.prototype.setDateChanged=function(time){this.dateChanged=time;}
LiveAgentButton.prototype.open=function(serverUrl,x,y){if(this.formIframeElement!=null){this.setFormIframeStyle(this.formIframeElement);this.setFormBlockingStyle(this.formBlockingElement);this.setBodyStyleOverflow('hidden');LiveAgentTracker.setRunningButton(this);LiveAgentTrackerXD.postMessage("setParam_visitorId_"+LiveAgentTracker.getVisitorId(),this.formIframeElement.src,this.formIframe);LiveAgentTrackerXD.postMessage("setParam_note_"+this.note,this.formIframeElement.src,this.formIframe);LiveAgentTrackerXD.postMessage("start",this.formIframeElement.src,this.formIframe);return;}
if(this.chatIframeElement!=null){this.setChatIframeStyle(this.chatIframeElement);LiveAgentTracker.setRunningButton(this);LiveAgentTrackerXD.postMessage("setParam_visitorId_"+LiveAgentTracker.getVisitorId(),this.chatIframeElement.src,this.chatIframe);LiveAgentTrackerXD.postMessage("setParam_note_"+this.note,this.chatIframeElement.src,this.chatIframe);LiveAgentTrackerXD.postMessage("start",this.chatIframeElement.src,this.chatIframe);return;}
if(this.chatAttributes!=null&&(this.chatAttributes['type']=='P'||this.chatAttributes['type']=='M')){this.openPopupWindow(this.chatAttributes['chaturl'],this.createChatWindowServerParams('cwid='+this.id+'&t='+this.dateChanged),x,y,this.chatAttributes['type']=='M');LiveAgentTracker.setRunningButton(null);}}
LiveAgentButton.prototype.startChat=function(cid,x,y){if(this.chatIframeElement!=null){this.setChatIframeStyle(this.chatIframeElement);LiveAgentTracker.setRunningButton(this);LiveAgentTrackerXD.postMessage("setParam_visitorId_"+LiveAgentTracker.getVisitorId(),this.chatIframeElement.src,this.chatIframe);LiveAgentTrackerXD.postMessage("start"+cid,this.chatIframeElement.src,this.chatIframe);return;}
if(this.chatAttributes!=null&&this.chatAttributes['type']=='P'){this.openPopupWindow(this.chatAttributes['chaturl'],this.createChatWindowServerParams('cid='+cid+'&t='+this.dateChanged),x,y);LiveAgentTracker.setRunningButton(null);}}
LiveAgentButton.prototype.createChatWindowServerParams=function(params){if(this.note!=undefined&&this.note!=''){params+='&note='+escape(this.note);}
params+="&pt="+encodeURIComponent(document.title);return params;}
LiveAgentButton.prototype.toJson=function(){return'{"t":"b","i":"'+this.id+'","e":"'+this.elementId+'","s":"'
+this.initialized+'"}';}
LiveAgentButton.prototype.unlock=function(){if(this.isWaiting()){this.onClick();}
this.lock=this.lockState['unlock'];}
LiveAgentButton.prototype.initialize=function(){this.initialized='Y';if(this.formAttributes!=null){if(this.formBlockingElement==null){this.formBlockingElement=document.createElement('div');this.setInvisibleStyle(this.formBlockingElement);document.body.appendChild(this.formBlockingElement);}
if(this.formIframeElement==null){this.formIframeElement=document.createElement('iframe');this.addOnLoadHandler(this.formIframeElement);this.setInvisibleStyle(this.formIframeElement);this.formIframeElement.setAttribute('src',this.formAttributes['formurl']+"?cwid="+this.id+'&t='+this.dateChanged
+"&ie="+encodeURIComponent(LiveAgentTrackerXD.getIeVersion())
+"&pt="+encodeURIComponent(document.title)+"#"
+encodeURIComponent(document.location.href));document.body.appendChild(this.formIframeElement);this.formIframe=frames[frames.length-1];}}
if(this.chatAttributes!=null){if(this.chatAttributes['type']=='E'){if(this.chatIframeElement==null){this.chatIframeElement=document.createElement('iframe');this.addOnLoadHandler(this.chatIframeElement);this.setInvisibleStyle(this.chatIframeElement);this.chatIframeElement.setAttribute('src',this.chatAttributes['chaturl']+"?cwid="+this.id+'&t='+this.dateChanged
+"&ie="+encodeURIComponent(LiveAgentTrackerXD.getIeVersion())
+"&pt="+encodeURIComponent(document.title)
+"#"+encodeURIComponent(document.location.href));document.body.appendChild(this.chatIframeElement);this.chatIframe=frames[frames.length-1];}}}}
LiveAgentButton.prototype.closeChatIframe=function(){if(this.chatIframeElement!=null){var element=this.chatIframeElement;this.chatIframeElement=null;this.lock=0;element.style.display='none';}}
LiveAgentButton.prototype.setBodyStyleOverflow=function(overflow){if(this.originalOverflowSetting!=undefined){try{document.body.style.overflow=overflow;}catch(e){}}}
LiveAgentButton.prototype.closeFormIframe=function(){if(this.formIframeElement!=null){document.body.removeChild(this.formIframeElement);this.formIframeElement=null;this.formIframe=null;document.body.removeChild(this.formBlockingElement);this.formBlockingElement=null;this.setBodyStyleOverflow(this.originalOverflowSetting);}}
LiveAgentButton.prototype.setInvisibleStyle=function(element){element.style.display='none';}
LiveAgentButton.prototype.addOnLoadHandler=function(iframe){var me=this;if(iframe.attachEvent){iframe.attachEvent('onload',function(){me.unlock();});}else{iframe.onload=function(){me.unlock();};}}
LiveAgentButton.prototype.getId=function(){return this.id;}
LiveAgentButton.prototype.onWindowResize=function(){if(this.formIframeElement==null||this.formIframeElement.style.display=='none'){return;}
this.setFormIframeStyle(this.formIframeElement);}
LiveAgentButton.prototype.setFormIframeStyle=function(element){this.recomputeWindowSize();var width=this.formAttributes['width'];var height=this.formAttributes['height'];if(this.winH>0&&this.winH*0.9<height){height=Math.round(this.winH*0.9);}
if(this.winW>0&&this.winW*0.9<width){width=Math.round(this.winW*0.9);}
if(width<200){width=200;}
if(height<300){height=300;}
LiveAgentTrackerXD.postMessage("setSize_"+width+"_"+height,this.formIframeElement.src,this.formIframe);element.style.display='block';element.style.position='fixed';element.style.zIndex='9999999';element.style.top='50%';element.style.left='50%';element.style.marginLeft="-"+Math.round(width/2)+"px";element.style.marginTop="-"+Math.round(height/2)+"px";element.style.width=width+"px";element.style.height=height+"px";element.style.overflow='hidden';element.style.border='0 none transparent';}
LiveAgentButton.prototype.setFormBlockingStyle=function(element){element.style.display='block';element.style.position='fixed';element.style.zIndex='999998';element.style.top='0px';element.style.left='0px';element.style.width='100%';element.style.height='100%';element.style.backgroundColor='#000';element.style.opacity='0.6';element.style.filter='alpha(opacity = 60)';element.style.cursor='default';}
LiveAgentButton.prototype.setChatIframeStyle=function(element){LiveAgentTracker.setChatIframeStyle(element,'999999',this.chatAttributes['width'],this.chatAttributes['height'],this.chatAttributes['position']);}
LiveAgentButton.prototype.createChatWindowParams=function(x,y){var size=this.getWindowSize();var width=this.chatAttributes['width'];var height=this.chatAttributes['height'];if(this.chatAttributes['width']>size[0]){width=size[0];}
if(this.chatAttributes['height']>size[1]){height=size[1];}
var left=0;var top=size[1]/2-height/2;if(this.chatAttributes['position']=='R'){left=size[0]-width;}
if(this.chatAttributes['position']=='C'){left=size[0]/2-width/2;}
if(this.chatAttributes['position']=='O'){left=x;top=y;}
return'width='+width+',height='+height+',left='+left+',top='
+top+',scrollbars=yes';}
LiveAgentButton.prototype.getChatIframeStyle=function(){if(this.chatIframeElement!=null){return this.chatIframeElement.style.cssText;}
return'';}

LiveAgentVirtualButton=function(id,element,note){this.id=id;this.note=note;this.elementId="b_"+id+"_"
+Math.round((Math.random()*1000)).toString();this.initialized='N';this.formAttributes=null;this.chatAttributes=null;this.formIframeElement=null;this.formBlockingElement=null;this.chatIframeElement=null;this.insertButtonElement(element);try{this.originalOverflowSetting=document.body.style.overflow;if(this.originalOverflowSetting==""){this.originalOverflowSetting="auto";}}catch(e){}
this.winW=0;this.winH=0;};LiveAgentVirtualButton.prototype=new LiveAgentButton;LiveAgentVirtualButton.prototype.constructor=LiveAgentButton;LiveAgentVirtualButton.prototype.setHtml=function(html){}

LiveAgentInvitation=function(cid,iaid,dateChanged){this.id=cid;this.iaid=iaid;this.width="200";this.height="200";this.position="C";this.animation="N";this.scrollX=0;this.scrollY=0;this.element;this.chatAttributes=null;this.chatIframe=null;this.chatIframeElement=null;this.runningEmbeddedChat=false;this.dateChanged=dateChanged;this.horizontalConstant=0;this.verticalConstant=0;};LiveAgentInvitation.prototype=new LiveAgentChatBaseObject;LiveAgentInvitation.prototype.constructor=LiveAgentInvitation;LiveAgentInvitation.prototype.insertInvitationElement=function(){if(this.element==undefined||this.element==null){this.element=document.createElement('div');this.element.setAttribute('id','_invitationcode');document.body.appendChild(this.element);}}
LiveAgentInvitation.prototype.setHtml=function(html){this.element.innerHTML=this.initHtml(html);}
LiveAgentInvitation.prototype.setInvitationParams=function(width,height,position,animation){this.width=width;this.height=height;this.position=position;this.animation=animation;}
LiveAgentInvitation.prototype.setPositionConstants=function(horizontalConstant,verticalConstant){this.horizontalConstant=horizontalConstant;this.verticalConstant=verticalConstant;}
LiveAgentInvitation.prototype.setValidTo=function(validTo){var self=this;setTimeout(function(){self.hide();},validTo.getTime()-new Date().getTime());}
LiveAgentInvitation.prototype.open=function(x,y){if(this.chatIframeElement!=null){this.setChatIframeStyle(this.chatIframeElement);LiveAgentTrackerXD.postMessage("start",this.chatIframeElement.src,this.chatIframe);this.runningEmbeddedChat=true;}
if(this.chatAttributes!=null&&this.chatAttributes['type']=='P'){this.openPopupWindow(this.chatAttributes['chaturl'],this.createChatWindowServerParams('iaid='+this.iaid+'&t='+this.dateChanged),x,y);}
this.hide();}
LiveAgentInvitation.prototype.hide=function(){if(this.element!=undefined){document.body.removeChild(this.element);this.element=null;}}
LiveAgentInvitation.prototype.show=function(html,x,y){this.insertInvitationElement();this.setHtml(html);if(this.animation=='Y'){this.animate(1000,1,x,y);}else{this.setStyle(100,x,y);}}
LiveAgentInvitation.prototype.animate=function(time,step,x,y){this.setStyle(step,x,y);if(step<100){this.setAnimateTimeout(time,++step,x,y);}}
LiveAgentInvitation.prototype.setAnimateTimeout=function(time,step,x,y){var self=this;setTimeout(function(){self.animate(time,step,x,y);},time/100);}
LiveAgentInvitation.prototype.setStyle=function(step,x,y){this.element.style.position='fixed';this.element.style.zIndex='99999';if(this.position=='TL'){this.setPosition(Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px',null,null,(0+this.horizontalConstant)+'px');}
if(this.position=='TC'){this.setPosition(Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px',null,null,'50%');this.setMargin(null,Math.round(-this.width/2)+'px');}
if(this.position=='TR'){this.setPosition(Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px',(0+this.horizontalConstant)+'px',null,null);}
if(this.position=='BL'){this.setPosition(null,null,Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px',(0+this.horizontalConstant)+'px');}
if(this.position=='BC'){this.setPosition(null,null,Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px','50%');this.setMargin(null,Math.round(-this.width/2)+'px');}
if(this.position=='BR'){this.setPosition(null,(0+this.horizontalConstant)+'px',Math.round(-this.height+this.height/100*step+this.verticalConstant)+'px',null);}
if(this.position=='O'){this.initScrollXY();xAnimation=-this.height/25+this.height/100*step/25;this.setPosition((y-this.scrollY+this.verticalConstant)+'px',null,null,Math.round(x-this.scrollX+(step%2==0?+xAnimation:-xAnimation)+this.horizontalConstant)+'px');}
if(this.position=='CL'){this.setPosition('50%',null,null,Math.round(-this.width+this.width/100*step+this.horizontalConstant)+'px');this.setMargin(Math.round(-this.height/2)+'px',null);}
if(this.position=='CR'){this.setPosition('50%',Math.round(-this.width+this.width/100*step+this.horizontalConstant)+'px',null,null);this.setMargin(Math.round(-this.height/2)+'px',null);}
if(this.position=='C'){this.setPosition('50%',null,null,'50%');xAnimation=-this.height/25+this.height/100*step/25;this.setMargin(Math.round(-this.height/2)+'px',Math.round(-this.width/2+(step%2==0?+xAnimation:-xAnimation))+'px');}}
LiveAgentInvitation.prototype.setPosition=function(top,right,bottom,left){if(top!=null){this.element.style.top=top;}
if(right!=null){this.element.style.right=right;}
if(bottom!=null){this.element.style.bottom=bottom;}
if(left!=null){this.element.style.left=left;}}
LiveAgentInvitation.prototype.setMargin=function(top,left){if(top!=null){this.element.style.marginTop=top;}
if(left!=null){this.element.style.marginLeft=left;}}
LiveAgentInvitation.prototype.initScrollXY=function(){if(typeof(window.pageYOffset)=='number'){this.scrollY=window.pageYOffset;this.scrollX=window.pageXOffset;}else if(document.body&&(document.body.scrollLeft||document.body.scrollTop)){this.scrollY=document.body.scrollTop;this.scrollX=document.body.scrollLeft;}else if(document.documentElement&&(document.documentElement.scrollLeft||document.documentElement.scrollTop)){this.scrollY=document.documentElement.scrollTop;this.scrollX=document.documentElement.scrollLeft;}}
LiveAgentInvitation.prototype.initChat=function(chatUrl,type,width,height,position){this.chatAttributes=new PostAssoc();this.chatAttributes['chaturl']=chatUrl;this.chatAttributes['type']=type;this.chatAttributes['width']=width;this.chatAttributes['height']=height;this.chatAttributes['position']=position;if(this.chatAttributes['type']=='E'){if(this.chatIframeElement==null){this.chatIframeElement=document.createElement('iframe');this.setInvisibleStyle(this.chatIframeElement);this.chatIframeElement.setAttribute('src',this.chatAttributes['chaturl']+"?iaid="+this.iaid+"&t="+this.dateChanged
+"&ie="+encodeURIComponent(LiveAgentTrackerXD.getIeVersion())
+"&pt="+encodeURIComponent(document.title)
+"#"+encodeURIComponent(document.location.href));document.body.appendChild(this.chatIframeElement);this.chatIframe=frames[frames.length-1];}}}
LiveAgentInvitation.prototype.setInvisibleStyle=function(element){element.style.display='none';}
LiveAgentInvitation.prototype.setChatIframeStyle=function(element){LiveAgentTracker.setChatIframeStyle(element,'999999',this.chatAttributes['width'],this.chatAttributes['height'],this.chatAttributes['position']);}
LiveAgentInvitation.prototype.createChatWindowServerParams=function(params){params+="&pt="+encodeURIComponent(document.title);return params;}
LiveAgentInvitation.prototype.isRunningEmbeddedChat=function(){return this.runningEmbeddedChat;}
LiveAgentInvitation.prototype.getChatIframeStyle=function(){if(this.chatIframeElement!=null){return this.chatIframeElement.style.cssText;}
return'';}
LiveAgentInvitation.prototype.getId=function(){return this.id;}
LiveAgentInvitation.prototype.getIAId=function(){return this.iaid;}
LiveAgentInvitation.prototype.closeChatIframe=function(){if(this.chatIframeElement!=null){var element=this.chatIframeElement;this.chatIframeElement=null;this.runningEmbeddedChat=false;element.style.display='none';}}
LiveAgentInvitation.prototype.createChatWindowParams=function(x,y){var size=this.getWindowSize();var width=this.chatAttributes['width'];var height=this.chatAttributes['height'];if(this.chatAttributes['width']>size[0]){width=size[0];}
if(this.chatAttributes['height']>size[1]){height=size[1];}
var left=0;var top=size[1]/2-height/2;if(this.chatAttributes['position']=='R'){left=size[0]-width;}
if(this.chatAttributes['position']=='C'){left=size[0]/2-width/2;}
if(this.chatAttributes['position']=='O'){left=x;top=y;}
return'width='+width+',height='+height+',left='+left+',top='
+top+',scrollbars=yes';}

LiveAgentKbSearchWidget=function(id,element){this.id=id;this.elementId="kb_"+id+"_"
+Math.round((Math.random()*1000)).toString();this.initialized='N';this.iframeElement=null;this.iframe=null;this.dateChanged=0;};LiveAgentKbSearchWidget.prototype=new LiveAgentChatBaseObject;LiveAgentKbSearchWidget.prototype.constructor=LiveAgentKbSearchWidget;LiveAgentKbSearchWidget.prototype.toJson=function(){return'{"t":"kb","i":"'+this.id+'","e":"'+this.elementId+'","s":"'
+this.initialized+'"}';}
LiveAgentKbSearchWidget.prototype.setDateChanged=function(time){this.dateChanged=time;}
LiveAgentKbSearchWidget.prototype.initWidget=function(url,width,height,position){this.url=url;this.width=width;this.height=height;this.position=position;}
LiveAgentKbSearchWidget.prototype.initialize=function(){this.initialized='Y';if(this.iframeElement==null){this.iframeElement=document.createElement('iframe');this.setFrameStyle(this.iframeElement,this.width,this.height);this.iframeElement.setAttribute('src',this.url+"?id="+this.id+"&eid="+this.elementId+"&t="+this.dateChanged+"#"
+encodeURIComponent(document.location.href));document.body.appendChild(this.iframeElement);this.iframe=frames[frames.length-1];}}
LiveAgentKbSearchWidget.prototype.recieveMessage=function(message){if(message=='close'){this.close();}
if(message.substring(0,6)=='resize'){var parts=message.split('_',3);this.setFrameStyle(this.iframeElement,parts[1],parts[2]);}}
LiveAgentKbSearchWidget.prototype.close=function(){if(this.iframeElement!=null){document.body.removeChild(this.iframeElement);this.iframeElement=null;this.iframe=null;}}
LiveAgentKbSearchWidget.prototype.setFrameStyle=function(element,width,height){LiveAgentTracker.setChatIframeStyle(element,'999997',width,height,this.position);}

LiveAgentInPageForm=function(id,element,note,width,height){this.id=id;this.note=note;if(this.note==undefined){this.note='';}
this.elementId="b_"+id+"_"+Math.round((Math.random()*1000)).toString();this.initialized='N';this.height=height;this.width=width;this.dateChanged=0;this.iframeElement=null;this.initElement(element);};LiveAgentInPageForm.prototype.constructor=LiveAgentInPageForm;LiveAgentInPageForm.prototype.initElement=function(element){this.iframeElement=document.createElement('iframe');this.iframeElement.setAttribute('id',this.elementId);this.iframeElement.style.maxWidth="100%";this.iframeElement.style.width="0px";this.iframeElement.style.height="0px";this.iframeElement.style.overflow='hidden';this.iframeElement.style.border='0 none transparent';element.parentNode.insertBefore(this.iframeElement,element.nextSibling);element.parentNode.removeChild(element);}
LiveAgentInPageForm.prototype.initWidget=function(formUrl,width,height){this.iframeElement.setAttribute('src',formUrl+"?cwid="+this.id+"&t="+this.dateChanged
+"&pt="+encodeURIComponent(document.title)
+((this.note!=undefined&&this.note!='')?'&note='+escape(this.note):'')
+"&vid="+LiveAgentTracker.getVisitorId()
+"#"+encodeURIComponent(document.location.href));this.iframeElement.style.width=width+"px";this.iframeElement.style.height=height+"px";}
LiveAgentInPageForm.prototype.setDateChanged=function(time){this.dateChanged=time;}
LiveAgentInPageForm.prototype.initialize=function(){this.initialized='Y';}
LiveAgentInPageForm.prototype.toJson=function(){return'{"t":"f","i":"'+this.id+'","e":"'+this.elementId+'","s":"'+this.initialized+'"}';}

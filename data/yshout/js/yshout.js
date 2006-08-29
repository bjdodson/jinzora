var yS = null;
var yShout = Class.create();

// Very nifty string format function, courtesy of Nick Chapman:
// http://chapnickman.com/2006/02/10/string-formatting-in-javascript/
String.prototype.format = function() {
  var params = String.prototype.format.arguments;
  var toReturn = this;

  for (var i = 0; i < params.length; i++) {
    var regex = new RegExp('\{[' + i + ']\}', 'g');
    toReturn = toReturn.replace(regex, params[i]);
  }
 return toReturn;
}

yShout.prototype = {
	initialize: function() { },
	
	doInit: function(initVars) {

		if ($('yshout') == null) return;

		yS.initVars(initVars);
		var pars =
			'reqType=init' +
			'&yPath=' + yS.yPath;
			
		yS.Ajax (pars, yS.initialLoadDone);
	},

	initVars: function(initVars) {

		// Set to true to enable debug messages
		yS.doDebug = false;
		
		var dVars = {
			yPath: 'yshout/',
			fileIndex: 1
		};
		
		initVars = Object.extend(dVars, initVars || {});

		yS.yPath = initVars.yPath;
		yS.yUser = initVars.yUser;
		yS.shoutFile = initVars.fileIndex;
		yS.numShouts = 0;
		yS.fcActive = false;
		yS.yShoutPHP = yS.yPath + '/yshout.php';
		yS.messageID = 0;

	},

	initRefresh: function() {
		new PeriodicalExecuter(yS.refresh, yS.refreshInterval / 1000);
	},
	
	sendShout: function() {
		if (yS.fcActive) return;
		if (!yS.formValidate()) return;

		yS.saveNickname();

		var pars =  'reqType=shout' +
								'&shout=' + escape($F('yshout-shout-text')) +
								'&name=' + escape($F('yshout-shout-nickname'));
		yS.Ajax(pars, yS.parseShouts);

		$('yshout-shout-text').value = '';
		if (yS.floodTimeout) yS.floodControl();
	},

	createForm: function() {
		$('yshout').innerHTML = '<div id="yshout-shouts"></div><form id="yshout-form">' +
			'<input id="yshout-shout-nickname" value="' + yS.yUser + '" type="hidden" />' +
			'<input id="yshout-shout-text" size="12" value="" type="text" maxlength="175" class="jz_input" />' +
			' <input id="yshout-shout-button" value="Say" type="button" maxlength="175" class="jz_input" />' +
			'</form>';
	},

	createElement: function(el, eID, eValue, eType) {
		var objElement = document.createElement(el);
		if (eID) objElement.setAttribute('id', eID);
		if (eType) objElement.setAttribute('type', eType);
		if (eValue) objElement.setAttribute('value', eValue);
		return objElement;
	},

	initialLoadDone: function(request) {
		var reqText = request.responseText;

		yS.a('Initial load: ' + reqText);
		var jData = yS.parseJSON(reqText);
		yS.a('After eval: ');

		// Set the variables received from the server
		yS.shoutMaxLines = jData.options.shoutMaxLines;
		yS.floodTimeout = jData.options.floodTimeout;
		yS.refreshInterval = jData.options.refreshInterval;
		yS.showTimestamps = jData.options.showTimestamps;

		yS.createForm();
		yS.initEvtHandlers();
		yS.loadNickname();
		yS.initRefresh();
		yS.parseShouts(request);
	},

	parseJSON: function(jsonData) {
		return eval('(' + jsonData + ')');
	},

	Ajax: function(pars, func) {
		pars = pars + '&file=' + yS.shoutFile;
		
		new Ajax.Request (yS.yShoutPHP, {
			method: 'post',
			parameters: pars,
			onComplete: func
		});
	},

	parseShouts: function(request) {
		var reqText = request.responseText;
		if (reqText == null) return;
		
		yS.a('parseShouts: ' + reqText);
		var jData = yS.parseJSON(reqText);
		jData.shouts.each (
			function(shout) {
				yS.parseMessage(shout);
			}
		);
		yS.truncate();
		yS.scrollToBottom();
	},

	scrollToBottom: function() {
		$('yshout-shouts').scrollTop = 424242;
	},

	scrollToTop: function() {
		$('yshout-shouts').scrollTop = 0;
	},

	setShoutText: function(newText) {
		var shoutTextBox = $('yshout-shout-text');
		shoutTextBox.focus();
		shoutTextBox.value = newText;
	},

	toggleInfo: function(messID) {
		var elInfo = $('yshout-messageinfo' + messID);
		var elMessage = $('yshout-message' + messID);
		var elShout = $('yshout-shout' + messID);

		if (elInfo.style.display == 'none') {
			// Show info
			elMessage.style.display = 'none';
			Element.addClassName(elShout, 'yshout-shout-infovisible');
			elInfo.style.display = '';
		} else {
			// Show message
			elMessage.style.display = '';
			Element.removeClassName(elShout, 'yshout-shout-infovisible');
			elInfo.style.display = 'none';
		}
	},
	
	parseMessage: function(shout) {
		yS.messageID++;
		yS.numShouts++;
		
		var container = $('yshout-shouts');
		var nicknameJS = '', userinfo = '', messageTimestamp = '';

		if (shout.showuserinfo) {
			nicknameJS = 'onclick="javascript:yS.toggleInfo(\'' + yS.messageID + '\');"';
			userinfo = 'Hello! I sent this message on {0}, at {1}. My IP is {2}, and I\'d love to be banned. You can <a href="javascript:yS.setShoutText(\'/ban {2}\');">ban me</a>, or <a href="javascript:yS.setShoutText(\'/unban {2}\');">unban me</a> if I have already been banned! You can also <a href="javascript:yS.toggleInfo({3})">return to the message</a>. Thanks!'.format(shout.date, shout.time, shout.ipaddress, yS.messageID); 
		}

		userinfo = '<span class="yshout-messageinfo" id="yshout-messageinfo{0}">{1}</span>'.format(yS.messageID, userinfo);
		var nickname = '-<span {0} class="yshout-nickname" style="font-weight: bold; font-size:9px;">{1}:</span> '.format(nicknameJS, shout.nickname);

		if (yS.showTimestamps && shout.time) {
				//messageTimestamp = shout.time;
				//messageTimestamp = messageTimestamp.replace(' am', '');
				//messageTimestamp = messageTimestamp.replace(' pm', '');
				//messageTimestamp = '<span class="yshout-message-timestamp">[' + messageTimestamp + ']</span> ';
		}
		
		var message = '<span class="yshout-message" id="yshout-message{0}" style="font-size:9px;">{1}</span>'.format(yS.messageID,  shout.message);
	
		var shoutHTML = 
			messageTimestamp 
			+ nickname
			+ message
			+ userinfo;
			
		var objShoutDiv = document.createElement('div');


		Element.addClassName(objShoutDiv, 'yshout-shout');
		switch(shout.shouttype) {
			case 'admin':
				Element.addClassName(objShoutDiv, 'yshout-admin-shout');
				break;
			case 'system':
				Element.addClassName(objShoutDiv, 'yshout-system-shout');
				break;
			case 'user':
				break;
		}
		
		objShoutDiv.setAttribute('id', 'yshout-shout' + yS.messageID);
		objShoutDiv.innerHTML = shoutHTML;
		
		container.appendChild(objShoutDiv);
		
		$('yshout-messageinfo' + yS.messageID).style.display = 'none';
		
	},

	removeShouts: function(howMany) {
		var objContainer = $('yshout-shouts');
		for (var i = 0; i < howMany; i++) {
			if (yS.numShouts == 0) break;
			objContainer.removeChild(objContainer.firstChild);
			yS.numShouts--;
		}
	},

	truncate: function() {
		if (yS.numShouts > yS.shoutMaxLines)
			yS.removeShouts(yS.numShouts - yS.shoutMaxLines);
	},

	formValidate: function() {
		var nameValid =  yS.validateInput('yshout-shout-nickname', 'Nickname');
		var shoutValid =  yS.validateInput('yshout-shout-text', 'Shout text');
		return nameValid && shoutValid;
	},

	validateInput: function(el, defaultText) {
		elText = $F(el);
		var idValid = 'yshout-valid', idInvalid = 'yshout-invalid';
		if (elText == defaultText || elText == '') {
			Element.removeClassName(el, idValid);
			Element.addClassName(el, idInvalid);
			$(el).focus();
			return false;
		} else {
			Element.removeClassName(el, idInvalid);
			Element.addClassName(el, idValid);
			return true;
		}
	},

	saveNickname: function() {
		var expireDate = new Date();
		var saveName = yS.yUser;
		expireDate.setTime(expireDate.getTime() + 365 * 24 * 60 * 60 * 1000);
		yS.setCookie('yshoutJS', saveName, expireDate);
	},

	loadNickname: function() {
		var loadName = yS.yUser;
		if (loadName) {
			var el = $('yshout-shout-nickname');
			yS.resetInput(el);
			el.value = loadName;
		}
	},

	refresh: function() {
		yS.Ajax('reqType=refresh', yS.parseShouts);
	},

	floodControl: function() {
		yS.fcActive = true;
		$('yshout-shout-button').disabled = true;
		setTimeout(yS.fcDone, yS.floodTimeout);
	},

	fcDone: function() {
		yS.fcActive = false;
		$('yshout-shout-button').disabled = false;
	},

	onKP: function(e) {
		var key = window.event ? e.keyCode : e.which;
		if (key == 13 || key == 3) {
			yS.sendShout();
			return false;
		}	
	},

	onF: function(e) {
		var el = Event.element(e);
		yS.resetInput(el);
	},

	resetInput: function(el) {
		Event.stopObserving(el, 'focus', yS.onFObserver);
		el.value = '';
		Element.removeClassName(el, 'yshout-before-focus');
		Element.addClassName(el, 'yshout-after-focus');
	},
	
	initEvtHandlers: function() {
		$('yshout-form').onsubmit = function(){ return false; };
		yS.onFObserver = yS.onF.bindAsEventListener(yS);

		Event.observe('yshout-shout-text', 'keypress', yS.onKP.bindAsEventListener(yS));
		Event.observe('yshout-shout-nickname', 'keypress', yS.onKP.bindAsEventListener(yS));
		Event.observe('yshout-shout-text', 'focus', yS.onFObserver);
		Event.observe('yshout-shout-nickname', 'focus', yS.onFObserver);
		Event.observe('yshout-shout-button', 'click', yS.sendShout.bindAsEventListener(yS));
	},

	a: function(toSay) {
		if (!yS.doDebug) return;
		
		var objDebug = $('debug');
		 $('debug').style.display = "block";
		var objDebugP = document.createElement('p');
		var pText = document.createTextNode(toSay);
		objDebugP.appendChild(pText);
		if (objDebug.firstChild) objDebug.insertBefore(objDebugP, objDebug.firstChild);
		else objDebug.appendChild(objDebugP);
	},

	setCookie: function(name, value, expires, path, domain, secure) {
	  var curCookie = name + '=' + escape(value) +
	      ((expires) ? '; expires=' + expires.toGMTString() : '') +
	      ((path) ? '; path=' + path : '') +
	      ((domain) ? '; domain=' + domain : '') +
	      ((secure) ? '; secure' : '');
	  document.cookie = curCookie;
	},

	getCookie: function(name) {
	  var dc = document.cookie;
	  var prefix = name + '=';
	  var begin = dc.indexOf('; ' + prefix);
	  if (begin == -1) {
	    begin = dc.indexOf(prefix);
	    if (begin != 0) return null;
	  } else
	    begin += 2;
	  var end = document.cookie.indexOf(';', begin);
	  if (end == -1)
	    end = dc.length;
	  return unescape(dc.substring(begin + prefix.length, end));
	}
};

function loadYShout(initVars){
	yS = new yShout();
	yS.doInit(initVars);
}
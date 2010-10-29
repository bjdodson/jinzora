function _JX(config){ 
	// See end of file for Module instantiation.
	
	var _config = false;
	if (config) {
		if (typeof(config) == "string") {
			_config = { host: config };
		} else {
			_config = config;
		}
	}
	
	/**
	 * Create a new Junction instance.
	 * TODO: document the necessary properties of activity and actor..
	 */
	this.newJunction = function(activity, actor) {
		var _sessionID = null;
		var _hostURL = null;
		var _isActivityCreator = false;
		var query = parseUri(window.location).query;
		var i;

		if ((i = query.indexOf('jxsessionid=')) >= 0) {
			_sessionID = query.substring(i+12);
			if ((i=_sessionID.indexOf('&')) >= 0) {
				_sessionID = _sessionID.substring(0,i);
			}
			if ((i = query.indexOf('jxswitchboard=')) >= 0) {
				_hostURL = query.substring(i+14);
				if ((i=_hostURL.indexOf('&')) >= 0) {
					_hostURL = _hostURL.substring(0,i);
				}
			}
		}
		else if (activity) {
			if (activity.sessionID) {
				_sessionID = activity.sessionID;
			}
			if (activity.host) {
				_hostURL = activity.host;
			}
		}

		if (!_sessionID) {
			_sessionID = randomUUID();
			_isActivityCreator = true;
		}
		if (!_hostURL) {
			if (_config.host) {
				_hostURL = _config.host;
			}
		}

		var _actorID = randomUUID();

		var _jx = new JX.Junction(actor, _actorID, activity, _sessionID, _hostURL, _isActivityCreator);

		return _jx;

	};


	/**
	 * Create and return a new BOSH connection to a remote XMPP host.
	 */
	var createXMPPConnection = function(hostURL, onConnect) {
		var _jid='junction';
		var _pw='junction';
		var connection = new Strophe.Connection('http://' + hostURL + '/http-bind');
		connection.connect(_jid, _pw, onConnect);
		return connection;
	};


	/**
	 * Class: Junction
	 * 
	 * The principle class describing a Junction activity. 
	 * Instances should be created with JX.newJunction.
	 */
	this.Junction = Class.extend(
		{

			init: function(actor, actorID, activityDesc, sessionID, hostURL, isActivityCreator) {

				this.xmppConnection = null; // See below..
				this.activityDesc = activityDesc;
				this.sessionID = sessionID;
				this.hostURL = hostURL;
				this.actor = actor;
				this.actorID = actorID;
				this.isActivityCreator = isActivityCreator;
				this.extrasDirector = new JX.ExtrasDirector();

				this.MUC_ROOM = sessionID;
				this.MUC_COMPONENT = 'conference.' + hostURL;

				this.registerActor(this.actor);

				// Init the connection!
				var self = this;
				this.xmppConnection = createXMPPConnection(
					this.hostURL, 
					function(status){ return self._onConnect(status);});

			},


			registerActor: function(actor){
				var self = this;
				
				// Initialize the client-facing actor interface.
				// This is the principle means with which a Junction 
				// client interacts with an Activity.
				(function(actor){

					 actor.junction = self;

					 actor.leave = function() { self.disconnect(); };

					 actor.sendMessageToActor = function(actorID, msg) {
						 self.sendMessageToActor(actorID, msg);
					 };

					 actor.sendMessageToRole = function(role, msg) {
						 self.sendMessageToRole(role, msg);
					 };

					 actor.sendMessageToSession = function(msg) {
						 self.sendMessageToSession(msg);
					 };

				 })(this.actor);


				// Register all the JunctionExtra instances provided
				// by this actor.
				var extras = self.actor.initialExtras;
				if(extras != null){
					for (var i = 0; i < extras.length;  i++) {
						self.registerExtra(extras[i]);
					}
				}

			},


			// TODO: Make the logger configurable.
			logInfo: function(msg){
				if(typeof console != 'undefined'){
					console.info(msg);
				}
			},
			
			logError: function(msg){
				if(typeof console != 'undefined'){
					console.error(msg);
				}
			},

			registerExtra: function(extra) {
				extra.setActor(this.actor);
				this.extrasDirector.registerExtra(extra);
			},

			getSessionID: function() { return this.sessionID; },

			sendMessageToActor: function(actorID, msg) {
				if (this.extrasDirector.beforeSendMessageToActor(actorID, msg)) {
					this.doSendMessageToActor(actorID, msg);
				}
			},

			doSendMessageToActor: function(actorID, msg){
				if (!(typeof msg == 'object')) {
					msg = {v:msg};
				}
				msg = JSON.stringify(msg);
				this.xmppConnection.send(
					$msg({to: this.MUC_ROOM + "@" + this.MUC_COMPONENT + '/' + actorID,
						  type: "chat", id: this.xmppConnection.getUniqueId()
						 }).c("body")
						.t(msg).up()
						.c("nick", {xmlns: "http://jabber.org/protocol/nick"})
						.t(actorID).tree());
			},


			sendMessageToRole: function (role, msg) {
				if(this.extrasDirector.beforeSendMessageToRole(role, msg)){
					this.doSendMessageToRole(role, msg);
				}
			},

			doSendMessageToRole: function (role, msg) {
				if (!(typeof msg == 'object')) {
					msg = {v:msg};
				}
				if (msg.jx) {
					msg.jx.targetRole = role;
				} 
				else {
					msg.jx = { targetRole: role };
				}
				msg = JSON.stringify(msg);
				this.xmppConnection.send($msg({to: this.MUC_ROOM + "@" + this.MUC_COMPONENT,
											   type: "groupchat", id: this.xmppConnection.getUniqueId()
											  }).c("body")
										 .t(msg).up()
										 .c("nick", {xmlns: "http://jabber.org/protocol/nick"})
										 .t(this.actorID).tree());
			},


			sendMessageToSession: function (msg) {
				if (this.extrasDirector.beforeSendMessageToSession(msg)) {
					this.doSendMessageToSession(msg);
				}
			},

			doSendMessageToSession: function(msg){
				if (!(typeof msg == 'object')) {
					msg = {v:msg};
				}
				msg = JSON.stringify(msg);
				this.xmppConnection.send(
					$msg({to: this.MUC_ROOM + "@" + this.MUC_COMPONENT,
						  type: "groupchat", id: this.xmppConnection.getUniqueId()
						 }).c("body")
						.t(msg).up()
						.c("nick", {xmlns: "http://jabber.org/protocol/nick"})
						.t(this.actorID).tree());
			},


			// receive
			triggerMessageReceived: function(header, message) {
				if (this.extrasDirector.beforeOnMessageReceived(header,message)) {
					this.actor.onMessageReceived(message, header);
					this.extrasDirector.afterOnMessageReceived(header,message);
				}
			},
			
			triggerActorJoin: function(isCreator){
				// Create
				if (isCreator) {
					if (!this.extrasDirector.beforeActivityCreate()) {
						this.disconnect();
						return;
					}
					this.actor.onActivityCreate();
					this.extrasDirector.afterActivityCreate();
				}
				
				// Join
				if (!this.extrasDirector.beforeActivityJoin()) {
					this.disconnect();
					return;
				}
				this.actor.onActivityJoin();
				this.extrasDirector.afterActivityJoin();
			},

			getInvitationURI: function() {
				var params = {};
				var url = 'junction://' + this.hostURL + "/" + this.sessionID;
				if (arguments[0]) {
					params["role"] = arguments[0];
				}
				this.extrasDirector.updateInvitationParameters(params);
				for(var name in params){
					url += "&" + name + "=" + params[name];
				}
				return url;
			},

			getInvitationForWeb: function(role) { // TODO: add role parameter
				// TODO: AcSpec should be { roles: { "player": { ... } } }
				var url='';
				if (role && this.activityDesc.roles) {
					if (this.activityDesc.roles[role]) {
						var plat=this.activityDesc.roles[role].platforms;
						if (plat["web"]) {
							url = plat["web"].url.toString();
						}
					}
					if (url=='') url=document.location.toString(); // return false?
				} else {
					url=document.location.toString();
				}
				var params = 'jxsessionid=' + this.sessionID + '&jxswitchboard=' + this.hostURL;
				if (url.indexOf('?')>0) {
					return url + '&' + params;
				} else {
					return url + '?' + params;
				}
			},

			getInvitationQR: function () {
				var url;
				var size;
    			//var content = new Object();
				//content.sessionID = _sessionID;
				//content.host = _hostURL;
				//content.ad = _activityDesc;

				if (arguments.length == 0) {
					url = 'junction://' + this.hostURL + "/" + this.sessionID;
				} else if (arguments[0] != false) {
					url = 'junction://' + this.hostURL + "/" + this.sessionID + "?role="+arguments[0];
					//content.role = arguments[0];
				}
				if (arguments.length == 2) {
					size = arguments[1]+'x'+arguments[1];
				} else {
					size = '250x250';
				}

				return 'http://chart.apis.google.com/chart?cht=qr&chs='+size+'&chl='+encodeURIComponent(url);

			},

			getActorsForRole: function() {},

			getRoles:  function() {},

			disconnect: function() { this.xmppConnection.disconnect(); },


			/*******  Strophe XMPP Handlers  **********/

			_onPresence: function(msg){

				var from = Strophe.getResourceFromJid(msg.getAttribute('from'));
				var type = msg.getAttribute('type');

				// Are we the owner of this room?
				if (type == null && from == this.actorID) {

					var roomdesc = JSON.stringify(this.activityDesc);

					// Unlock room
					var form = $iq({to: this.MUC_ROOM + "@" + this.MUC_COMPONENT,type: 'set'})
						.c("query", {xmlns: "http://jabber.org/protocol/muc#owner"})
						.c("x", {xmlns: "jabber:x:data", type:"submit"})
						.c("field", {"var": "muc#roomconfig_roomdesc"})
						.c("value").t(roomdesc)
 						.up().up()
						.c("field", {"var": "muc#roomconfig_whois"})
						.c("value").t("moderators")
						.up().up()
					//.c("field", {"var": "muc#roomconfig_publicroom"})
					//.c("value").t("0")
						.tree();

					this.xmppConnection.send(form);

					if (this.isActivityCreator) {
						var roles = this.activityDesc.roles;
						if (typeof(roles) == 'object') {
							for (r in roles){
								var plats = roles[r].platforms;
								if(plats['jxservice']){
									var uri = this.getInvitationURI(r);
									JX.inviteServiceForRole(uri, this.activityDesc, r);
								}
							}
						}
					}
				}

				// TODO: 
				// Making assumption here that any presence msg must have come from
				// ourself - since we always supply the 'to' attribute when sending
				// presence.
				if (this.actor) {
					this.actor.actorID = this.actorID;
					if (this.isActivityCreator && this.actor.onActivityCreate) {
						this.triggerActorJoin(true);
					}
					if (this.actor.onActivityJoin) {
						this.triggerActorJoin(false);
					}
				}

				return false;
			},


			_onMessage: function(msg){
				var from = msg.getAttribute('from');
				var i = from.lastIndexOf('/');
				if (i >= 0) {
					from = from.substring(i+1);
				}
				var type = msg.getAttribute('type');
				var body = msg.getElementsByTagName("body")[0].childNodes[0];

				var jxheader = new Object();
				jxheader.from = from;

				if ((type == "groupchat" || type == "chat") && body) {
					try {
						var txt = body.wholeText;
						if(txt.match(/^This room/)){
							this.logInfo(txt);
						}
						else{
							var content = JSON.parse(body.wholeText);
							if (content.jx && content.jx.targetRole) {
								if (!this.actor.roles) {
									return true;
								}
								// Otherwise pass message to Actor
								for (i=0;i<this.actor.roles.length;i++) {
									if (this.actor.roles[i] == content.jx.targetRole) {
										this.triggerMessageReceived.onMessageReceived(jxheader, content);
										return true;
									}
								}
								return true;
							}
							this.triggerMessageReceived(jxheader, content);
						}
					} 
					catch (e) {
						this.logError("Failed to handle msg: '" + body.wholeText + "'   " + e.message);
					}
				}
				return true;
			},


			_onConnect: function(status){
				var self = this;

				if (status == Strophe.Status.CONNECTED) {
					var old = window.onbeforeunload;
					var discon =
						function() {
							this.xmppConnection.disconnect();
						};
					if (typeof window.onbeforeunload != 'function') {
						window.onbeforeunload = discon;
					} else {
						window.onbeforeunload = function() {
							old();
							discon();
						};
					}

					this.xmppConnection.send(
						$pres({to: this.MUC_ROOM + "@" + this.MUC_COMPONENT + "/" + this.actorID})
							.c("x", {xmlns: "http://jabber.org/protocol/muc"})
							.tree());
					
					this.xmppConnection.addHandler(function(msg){ return self._onPresence(msg); },
												   null,
												   'presence',
												   null,null,null);
					if (this.actor && this.actor.onMessageReceived) {
						this.xmppConnection.addHandler(function(msg){ return self._onMessage(msg); },
													   null,
													   'message',
													   null,null,null);
					}
				}
			}

		});




	/**
	 *  Class: JunctionExtra
	 * 
	 *  A Junction plugin. Allows clients to inject code before or after message sending,
	 *  activity joining, activity creation, etc.
	 */
	this.JunctionExtra = Class.extend(
		{
			init: function(){
				this.actor = null;	
			},

			/**
			 * This method should only be called internally.
			 * @param actor
			 */
			setActor: function(actor) {
				this.actor = actor;
			},

			/**
			 * Update the parameters that will be sent in an invitation
			 */
			updateInvitationParameters: function(params) {},

			/**
			 * Returns true if the normal event handling should proceed;
			 * Return false to stop cascading.
			 */
			beforeOnMessageReceived: function(messageHeader, jsonMsg) { return true; },

			afterOnMessageReceived: function(messageHeader, jsonMsg) {},
			beforeSendMessageToActor: function(actorID, jsonMsg) { return this.beforeSendMessage(jsonMsg); },
			beforeSendMessageToRole: function(role, jsonMsg) { return this.beforeSendMessage(jsonMsg); },
			beforeSendMessageToSession: function(jsonMsg) { return this.beforeSendMessage(jsonMsg); },

			/**
			 * Convenience method to which, by default, all message sending methods call through.
			 * @param msg
			 * @return
			 */
			beforeSendMessage: function(jsonMsg) { return true; },

			/**
			 * Called before an actor joins an activity.
			 * Returning false aborts the attempted join.
			 */
			beforeActivityJoin: function() { return true;},
			
			afterActivityJoin: function() {},
			
			/**
			 * Called before an actor joins an activity.
			 * Returning false aborts the attempted join.
			 */
			beforeActivityCreate: function() { return true; },
			
			afterActivityCreate: function() {},

			/**
			 * Returns an integer priority for this Extra.
			 * Lower priority means closer to switchboard;
			 * Higher means closer to actor.
			 */
			getPriority: function() { return 20; }


		});



	/**
	 *  Class: ExtrasDirector
	 * 
	 *  An aggregate of JunctionExtras. Implements the JunctionExtra interface,
	 *  allows Junction to conveniantly interact with all registered extras.
	 */
	this.ExtrasDirector = Class.extend(
		{

			init: function(){
				this.extras = [];					
			},

			/**
			 * Iterator is applied to each Extra.
			 * @param iterator A function that takes two parametars, the item and its index
			 */
			each: function(iterator){
				var len = this.extras.length;
				for(var i = 0; i < len; i++){
					iterator(this.extras[i], i); 
				}
			},
			
			/**
			 * Iterator is applied to each Extra in reverse order.
			 * @param iterator A function that takes two parametars, the item and its index
			 */
			eachInReverse: function(iterator){
				var len = this.extras.length;
				for(var i = len - 1; i > -1; i--){
					iterator(this.extras[i], i); 
				}
			},

			/**
			 * Adds an Extra to the set of executed extras.
			 * @param extra
			 */
			registerExtra: function(extra){
				this.extras.push(extra);
				this.extras.sort(function(a,b){ 
									 var p1 = a.getPriority();
									 var p2 = b.getPriority();
									 if(p1 < p2) return -1;
									 if(p1 > p2) return 1;
									 return 0;});
			},


			/**
			 * Returns true if onMessageReceived should be called in the usual way.
			 * @param header
			 * @param message
			 * @return
			 */
			beforeOnMessageReceived: function(header, message) {
				var cont = true;
				this.eachInReverse(
					function(ex, i){
						if (!ex.beforeOnMessageReceived(header, message)){
							cont = false;
						}
					});
				return cont;
			},


			afterOnMessageReceived: function(header, message) {
				this.eachInReverse(
					function(ex, i){
						ex.afterOnMessageReceived(header, message);
					});
			},


			beforeSendMessageToActor: function(actorID, message) {
				var cont = true;
				this.each(
					function(ex, i){
						if (!ex.beforeSendMessageToActor(actorID, message)){
							cont = false;
						}
					});
				return cont;
			},


			beforeSendMessageToRole: function(role, message) {
				var cont = true;
				this.each(
					function(ex, i){
						if (!ex.beforeSendMessageToRole(role, message)){
							cont = false;
						}
					});
				return cont;
			},

			beforeSendMessageToSession: function(message) {
				var cont = true;
				this.each(
					function(ex, i){
						if (!ex.beforeSendMessageToSession(message)){
							cont = false;
						}
					});
				return cont;
			},

			beforeActivityJoin: function() {
				var cont = true;
				this.each(
					function(ex, i){
						if (!ex.beforeActivityJoin()){
							cont = false;
						}
					});
				return cont;
			},

			afterActivityJoin: function() {
				this.each(
					function(ex, i){
						ex.afterActivityJoin();
					});
			},

			beforeActivityCreate: function() {
				var cont = true;
				this.each(
					function(ex, i){
						if (!ex.beforeActivityCreate()){
							cont = false;
						}
					});
				return cont;
			},

			afterActivityCreate: function() {
				this.each(
					function(ex, i){
						ex.afterActivityCreate();
					});
			},

			updateInvitationParameters: function(params) {
				this.each(
					function(ex, i){
						ex.updateInvitationParameters(params);
					});
			}


		});


};





/*  TODO - make this stuff work

 // must use a callback since javascript is asynchronous
 , activityDescriptionCallback: function(uri, cb) {
 var parsed = parseUri(uri);
 var switchboard = parsed.host;
 var sessionID = parsed.path.substring(1);

 var _room = sessionID;
 var _component = 'conference.'+switchboard;

 var _jid='junction';
 var _pw='junction';
 var connection = new Strophe.Connection('http://' + switchboard + '/http-bind');
 //connection.rawOutput = function(data) { $('#raw').append('<br/><br/>OUT: '+data.replace(/</g,'&lt;').replace(/>/g,'&gt;')); }
 //connection.rawInput = function(data) { $('#raw').append('<br/><br/>IN: '+data.replace(/</g,'&lt;').replace(/>/g,'&gt;')); }
 var getInfo = function(a) {
 var fields = a.getElementsByTagName('field');
 for (i=0;i<fields.length;i++) {
 if (fields[i].getAttribute('var') == 'muc#roominfo_description') {
 var desc = fields[i].childNodes[0].childNodes[0].wholeText; // get text of value
 var json = JSON.parse(desc);
 cb(json);
 connection.disconnect();
 return false;
 }
 }

 return true;
 };

 connection.connect(_jid,_pw, function(status){
 if (status == Strophe.Status.CONNECTED) {
 // get room info for sessionID
 connection.send(
 $iq({to: _room + "@" + _component, type: 'get'})
 .c("query", {xmlns: "http://jabber.org/protocol/disco#info"}).tree());


 connection.addHandler(getInfo,
 'http://jabber.org/protocol/disco#info',
 null,
 null,null,null);



 }
 });
 }


 , inviteServiceForRole: function(uri, ad, role) {
 if (role == '' || !ad.roles || !ad.roles[role]) return;
 var rolespec = ad.roles[role];
 if (!rolespec) return false;

 actor = {
 mRequest: plat,
 onActivityJoin:
 function() {
 var invite = {action:"cast",activity: uri};
 invite.spec = rolespec;
 invite.role = role;

 this.sendMessageToSession(invite);
 var scopedActor=this;
 var f = function() {
 scopedActor.leave();
 }
 setTimeout(f,500);
 }
 };
 var remoteURI = 'junction://';
 if (plat.switchboard) remoteURI += plat.switchboard;
 else remoteURI += parseUri(uri).host;
 remoteURI += '/jxservice';
 JX.getInstance().newJunction(remoteURI, actor);
 }
 // TODO: Just pass directorURI and activityURI
 // send: {action:cast,activity:uri}
 // have the rest looked up by the other director
 , castActor: function(directorURI, activityURI) {
 this.activityDescriptionCallback(activityURI,
 function(ad){
 if (role == '' || !ad.roles || !ad.roles[role]) return false;
 var rolespec = ad.roles[role];
 actor = {
 onActivityJoin:
 function() {
 var invite = {action:"cast",activity:activityURI};
 this.sendMessageToSession(invite);
 var scopedActor=this;
 var f = function() {
 scopedActor.leave();
 }
 setTimeout(f,500);
 }
 };

 JX.getInstance().newJunction(directorURI,actor);
 });
 }
 };
 }
 }
 }();


 */


// TODO: Use JQuery to load this script from another file

/* randomUUID.js - Version 1.0
 *
 * Copyright 2008, Robert Kieffer
 *
 * This software is made available under the terms of the Open Software License
 * v3.0 (available here: http://www.opensource.org/licenses/osl-3.0.php )
 *
 * The latest version of this file can be found at:
 * http://www.broofa.com/Tools/randomUUID.js
 *
 * For more information, or to comment on this, please go to:
 * http://www.broofa.com/blog/?p=151
 */

/**
 * Create and return a "version 4" RFC-4122 UUID string.
 */

function randomUUID() {
	var s = [], itoh = '0123456789ABCDEF';
	// Make array of random hex digits. The UUID only has 32 digits in it, but we
	// allocate an extra items to make room for the '-'s we'll be inserting.
	for (var i = 0; i <36; i++) s[i] = Math.floor(Math.random()*0x10);

	// Conform to RFC-4122, section 4.4
	s[14] = 4;  // Set 4 high bits of time_high field to version
	s[19] = (s[19] & 0x3) | 0x8;  // Specify 2 high bits of clock sequence

	// Convert to hex chars
	for (var i = 0; i <36; i++) s[i] = itoh[s[i]];

	// Insert '-'s
	s[8] = s[13] = s[18] = s[23] = '-';

	return s.join('');
}



// parseUri 1.2.2
// (c) Steven Levithan <stevenlevithan.com>
// MIT License

function parseUri (str) {
	var	o   = parseUri.options,
	m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
	uri = {},
	i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
							   if ($1) uri[o.q.name][$1] = $2;
						   });

	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};



function arrayEquals(a1, a2){
	if(a1.length != a2.length)	return false;
	for(var i = 0; i < a1.length; i++){
		var i1 = a1[i];
		var i2 = a2[i];
		if(!i1.equals || !i2.equals || !i1.equals(i2)) return false;
	}
	return true;
}


function clearArray(a){
	a.splice(0, a.length);
}

function deepObjCopy (dupeObj) {
	var retObj = new Object();
	if (typeof(dupeObj) == 'object') {
		if (typeof(dupeObj.length) != 'undefined'){
			retObj = new Array();
		}
		for (var objInd in dupeObj) {	
			if (typeof(dupeObj[objInd]) == 'object') {
				retObj[objInd] = deepObjCopy(dupeObj[objInd]);
			} else if (typeof(dupeObj[objInd]) == 'string') {
				retObj[objInd] = dupeObj[objInd];
			} else if (typeof(dupeObj[objInd]) == 'number') {
				retObj[objInd] = dupeObj[objInd];
			} else if (typeof(dupeObj[objInd]) == 'boolean') {
				((dupeObj[objInd] == true) ? retObj[objInd] = true : retObj[objInd] = false);
			}
		}
	}
	return retObj;
}


// Implement classical inheritance
//   by John Resig
//   http://ejohn.org/blog/simple-javascript-inheritance/
// 
// Inspired by base2 and Prototype
(function(){
	 var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

	 // The base Class implementation (does nothing)
	 this.Class = function(){};
	 
	 // Create a new Class that inherits from this class
	 Class.extend = function(prop) {
		 var _super = this.prototype;
		 
		 // Instantiate a base class (but only create the instance,
		 // don't run the init constructor)
		 initializing = true;
		 var prototype = new this();
		 initializing = false;
		 
		 // Copy the properties over onto the new prototype
		 for (var name in prop) {
			 // Check if we're overwriting an existing function
			 prototype[name] = typeof prop[name] == "function" &&
				 typeof _super[name] == "function" && fnTest.test(prop[name]) ?
				 (function(name, fn){
					  return function() {
						  var tmp = this._super;
						  
						  // Add a new ._super() method that is the same method
						  // but on the super-class
						  this._super = _super[name];
						  
						  // The method only need to be bound temporarily, so we
						  // remove it when we're done executing
						  var ret = fn.apply(this, arguments);       
						  this._super = tmp;
						  
						  return ret;
					  };
				  })(name, prop[name]) :
			 prop[name];
		 }
		 
		 // The dummy class constructor
		 function Class() {
			 // All construction is actually done in the init method
			 if ( !initializing && this.init )
				 this.init.apply(this, arguments);
		 }
		 
		 // Populate our constructed prototype object
		 Class.prototype = prototype;
		 
		 // Enforce the constructor to be what we expect
		 Class.constructor = Class;

		 // And make this class extendable
		 Class.extend = arguments.callee;
		 
		 return Class;
	 };
 })();


var JX = new _JX();
JX.getInstance = function(config) { return new _JX(config); };

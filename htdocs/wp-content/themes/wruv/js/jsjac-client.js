CHAT_TO_USER = 'WRUVDJ';

function clear_chat() {
	var cd = document.getElementById('chat_dialog');
	cd.innerHTML = '';
	jQuery(cd).animate({ scrollTop: 0 }, 500);
}
function append_to_chat(who, msg) {
	var html = '';
	if( who == 'system' ) {
		html += '<div class="msg chat-info">' + msg + '</div>';
	}
	else if( who == 'ME' ) {
		html += '<div class="msg chat-said"><b>' + who + ':</b> ' + msg + '</div>';
	}
	else {
		html += '<div class="msg chat-to-me"><b>' + who.replace(CHAT_TO_USER, 'DJ') + ':</b> ' + msg + '</div>';
	}

	var cd = document.getElementById('chat_dialog');
	cd.innerHTML += html;
	jQuery(cd).animate({ scrollTop: jQuery(cd).prop('scrollHeight') }, 500);
	// document.getElementById('chat_dialog').lastChild.scrollIntoView();

}

function handleIQ(oIQ) {
	return;
	document.getElementById('chat_dialog').innerHTML += "<div class='msg'>IN (raw): " + oIQ.xml().htmlEnc() + '</div>';
	// document.getElementById('chat_dialog').lastChild.scrollIntoView();
	con.send(oIQ.errorReply(ERR_FEATURE_NOT_IMPLEMENTED));
}
function handleMessage(oJSJaCPacket) {
	append_to_chat(CHAT_TO_USER,  oJSJaCPacket.getBody().htmlEnc());
	/*
	var html = '';
	// html += '<div class="msg"><b>' + oJSJaCPacket.getFromJID() + ':</b><br/>';
	html += '<div class="msg from-dj"><b>WRUVDJ:</b> ';
	html += oJSJaCPacket.getBody().htmlEnc() + '</div>';



	document.getElementById('chat_dialog').innerHTML += html;
	document.getElementById('chat_dialog').lastChild.scrollIntoView();
	*/
}
function handlePresence(oJSJaCPacket) {
	return;
	var html = '<div class="msg">';
	if (!oJSJaCPacket.getType() && !oJSJaCPacket.getShow())
		html += '<b>' + oJSJaCPacket.getFromJID() + ' has become available.</b>';
	else {
		html += '<b>' + oJSJaCPacket.getFromJID() + ' has set his presence to ';
		if (oJSJaCPacket.getType())
			html += oJSJaCPacket.getType() + '.</b>';
		else
			html += oJSJaCPacket.getShow() + '.</b>';
		if (oJSJaCPacket.getStatus())
			html += ' (' + oJSJaCPacket.getStatus().htmlEnc() + ')';
	}
	html += '</div>';
	document.getElementById('chat_dialog').innerHTML += html;
	// document.getElementById('chat_dialog').lastChild.scrollIntoView();
}
function handleError(e) {
	document.getElementById('err').innerHTML = "An error occured:<br />" + ("Code: " + e.getAttribute('code') + "\nType: " + e.getAttribute('type') + "\nCondition: " + e.firstChild.nodeName).htmlEnc();
	// document.getElementById('login_pane').style.display = '';
	document.getElementById('sendmsg_pane').style.display = 'none';
	if (con.connected())
		con.disconnect();
}
function handleStatusChanged(status) {
	oDbg.log("status changed: " + status);
}
function handleConnected() {
	// document.getElementById('login_pane').style.display = 'none';
	document.getElementById('sendmsg_pane').style.display = '';
	jQuery('#tapeman-chat').addClass('chatting');
	jQuery('.chat-loading').hide();
	jQuery('#chat-button > i.fa').prop('class', '').addClass('fa fa-ban');
	append_to_chat('system', 'chatting with ' + CHAT_TO_USER);
	// document.getElementById('err').innerHTML = '';
	con.send(new JSJaCPresence());
}
function handleDisconnected() {
	// document.getElementById('login_pane').style.display = '';
	document.getElementById('sendmsg_pane').style.display = 'none';
}
function handleIqVersion(iq) {
	con.send(iq.reply([iq.buildNode('name', 'jsjac simpleclient'), iq.buildNode('version', JSJaC.Version), iq.buildNode('os', navigator.userAgent)]));
	return true;
}
function handleIqTime(iq) {
	var now = new Date();
	con.send(iq.reply([iq.buildNode('display', now.toLocaleString()), iq.buildNode('utc', now.jabberDate()), iq.buildNode('tz', now.toLocaleString().substring(now.toLocaleString().lastIndexOf(' ') + 1))]));
	return true;
}

var sent_chat_msg;
function toggleChatLogin() {
	if (con.connected()) {
		quitChat();
		return;
	}

	sent_chat_msg = false;
	document.getElementById('sendmsg_pane').style.display = '';
	jQuery('#tapeman-chat').addClass('chatting');
	jQuery('.chat-loading').show();
	jQuery('#chat-button > i.fa').prop('class', '').addClass('fa fa-spin fa-circle-o-notch');
	try {
		var http_base = 'http://' + window.location.host + '/http-bind/';
		if (http_base.substr(0, 5) === 'ws://' || http_base.substr(0, 6) === 'wss://') {
			con = new JSJaCWebSocketConnection({
				httpbase : http_base,
				oDbg : oDbg
			});
		} else {
			con = new JSJaCHttpBindingConnection({
				httpbase : http_base,
				oDbg : oDbg
			});
		}
		setupCon(con);

		var actual_username = readCookie('wruv-chat-user'); //have fun, kids
		if( !actual_username ) {
		 	actual_username = 'www' + (Math.random()+'').substring(3,8);
			writeCookie('wruv-chat-user', actual_username);
		}
		// setup args for connect method
		oArgs = new Object();
		// oArgs.domain = oForm.server.value;
		// oArgs.username = oForm.username.value;
		oArgs.domain = 'chat.barbershop.wruv.org';
		oArgs.username = actual_username;
		oArgs.resource = '<3';
		// oArgs.pass = oForm.password.value;
		// oArgs.register = oForm.register.checked;
		con.connect(oArgs);
	} catch (e) {
		// document.getElementById('err').innerHTML = e.toString();
		console.log(e.toString());
	} finally {
		return false;
	}
}
function setupCon(oCon) {
	oCon.registerHandler('message', handleMessage);
	oCon.registerHandler('presence', handlePresence);
	oCon.registerHandler('iq', handleIQ);
	oCon.registerHandler('onconnect', handleConnected);
	oCon.registerHandler('onerror', handleError);
	oCon.registerHandler('status_changed', handleStatusChanged);
	oCon.registerHandler('ondisconnect', handleDisconnected);
	oCon.registerIQGet('query', NS_VERSION, handleIqVersion);
	oCon.registerIQGet('query', NS_TIME, handleIqTime);
}

function sendChatMsg(msg, silent) {
	/*if (oForm.msg.value == '' || oForm.sendTo.value == '')
		return false;
	if (oForm.sendTo.value.indexOf('@') == -1)
		oForm.sendTo.value += '@' + con.domain;
	*/
	try {
		var oMsg = new JSJaCMessage();
		oMsg.setTo(new JSJaCJID(CHAT_TO_USER + '@' + con.domain));
		oMsg.setBody(msg);
		con.send(oMsg);
		sent_chat_msg += 1;
		if( !silent ) append_to_chat('ME', msg);
		// oForm.msg.value = '';
		return false;
	} catch (e) {
		html = "<div class='msg error''>Error: " + e.message + "</div>";
		document.getElementById('chat_dialog').innerHTML += html;
		// document.getElementById('chat_dialog').lastChild.scrollIntoView();
		return false;
	}
}
function quitChat() {
	if( sent_chat_msg > 0 ) {
		append_to_chat('system', 'quit chat');
		sendChatMsg('/me -- closed chat --', true);
	}
	else {
		clear_chat();
	}
	var p = new JSJaCPresence();
	p.setType("unavailable");
	con.send(p);
	con.disconnect();
	// document.getElementById('login_pane').style.display = '';
	jQuery('#tapeman-chat').removeClass('chatting');
	document.getElementById('sendmsg_pane').style.display = 'none';
	jQuery('#chat-button > i.fa').prop('class', '').addClass('fa fa-comment');
}
function init() {
	oDbg = new JSJaCConsoleLogger(0);
	try {// try to resume a session
		con = new JSJaCHttpBindingConnection({
			'oDbg' : oDbg
		});
		setupCon(con);
		if (con.resume()) {
			// document.getElementById('login_pane').style.display = 'none';
			// document.getElementById('err').innerHTML = '';
			handleConnected();
		}
	} catch (e) {
	} // reading cookie failed - never mind
}
onload = init;
//onerror = function(e) {
//  document.getElementById('err').innerHTML = e;
//
//  document.getElementById('login_pane').style.display = '';
//  document.getElementById('sendmsg_pane').style.display = 'none';
//
//  if (con && con.connected())
//    con.disconnect();
//  return false;
//};
onunload = function() {
	if ( typeof con != 'undefined' && con && con.connected()) {
		// save backend type
		if (con._hold)// must be binding
			(new JSJaCCookie('btype', 'binding')).write();
		else
			(new JSJaCCookie('btype', 'polling')).write();
		if (con.suspend) {
			con.suspend();
		}
	}
};

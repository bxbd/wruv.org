var isIE = /*@cc_on!@*/false;


function handleIQ(oIQ) {
	document.getElementById('iResp').innerHTML += "<div class='msg'>IN (raw): " + oIQ.xml().htmlEnc() + '</div>';
	document.getElementById('iResp').lastChild.scrollIntoView();
	con.send(oIQ.errorReply(ERR_FEATURE_NOT_IMPLEMENTED));
}
function handleMessage(oJSJaCPacket) {
	var html = '';
	html += '<div class="msg"><b>Received Message from ' + oJSJaCPacket.getFromJID() + ':</b><br/>';
	html += oJSJaCPacket.getBody().htmlEnc() + '</div>';
	document.getElementById('iResp').innerHTML += html;
	document.getElementById('iResp').lastChild.scrollIntoView();
}
function handlePresence(oJSJaCPacket) {
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
	document.getElementById('iResp').innerHTML += html;
	document.getElementById('iResp').lastChild.scrollIntoView();
}
function handleError(e) {
	document.getElementById('err').innerHTML = "An error occured:<br />" + ("Code: " + e.getAttribute('code') + "\nType: " + e.getAttribute('type') + "\nCondition: " + e.firstChild.nodeName).htmlEnc();
	document.getElementById('login_pane').style.display = '';
	document.getElementById('sendmsg_pane').style.display = 'none';
	if (con.connected())
		con.disconnect();
}
function handleStatusChanged(status) {
	oDbg.log("status changed: " + status);
}
function handleConnected() {
	document.getElementById('login_pane').style.display = 'none';
	document.getElementById('sendmsg_pane').style.display = '';
	document.getElementById('err').innerHTML = '';
	con.send(new JSJaCPresence());
}
function handleDisconnected() {
	document.getElementById('login_pane').style.display = '';
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

function chat_box_keyevent(e, fcn) {
   if( isIE ) {
	  if( e.keyCode == 13 ) fcn(e.srcElement.form);
   }
   else {
	  if( e.keyCode == 13 ) fcn(e.target.form);
   }
}

function doChatLogin(oForm) {
	var actual_username = 'www' + (Math.random()+'').substring(3,8);

	// document.getElementById('err').innerHTML = '';
	// reset
	try {
		if (oForm.http_base.value.substr(0, 5) === 'ws://' || oForm.http_base.value.substr(0, 6) === 'wss://') {
			con = new JSJaCWebSocketConnection({
				httpbase : oForm.http_base.value,
				oDbg : oDbg
			});
		} else {
			con = new JSJaCHttpBindingConnection({
				httpbase : oForm.http_base.value,
				oDbg : oDbg
			});
		}
		setupCon(con);
		// setup args for connect method
		oArgs = new Object();
		oArgs.domain = oForm.server.value;
		oArgs.username = actual_username;
		// oArgs.username = oForm.username.value;
		oArgs.resource = 'jsjac_simpleclient';
		oArgs.pass = oForm.password.value;
		oArgs.register = oForm.register.checked;
		con.connect(oArgs);
	} catch (e) {
		document.getElementById('err').innerHTML = e.toString();
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
function sendMsg(oForm) {
	if (oForm.msg.value == '') // || oForm.sendTo.value == '')
		return false;
	// if (oForm.sendTo.value.indexOf('@') == -1)
	// 	oForm.sendTo.value += '@' + con.domain;
	try {
		var oMsg = new JSJaCMessage();
		var sendto = 'WRUVDJ_test@' + con.domain;
		oMsg.setTo(new JSJaCJID(sendto));
		oMsg.setBody(oForm.msg.value);
		con.send(oMsg);
		oForm.msg.value = '';
		return false;
	} catch (e) {
		html = "<div class='msg error''>Error: " + e.message + "</div>";
		document.getElementById('iResp').innerHTML += html;
		document.getElementById('iResp').lastChild.scrollIntoView();
		return false;
	}
}
function quit() {
	var p = new JSJaCPresence();
	p.setType("unavailable");
	con.send(p);
	con.disconnect();
	document.getElementById('login_pane').style.display = '';
	document.getElementById('sendmsg_pane').style.display = 'none';
}
function init() {
	oDbg = new JSJaCConsoleLogger(4);
	try {// try to resume a session
		con = new JSJaCHttpBindingConnection({
			'oDbg' : oDbg
		});
		setupCon(con);
		if (con.resume()) {
			document.getElementById('login_pane').style.display = 'none';
			document.getElementById('sendmsg_pane').style.display = '';
			document.getElementById('err').innerHTML = '';
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

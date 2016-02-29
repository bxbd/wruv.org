var isIE = /*@cc_on!@*/false;

//~ function add_to_dialog(from, msg) {
   //~ var html += '<div class="msg"><b>' + from + ':</b>';
   //~ document.getElementById('chat_dialog').innerHTML += html;
//~ }

function add_to_chat_output(html) {
   document.getElementById('chat_dialog').innerHTML += html;
   document.getElementById('chat_dialog').lastChild.scrollIntoView(false);
}

function handleMessage(aJSJaCPacket) {
   var html = '';
   if( !aJSJaCPacket.getFrom().match(/^wruvdj@/i) ) {
      return;
   }
   html += '<div class="msg msg-from"><b>DJ:</b> ';
   if( aJSJaCPacket.isError() ) {
      html += "I'm away right now, dial (802) 656-4399" + '</div>';
   }
   else {
      html += aJSJaCPacket.getBody().htmlEnc() + '</div>';
   }
   add_to_chat_output(html);
}

function handlePresence(aJSJaCPacket) {
   //~ var html = '<div class="msg">';
   //~ if (!aJSJaCPacket.getType() && !aJSJaCPacket.getShow())
      //~ html += '<b>'+aJSJaCPacket.getFrom()+' has become available.</b>';
   //~ else {
      //~ html += '<b>'+aJSJaCPacket.getFrom()+' has set his presence to ';
      //~ if (aJSJaCPacket.getType())
         //~ html += aJSJaCPacket.getType() + '.</b>';
      //~ else
         //~ html += aJSJaCPacket.getShow() + '.</b>';
      //~ if (aJSJaCPacket.getStatus())
         //~ html += ' ('+aJSJaCPacket.getStatus().htmlEnc()+')';
   //~ }
   //~ html += '</div>';

   //~ add_to_chat_output(html);
}

function handleError(e) {
   chat_error( "An error occured:\n"+
      ("Code: "+e.getAttribute('code')+"\nType: "+e.getAttribute('type')+
      "\nCondition: "+e.firstChild.nodeName)
   );;
   activate_login_pane();

   if (con.connected())
      con.disconnect();
}

function handleStatusChanged(status) {
   oDbg.log("status changed: "+status);
}

function handleConnected(nomsg) {
   //~ activate_chat_pane("<span class='chat_connected'>DJ CHAT, <span class='chat_as'>as " + con.username + "</span></span>");
   activate_chat_pane("<span class='chat_connected'>DJ CHAT, connected</span>", true);
   //~ activate_chat_pane("<span class='chat_connected'>" + con.username + " connected</span>");
   if( !nomsg ) {
      //~ dispatch_msg( '** Listener "' + con.username + '" has opened a chat from the website' );
      document.getElementById('chat_sendmsg').focus();
   }
   con.send(new JSJaCPresence());
}

function handleDisconnected() {
   activate_login_pane();
}

function chat_box_keyevent(e, fcn) {
   if( isIE ) {
      if( e.keyCode == 13 ) fcn(e.srcElement.form);
   }
   else {
      if( e.keyCode == 13 ) fcn(e.target.form);
   }
}

function doChatLogin(aForm) {
   chat_error( '' ); // reset
   //~ var first_msg = aForm.username.value;

   var actual_username = 'www' + (Math.random()+'').substring(3,8);

   //~ if( aForm.username.value == '' ) {
      //~ aForm.username.value = "Enter Name";
      //~ return;
   //~ }
   //~ else if( aForm.username.value == 'Enter Name' ) {
      //~ return;
   //~ }

   //~ var their_username = aForm.username.value;
   //~ var actual_username = their_username.replace(/[^a-z0-9]/ig, '_').substr(0,20);

   try {
      // setup args for contructor
      /*oArgs = new Object();
      var loc = window.location;
      oArgs.httpbase = loc.protocol + '//' + loc.host + '/http-bind/';
      // oArgs.httpbase = 'https://wruv.org/http-bind/';
      oArgs.timerval = 2000;

      if (typeof(oDbg) != 'undefined')
         oArgs.oDbg = oDbg;

      //~ if (aForm.backend[0].checked)
         con = new JSJaCHttpBindingConnection(oArgs);
      //~ else
         //~ con = new JSJaCHttpPollingConnection(oArgs);

      setupCon(con);*/


      // setup args for connect method
      oArgs = new Object();
      oArgs.domain = "chat.wruv.org";
      var loc = window.location;
      oArgs.timerval = 2000;
      oArgs.httpbase = loc.protocol + '//chat.wruv.org/http-bind/';
      oArgs.username = actual_username;
      //~ oArgs.given_username = their_username;
      oArgs.resource = '<3';
      oArgs.pass = '';
      oArgs.register = false;
      con = new JSJaCHttpBindingConnection(oArgs);
      setupCon(con);
      con.connect(oArgs);

      activate_chat_pane('Connecting...', false);
      document.getElementById('chat_sendmsg').focus();
   } catch (e) {
      chat_error( e.toString() );
   } finally {
      return false;
   }

   return false;
}

function setupCon(con) {
      con.registerHandler('message',handleMessage);
      con.registerHandler('presence',handlePresence);
      //~ con.registerHandler('iq',handleIQ);
      con.registerHandler('onconnect',handleConnected);
      con.registerHandler('onerror',handleError);
      con.registerHandler('status_changed',handleStatusChanged);
      con.registerHandler('ondisconnect',handleDisconnected);

      //~ con.registerIQGet('query', NS_VERSION, handleIqVersion);
      //~ con.registerIQGet('query', NS_TIME, handleIqTime);
}

function dispatch_msg(msg) {
   var sendto = 'WRUVDJ@' + con.domain;

   var aMsg = new JSJaCMessage();
   aMsg.setTo(new JSJaCJID(sendto));
   aMsg.setBody(msg);
   con.send(aMsg);
}

function sendMsg(form, no_output) {
   var msg = form.msg.value;
   if (msg == '')
      return false;

   form.msg.value = '';
   try {
      dispatch_msg(msg);

      var html = '<div class="msg msg-to"><b><i>ME:</b></i> ' + msg + '</div>';
      //~ var html = '<div class="msg"><b><i>' + con.username + '</b></i>: ' + msg + '</div>';
      add_to_chat_output(html);


      return false;
   } catch (e) {
      html = "<div class='msg error''>Error: "+e.message+"</div>";
      add_to_chat_output(html);
      return false;
   }
}

function doChatLogout() {
   activate_login_pane();

   window.setTimeout(function() {
      //~ dispatch_msg( '** Listener "' + con.username + '" has closed the chat box' );

      var p = new JSJaCPresence();
      p.setType("unavailable");
      con.send(p);
      con.disconnect();

   }, 50);
}

function init() {
   if (typeof(Debugger) == 'function') {
      oDbg = new Debugger(2,'simpleclient');
      oDbg.start();
   } else {
      // if you're using firebug or safari, use this for debugging
      //oDbg = new JSJaCConsoleLogger(2);
      // comment in above and remove comments below if you don't need debugging
      oDbg = function() {};
      oDbg.log = function() {};
   }


   try { // try to resume a session
      //~ if (JSJaCCookie.read('btype').getValue() == 'binding')
         con = new JSJaCHttpBindingConnection({'oDbg':oDbg});
      //~ else
         //~ con = new JSJaCHttpPollingConnection({'oDbg':oDbg});

      setupCon(con);

      if (con.resume()) {
         chat_error( '' );
         handleConnected(1);
      }
      else {
         activate_login_pane();
      }
   } catch (e) {
      activate_login_pane();
   } // reading cookie failed - never mind

}

function activate_chat_pane(msg, allow_send) {
   document.getElementById('chat_login_pane').style.display = 'none';

   var cpane = document.getElementById('chat_sendmsg_pane');
   cpane.style.display = '';
   document.getElementById('chat_sendmsg').disabled = !allow_send;
   document.getElementById('chat_dialog').innerHTML = msg ? msg + '<br/>' : '&nbsp;';
   make_dialog_resizeable();

}

function make_dialog_resizeable() {
   if( typeof($) != 'undefined' ) { // jquery is on
      $('#chat_sendmsg_pane.big-chat').resizable({
         //grid : [40, 0],
         minWidth : 520,
         maxWidth : 520,
         minHeight : 90,
         alsoResize: '#chat_dialog.big-chat'
         //~ maxWidth: 550
      });
   }

}

function activate_login_pane() {
   document.getElementById('chat_login_pane').style.display = '';
   document.getElementById('chat_sendmsg_pane').style.display = 'none';
}

onload = init;

onerror = function(e) {
   chat_error( e );

   alert(e);

   document.getElementById('chat_login_pane').style.display = '';
   document.getElementById('chat_sendmsg_pane').style.display = 'none';

   if (con && con.connected())
      con.disconnect();
   return false;
};

function jsjac_onunload() {
   // save the cookie with only the name, and just reconnect, don't do this suspending.
   // what to do about having the site open in
   if (typeof con != 'undefined' && con && con.connected()) {
   // save backend type
      //~ if (con._hold) // must be binding
         (new JSJaCCookie('btype','binding')).write();
      //~ else
         //~ (new JSJaCCookie('btype','polling')).write();
      if (con.suspend) {
         con.suspend();
      }
   }
};
onbeforeunload = jsjac_onunload;

function chat_error(msg) {
   if( msg == '' ) return;
   if( typeof(console) != 'undefined' ) console.log("CHAT ERROR: " + msg);
}

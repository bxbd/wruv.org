<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<!-- ### BEGIN HEAD ####  -->
<head>

<!-- Meta -->
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Title -->
<title><?php
	$prefix = false;

		 if (function_exists('is_tag') && is_tag()) {
			single_tag_title('Tag Archive for &quot;');
			echo '&quot; - ';

			$prefix = true;
		 } elseif (is_archive()) {

			wp_title(''); echo ' '.__('Archive').' - ';
			$prefix = true;

		 } elseif (is_search()) {

			echo __('Search for', 'wizedesign').' &quot;'.wp_specialchars($s).'&quot; - ';
			$prefix = true;

		 } elseif (!(is_404()) && (is_single()) || (is_page())) {
				wp_title('');
				echo '  ';
		 } elseif (is_404()) {
			echo __('Not Found', 'wizedesign').' - ';
		 }

		 if (is_home()) {
			bloginfo('name'); echo ' - '; bloginfo('description');
		 } else {
		  bloginfo('name');
		 }

		 if ($paged > 1) {
			echo ' - page '. $paged;
		 }
	?></title>

<!-- Favicon -->
<?php
		if (of_get_option('favicon_upload','true') == 'true'){
		    }else{
                          if (of_get_option('favicon_upload',null) != null){
                             $favicon_url = of_get_option('favicon_upload');
                             }else{
                             $favicon_url = get_template_directory_uri().'/images/favicon.ico';
                          }

                 echo '<link rel="shortcut icon" href="'.$favicon_url.'" />';
                 }
?>


<!-- Wordpress functions -->
<?php wp_head(); ?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=Covered+By+Your+Grace' rel='stylesheet' type='text/css'>
</head>


<!-- Begin Body -->
<body  <?php body_class(); ?>>

<?php
$playerar = of_get_option('player_audio_radio');
if (of_get_option('active_player', '1') == '1') {
switch ($playerar) {
		 case "player_audio":
		 echo ' '. get_template_part( 'player' ) . '';
		 break;

		 case "player_radio":
		 echo ' '. get_template_part( 'radio' ) . '';
		 break;
		 }
}
?>

<!-- header -->
   <div class="header-row clearfix">
     <div id="header">
       <div class="header-col header-col-1">
         <div id="logo">
   <?php
      if (of_get_option('logo_upload','true') == 'true'){
          }else{
                             if (of_get_option('logo_upload',null) != null){
                                $logo_url = of_get_option('logo_upload');
                                }else{
                                $logo_url = get_template_directory_uri().'/images/logo.png';
                             }

                    echo '
            <a href="'.get_bloginfo('url').'"><img src="'.$logo_url.'" alt="logo" /></a>';
                    }
   ?>
         </div><!-- end #logo -->
         <div id="multiplayer">
           <h3 class="player-label">Desktop Player</h3>
           <div class="player-choice">
             <div class="dot orange"><br><a class="player-btn" target="_blank" href="http://wruv.org/wruv48.m3u"><strong>48</strong>KBPS</a></div>
           </div>
           <div class="player-choice">
             <div class="dot green"><br><a class="player-btn" target="_blank" href="http://wruv.org/wruv128.m3u"><strong>128</strong>KBPS</a></div>
           </div>
           <div class="player-choice">
             <div class="dot red"><br><a class="player-btn" target="_blank" href="http://wruv.org/wruv256.m3u"><strong>256</strong>KBPS</a></div>
           </div>
         </div>
       </div>
       <div class="header-col col-2">
         <div class="tapeman-container">
           <div id="tapeman-chat" class="big-chat">
             <div id="chat_login_pane">
                <form name="loginForm" onsubmit="return false;" action="#">
                   <input type="image" class="chat_button" src="/img/chat/login.gif" title="Connect!" onclick="doChatLogin(this.form)">
                </form>
             </div>

             <div id="chat_sendmsg_pane" class="big-chat" style="display:none;">
                <form name="sendForm" onsubmit="return false" action="#">
                <div id="chat_dialog" class="big-chat"></div>
                <table>
                   <tbody><tr>
                      <td width="16">
                         <input type="image" class="chat_button" src="/img/chat/send.gif" title="Send" onclick="sendMsg(this.form)">
                      </td>
                      <td width="16">
                         <a href="#" onclick="return doChatLogout();"><img border="0" style="margin-top: 1px;" class="chat_button" src="/img/chat/logout.gif" title="Logout"></a>

                      </td>

                      <td nowrap="1">
                         <small>Enter message:</small>
                      </td>
                      <td width="100%">
                         <input type="text" name="msg" id="chat_sendmsg" class="big-chat" onkeyup="chat_box_keyevent(event, sendMsg);">
                      </td>
                   </tr>
                </tbody></table>
                </form>
             </div>
             <div id="concealed-player">
               <audio id="main-player" width="200" height="25" src="http://icecast.uvm.edu:8005/wruv_fm_128" type="audio/mpeg"></audio>
             </div>
        </div>
           <div class="tapedeck-controls">
             <span class="tape-label" id="chat-label">
               Chat With The DJ</span>
                     <span class="tape-label">
               Full Size</span>
                     <span class="tape-label">
               Play</span>
                             <span class="tape-label">
               Stop</span>
             <div class="inner">
               <a href="#" class="tape-button" id="chat-button" onclick="doChatLogin(document.forms.loginForm); return false;"><i class="fa fa-comment-o"></i></a>
               <a href="#" class="tape-button"><i class="fa fa-external-link"></i></a>
               <a href="#" id="play-button" class="tape-button"><i class="fa fa-play"></i></a>
               <a href="#" id="stop-button" class="tape-button"><i class="fa fa-stop"></i></a>
             </div>
           </div>
         </div>
       </div>
       <div class="header-col col-3">
         <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/252172/wruv-on-air.png" class="on-air-img">
             <div id="main-vu-meter">
           <div class="needle" id="needle"></div>
         </div>
         <div class="header-meta">
           My Dog is a Person Too<br>
           <sup class="with">with</sup> DJ Liz<br><hr class="drawn">
           <span class="genre">music for the being alive jive (mix of indie rock, folk, blues, electronic, and more)</span>
         </div>
         <div id="bar-vu-meter">
           <div class="inner">
           </div>
         </div>
       </div>
     </div>

   <div id="main">
      <div class="main-navigation">
<?php
wp_nav_menu(array(
    'menu' => 'Main Menu',
    'container_id' => 'wizemenu',
    'walker' => new CSS_Menu_Maker_Walker()
));
?>

      </div><!-- end .main-navigation -->
   </div>
<!-- Wrap -->
<div class="wrap clearfix">
<div id="wrcon">

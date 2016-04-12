<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<!-- ### BEGIN HEAD ####  -->
<head>

<!-- Meta -->
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Title -->
<title>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
<script src="<?php echo get_stylesheet_directory_uri() ?>/bower_components/jsxc/build/lib/jsxc.dep.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/bower_components/jsxc/build/jsxc.min.js"></script>
<script>
	
</script>

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
						if( of_get_option('logo_upload','true') == 'true' ) {
						}
						else {
							if( of_get_option('logo_upload',null) != null ) {
								$logo_url = of_get_option('logo_upload');
							}
							else {
								$logo_url = get_template_directory_uri().'/images/logo.png';
							}
							?>
							<a href="<?= get_bloginfo('url') ?>"><img src="<?= $logo_url ?>" alt="logo" /></a>
						<?php
						}
					?>
				</div><!-- end #logo -->
			</div>
			<div class="header-col col-2">
				<div class="tapeman-container">
					<div id="tapeman-chat" class="big-chat">
						<div id="concealed-player">
							<audio id="main-player" width="200" height="25" src="http://icecast.uvm.edu:8005/wruv_fm_128" type="audio/mpeg"></audio>
						</div>
						<div id="chat-area" style="display:none;">
							<div id="chat-loader">
								<i id="chat-loader-spinner" class="fa fa-spinner fa-spin fa-4x"></i><br>
								<span class="loading-message">Connecting...</span>
							</div>
							<div id="chat_login_pane" style="display: none;">
								<form name="loginForm" onsubmit="return false;" action="#">
								</form>
							</div>

							<div id="chat_sendmsg_pane" class="big-chat">
								<div id="chat_dialog" class="big-chat">
								</div>
								<form name="sendForm" onsubmit="return false" action="#">
									<input type="text" size="30" name="msg" id="chat_sendmsg" placeholder="Start typing..." value="" onkeyup="chat_box_keyevent(event, sendMsg);"></input>
								</form>
							</div>
						</div>
					</div>
					<div class="tapedeck-controls">
						<span class="tape-label" id="chat-label">
							DJ Chat
						</span>
						<span class="tape-label">
							Full Size
						</span>
						<span class="tape-label" id="play-label">
							Play
						</span>
						<span class="tape-label">
							Stream
						</span>
						<div class="inner">
							<a href="#" class="tape-button" id="chat-button" onclick="doChatLogin(document.forms.loginForm); return false;"><i class="fa fa-comment-o"></i></a>
							<a href="#" class="tape-button"><i class="fa fa-external-link"></i></a>
							<a href="#" id="play-pause-button" class="tape-button"><i class="fa fa-play"></i></a>
							<div id="multiplayer" class="tape-button">
								<div class="player-choice">
									<div class="dot red"><a class="player-btn" href="/wruv256.m3u"><strong>256</strong>KBPS</a></div>
								</div>
								<div class="player-choice">
									<div class="dot green"><a class="player-btn" href="/wruv128.m3u"><strong>128</strong>KBPS</a></div>
								</div>
								<div class="player-choice">
									<div class="dot orange"><a class="player-btn" href="/wruv48.m3u"><strong>&nbsp;48</strong>KBPS</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="header-col col-3">
				<img src="/wp-content/themes/wruv/images/wruv-on-air.png" class="on-air-img">
				<div id="main-vu-meter">
					<div class="needle" id="needle"></div>
				</div>
				<div class="header-meta">
					<div class="onair-showname">My Dog is a Person Too</div>
					<div class="onair-djname-withwith">
						<sup class="onair-with">with</sup>
						<div class="onair-djname">DJ Liz</div>
					</div>
					<hr class="drawn">
					<div class="onair-genre">music for the being alive jive (mix of indie rock, folk, blues, electronic, and more)</div>
				</div>
				<div class="mobile-stuff clearfix">
					<div id="mobile-menu-container">
						<div class="mobile-menu-open "><i class="fa fa-bars"></i></div>
					</div>
					<div id="mobile-phone-container">
						<div id="lightning-phone"><i class="fa fa-bolt"></i>ON AIR: <a href="tel:802-656-4399">802-656-4399</a></div>
					</div>
				</div>
			</div>
		</div>

		<div id="main" class="mobile-menu-toggle toggle">
			<div class="main-navigation">
				<?php
					wp_nav_menu(array(
						'menu' => 'Main Menu',
						'container_id' => 'wizemenu',
						'walker' => new CSS_Menu_Maker_Walker()
					));
				?>

				<?php
				if (of_get_option('social_header', '1') == '1') {
				?>
					<div class="header-social">
						<ul id="footer-social">
							<?php
								$social_icons = array(
									'facebook', 'twitter', 'digg', 'youtube',
									'vimeo', 'rss', 'flickr', 'lastfm', 'pinterest',
									'vk', 'google', 'amazon', 'beatport', 'instagram',
									'myspace', 'mixcloud', 'soundcloud', 'resident',
									'tumblr',
								);
								foreach( $social_icons as $si ) {
									if ( of_get_option( $si ) != "") { ?>
										<li class="<?= $si ?> footer-social">
											<a href="<?= of_get_option($si) ?>" target="_blank"
										></a></li><?php
									}
								}
							?>
						</ul>
					</div><!-- end .header-social -->
				<?php
				}
				?>

			</div><!-- end #main -->
		</div><!-- end #header -->

	</div><!-- end .main-navigation -->
</div>
<!-- Wrap -->
<div class="wrap clearfix">
<div id="wrcon">

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
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

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
		<div id="header" class="sparkle-target">
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
				<script>
					jQuery(document).ready(function($) {

						$('#sparkles-button').click( function() {
							var $st = $('.sparkle-target');
							$st.sparkle()
							    .off("mouseover.sparkle")
							    .off("mouseout.sparkle")
							    .off("focus.sparkle")
							    .off("blur.sparkle");

							if( $(this).data('sparkle') ) {
								$(this).css('color', 'white');
								$(this).data('sparkle', false);
								$st.trigger("stop.sparkle");
							}
							else {
								$(this).css('color', 'gold');
								$(this).data('sparkle', true);
								$st.trigger("start.sparkle");
							}
							return false;
						});
						$('#chat-label, #chat-button').click( function() {
							jQuery('#tapeman-chat').addClass('chatting');
							toggleChatLogin();
							return false;
						});

						$('#chat-input').keypress( function(e) {
							if( e.which == 13 ) {
								sendChatMsg($(this).val());
								$(this).val('');
								return false;
							}
						});

					}, $);
				</script>
				<div class="tapeman-container">
					<div id="tapeman-chat" class="big-chat">
						<div id="sendmsg_pane" style="display:none;">
							<div id="chat-area">
								<div id="chat_dialog"></div>
								<input name="msg" id='chat-input' rows="3" cols="80" tabindex="2" placeholder="type to chat...">
							</div>
						</div>
						<div class="chat-loading hidden"><i class="fa fa-spin fa-circle-o-notch"></i></div>
					</div>

					<div class="tapedeck-controls">
						<span class="tape-label" id="chat-label">
							Chat DJ
						</span>
						<span class="tape-label" id="sparkles-label">
							Sparkle
						</span>
						<span class="tape-label" id="play-label">
							Play
						</span>
						<span class="tape-label" id="stream-label">
							Stream
						</span>
						<div class="inner">
							<a href="#" class="tape-button" id="chat-button"><i class="fa fa-comment"></i></a>
							<a href="#" class="tape-button" id="sparkles-button"><i class="fa fa-star"></i></a>
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
				<div class="on-air-img"></div>
				<div id="main-vu-meter">
					<div class="needle" id="needle"></div>
				</div>
				<div class="header-meta">

					<?php $current_show = wruv_current_sched_slot(); ?>

                    <div class="onair-showtime"><?= $current_show['show_time_str'] ?></div>
                    <div class="onair-showname"><?= $current_show['show_name'] ?: $current_show['show_dj_name']  ?></div>
					<?php if( !empty($current_show['show_name']) && !empty($current_show['show_dj_name']) ) { ?>
                    <div class="onair-djname-withwith">
						<sup class="onair-with">with</sup>
                    	<div class="onair-djname"><?= $current_show['show_dj_name'] ?></div>
					</div>
					<?php } ?>
					<hr class="drawn">
                    <div class="onair-genre"><?= preg_replace('/\//', ' / ', $current_show['genre']) ?></div>


					<!--
					<div class="onair-showname"><?= $current_show['show_name'] ?></div>
					<div class="onair-djname-withwith">
						<sup class="onair-with">with</sup>
						<div class="onair-djname"><?= $current_show['show_dj_name'] ?></div>
					</div>
					<hr class="drawn">
					<div class="onair-genre">music for the being alive jive (mix of indie rock, folk, blues, electronic, and more)</div>
				-->
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

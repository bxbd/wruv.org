(function ($) {

  $(document)
	.ready(function () {

	selectnav('menu-top-menu', {
	nested: true
	});

	$("ul.fap-my-playlist li").click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	});

	$(".photo-preview img")
	  .fadeTo(1, 1);
	$(".photo-preview img")
	  .hover(

	function () {
	  $(this)
		.fadeTo("fast", 0.70);
	}, function () {
	  $(this)
		.fadeTo("slow", 1);
	});

	$(".flickr_badge_image img")
	  .fadeTo(1, 1);
	$(".flickr_badge_image img")
	  .hover(

	function () {
	  $(this)
		.fadeTo("fast", 0.70);
	}, function () {
	  $(this)
		.fadeTo("slow", 1);
	});

	// -------------------------------------------------------------------------------------------------------
	// Slider
	// -------------------------------------------------------------------------------------------------------

	if(jQuery('.flexslider').length && jQuery()) {
	   jQuery('.flexslider').flexslider({
		  animation: "fade",
		  controlNav: true,
		  animationLoop: true,
		  controlsContainer: "",
		  pauseOnAction: false,
		  pauseOnHover: true,
		  smoothHeight: true,
		  controlNav: true,
		  nextText: "&rsaquo;",
		  prevText: "&lsaquo;",
		  keyboardNav: true,
		  slideshowSpeed: 3000,
		  animationSpeed: 500,

		  start: function (slider) {
			 slider.removeClass('loading');
		  }

	   });

	}

	// -------------------------------------------------------------------------------------------------------
	// Tabs
	// -------------------------------------------------------------------------------------------------------

	$("#tabs ul")
	  .idTabs();

	// -------------------------------------------------------------------------------------------------------
	// Toggle
	// -------------------------------------------------------------------------------------------------------

	$("#tabs ul")
	  .idTabs();
	$(".toggle_container")
	  .hide();
	$(".trigger")
	  .click(function () {
	  jQuery(this)
		.toggleClass("active")
		.next()
		.slideToggle("fast");
	  return false; //Prevent the browser jump to the link anchor
	});


	// -------------------------------------------------------------------------------------------------------
	// Fixed DIV
	// -------------------------------------------------------------------------------------------------------

	jQuery(document)
	  .ready(function () {
	  jQuery('.widget:last')
		.addClass('last');
	  jQuery('.evwdg:first')
		.addClass('first');
	  jQuery('.evwdg:last')
		.addClass('last');
	  jQuery('.widgets-col-player ul.fap-my-playlist li:last')
		.addClass('last');
	  jQuery('.bl1page-col:last')
		.addClass('last');
	  jQuery('.bl2page-col:last')
		.addClass('last');
	  jQuery('.ev2page-col:last')
		.addClass('last');
	  jQuery('.ev3page:last')
		.addClass('last');
	  jQuery('.mxpage-col:last')
		.addClass('last');
	  jQuery('.home-shr:last')
		.addClass('last');
	});

});

function update_playerState(newstate) {
	window.playerState = newstate;

	writeCookie( 'audio_playing', window.playerState == 'playing' ? 1 : 0, 2/24 );
	if( window.playerState == 'playing' ) {
		$('body').addClass('playing');
		$('#play-pause-button .fa').removeClass(' fa-spin fa-circle-o-notch');
		$('#play-pause-button .fa').removeClass('fa-play');
		$('#play-pause-button .fa').addClass('fa-pause');

		window.VUTimer = setInterval( iterateMainVU, 200 );
	}
	else if( window.playerState == 'paused' ) {
		$('body').removeClass('playing');
		$('#needle').css('transform', 'rotateZ(-45deg)');

		clearTimeout(window.VUTimer);
		$('#play-pause-button .fa').removeClass('fa-pause');
		$('#play-pause-button .fa').addClass('fa-play');
	}
	else { //window.playerState == 'loading'
		$('#play-pause-button .fa').removeClass('fa-play');
		$('#play-pause-button .fa').addClass('fa-spin fa-circle-o-notch');
	}
}

function init_stream_player() { //don't hate the player, hate the web
	var playerElement = $('<audio id="main-player" src="http://icecast.uvm.edu:8005/wruv_fm_128"></audio>');
	var player = new MediaElement( playerElement.get(0), {
		type: 'audio/mpeg',
		success: function( media, dom ) {
			media.addEventListener('loadeddata', function() {
				update_playerState('playing');
			});
		}
	} );

	update_playerState('loading');
	player.play();

	return player;
}

function iterateMainVU() {
    var val = Math.floor( Math.random() * 80 + 1 ) - 45;
    $( '#needle' ).css('transform', 'rotateZ( '+val+'deg)');
}

$( document ).ready( function() {
	var player;

	var start_playing = readCookie('audio_playing');
	if( start_playing != '' && start_playing > 0 ) {
		player = init_stream_player();
	}

	$('#play-pause-button').click( function() {
		// console.log('playerState = ' , window.playerState);
		if( window.playerState == 'loading' ) {
			return;  //chill
		}
		else if ( window.playerState == 'paused' ) {
			update_playerState('playing');
			player.play();
		}
		else if( window.playerState == "playing" ) {
			update_playerState('paused');
			player.pause();
		}
		else { //time to set 'er up
			player = init_stream_player();
		}
	});
    $('.mobile-menu-open').click( function() {
        $('.mobile-menu-toggle').toggleClass('toggle');
    });


});
})(window.jQuery);


function eraseCookie(name) {
	writeCookie(name,"",-1);
}
function writeCookie(name,value,days) {
	var expires;
	if (arguments.length > 2) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		expires = "; expires="+date.toGMTString();
	}
	else
		expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name) {
	var nameEQ = name + "=";
	if( document.cookie ) {
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
	}
	return null;
}

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


$( document ).ready( function() {
  var playerElement = $('audio#main-player')[0];
  var player = new MediaElement( playerElement, {
    type: 'audio/mpeg',
		success: function( media, dom ) {
			if ( media.paused ) {
				window.playerState = 'paused';
			}
			media.addEventListener( 'playing', function() {
				window.playerState = 'playing';
			  window.VUTimer = setInterval( iterateMainVU, 200 );
			})
		}
	} );
	$('#play-pause-button').click( function() {
		if ( window.playerState == 'paused' ) {
			$('body').addClass('playing');
			$('#play-pause-button .fa').addClass('fa-pause');
			$('#play-pause-button .fa').removeClass('fa-play');
			player.play();
		} else {
			player.pause();
			$('body').removeClass('playing');
			$('#needle').css('transform', 'rotateZ(-45deg)');
			window.playerState = 'paused';
			clearTimeout(window.VUTimer);
			$('#play-pause-button .fa').removeClass('fa-pause');
			$('#play-pause-button .fa').addClass('fa-play');
		}
	});
	$('#chat-button').click( function() {
		console.log('opening chat');
		doChatLogin(document.forms.loginForm);
		$('#chat-area').slideToggle();
	});
})
function iterateMainVU() {
  var val = Math.floor( Math.random() * 80 + 1 ) - 45;
  $( '#needle' ).css('transform', 'rotateZ( '+val+'deg)');
}

})(window.jQuery);

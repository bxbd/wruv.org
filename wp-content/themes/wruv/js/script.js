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
  setInterval( iterateMainVU, 500 );
  populateBarVU();
})
function populateBarVU() {
  var parent_container = $('#bar-vu-meter > .inner');
  var parent_width = parent_container.width();
  var needs_more_bars = true;
  var i = 0;
  while( needs_more_bars ) {
    parent_container.append( '<div class="bar"></div>' );
    var current_bar = parent_container.find( '.bar:last-child' );
    console.log(parent_width);
    console.log(current_bar.position());
    if ( current_bar.position().left + 50 > parent_width ) {
      needs_more_bars = false;
    }
    if ( i > 50 ) {
      needs_more_bars = false;
    }
    i++;
  }
}
function iterateMainVU() {
  var val = Math.floor( Math.random() * 70 + 1 ) - 45;
  $( '#needle' ).css('transform', 'rotateZ( '+val+'deg)');
}

})(window.jQuery);

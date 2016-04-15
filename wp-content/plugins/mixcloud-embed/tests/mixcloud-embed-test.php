<?php

/**
 * Mocks
 */

function wp_oembed_add_provider()
{
    return;
}

function add_shortcode()
{
    return;
}

function add_filter()
{
    return;
}

function plugin_basename()
{
    return;
}

function add_action()
{
    return;
}

function get_option($name)
{
    switch ($name) {
        case 'mixcloud-embed_player_iframe':
            return '';
        case 'mixcloud-embed_player_width':
            return '100%';
        case 'mixcloud-embed_player_height':
            return '100%';
        case 'mixcloud-embed_auto_play':
        case 'mixcloud-embed_show_comments':
        case 'mixcloud-embed_theme_color':
        default:
            return '';
    }

}


require_once('../mixcloud-embed.php');
require_once('../mixcloud-embed-core.php');

class SC_Widget_Test extends PHPUnit_Framework_TestCase
{

    public function testMixcloudProfile(){

        $expected = '<a class="mixcloud-follow-widget" href="http://www.mixcloud.com/BJT/" data-h="200"  data-w="200" data-faces="on">Follow BJT on Mixcloud</a><script type="text/javascript" src="http://widget.mixcloud.com/media/js/follow_embed.js"></script>';
        $profile = new MixcloudProfile(array("height" => 200, "width" => 200), "http://www.mixcloud.com/BJT/");
        $this->assertEquals($expected, $profile -> __toString());
    }

    /**
     * @expectedException Exception
     */
    public function testMixcloudProfileError(){

        $expected = '<a class="mixcloud-follow-widget" href="http://www.mixcloud.com/BJT/bjt-march-2013-techno-mix/" data-h="200"  data-w="200" data-faces="on">Follow BJT on Mixcloud</a><script type="text/javascript" src="http://widget.mixcloud.com/media/js/follow_embed.js"></script>';
        $profile = new MixcloudProfile(array("height" => 200, "width" => 200), "http://www.mixcloud.com/BJT/bjt-march-2013-techno-mix/");
        $this->assertEquals($expected, $profile -> __toString());
    }

    public function testMixcloudPlaylist(){

    }

    public function testMixcloudPlaylistError(){

    }



}

?>

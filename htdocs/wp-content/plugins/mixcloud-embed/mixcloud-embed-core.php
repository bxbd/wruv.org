<?php
/**
 *
 * @author  : Domenico Biancardi
 * @email   : domenico.biancardi@gmail.com
 * Created  : 29/03/13 - {16.00}
 */


/**
 * Get the playlist from a single cloudcast
 */
class MixcloudPlaylist
{
    /**
     * @var
     */
    private $url;
    /**
     * @var wpdb
     */
    private $wpdb;
    /**
     * @var WP_Error
     */
    private $errors;
    /**
     * @var string
     */
    private $table_name;

    function __construct($url)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->errors = new WP_Error();
        $this->table_name = $this->wpdb->prefix . "mixcloudPlaylist";
        $this->url = $url;
    }

    function __toString()
    {

        // if the playlist are saved to db, i load it from db
        $playlist = $this->wpdb->get_row("SELECT ID, url, playlist FROM " . $this->table_name . " WHERE url = '" . $this->url . "'");
        if ($this->wpdb->num_rows > 0) {
            $playlist = unserialize($playlist->playlist);
        } else {
            $playlist = array();
            $code = implode("", file($this->url));
            if ($code == "") $this->errors->add('no_content', __('The url $url are not valid!'));
            preg_match_all("/section-row-track(.+)/", $code, $results);
            for ($i = 0; $i < sizeof($results[0]); $i++) {
                preg_match("/class=\"tracklisttrackname mx-link\">(.+)<\/a>/U", $results[0][$i], $match);
                $title = $match[1];
                preg_match("/class=\"tracklistartistname mx-link\">(.+)<\/a>/U", $results[0][$i], $match);
                $artist = $match[1];
                if ($title != "" || $artist != "") {
                    $playlist[] = array("title" => $title, "artist" => $artist);
                }
            }

            $this->wpdb->show_errors();
            // save to db the playlist for this url
            $this->wpdb->insert($this->table_name, array("url" => $this->url, "playlist" => serialize($playlist)), array("%s", "%s"));
        }
        $code = "<h3>Playlist</h3><ul class='mixcloud-embed-playlist'>";
        for ($i = 0; $i < count($playlist); $i++) {
            $code .= "<li><span class='mixcloud-embed-position'>" . ($i + 1) . "</span>";
            $code .= "<span class='mixcloud-embed-artist'>" . $playlist[$i]["artist"] . "</span>";
            $code .= "<span class='mixcloud-embed-title'>" . $playlist[$i]["title"] . "</span></li>";

        }
        $code .= "</ul>";
        return $code;
    }


}

/**
 * Abstract object of mixcloud embed
 */
abstract class AbstractMixcloudObject
{
    /**
     * @var
     */
    protected $width;
    /**
     * @var
     */
    protected $height;
    /**
     * @var array
     */
    protected $params;

    abstract public function getUuid();

    /**
     * @param $options
     * @param $url
     */
    function __construct($options, $url)
    {
        $this->_options = $options;
        $this->width = $this->_options["width"];
        $this->height = $this->_options["height"];
    }

	/**
	 * Make an array from options
	 */
	function arrayOptions(){
		$arrayOpts = array();
		$arrayOpts["embed_uuid"] = $this->getUuid();
		$arrayOpts["replace"] = 0;
		if (!$this -> _options["cover"]) $arrayOpts["hide_cover"] = 1;
		if (!$this -> _options["artwork"]) $arrayOpts["hide_artwork"] = 1;
		$arrayOpts["embed_type"] = "widget_standard";
		if (!$this -> _options["tracklist"]) $arrayOpts["hide_tracklist"] = 1;
		$arrayOpts["stylecolor"] = $this->_options['color'];
		$arrayOpts["mini"] = $this->_options['mini'];
		$arrayOpts["light"] = $this->_options['light'];

		$str = "";
		foreach($arrayOpts as $k => $v){
			$str .= "&amp;$k=$v";
		}
		return $str;

	}
}

class MixcloudEmbedObject extends AbstractMixcloudObject
{
    protected $movie;

    function __construct($options, $url)
    {
        parent::__construct($options, $url);
        $this->movie = "//www.mixcloud.com/media/swf/player/mixcloudLoader.swf?feed=".urlencode($url) .  $this -> arrayOptions();
        $this->params["allowFullScreen"] = "true";
        $this->params["wmode"] = "opaque";
        $this->params["allowscriptaccess"] = "always";

    }

    public function getUuid()
    {
        return "8d600ec2-4de4-4196-804f-5d839aac495d";
    }


    function __toString()
    {
        $object = new FlashObject($this->movie, $this->width, $this->height, 'Mixcloud Mixes Object', 'mixcloud-embed');

        $object->setParams($this->params);

        return $object->get();
    }


}

class MixcloudEmbedHtml5 extends AbstractMixcloudObject
{

    function __construct($options, $url)
    {
        parent::__construct($options, $url);
        $this->movie = "https://www.mixcloud.com/widget/iframe/?feed=".urlencode($url) . $this -> arrayOptions();

		/**
         * <iframe width="660" height="180" src="https://www.mixcloud.com/widget/iframe/?embed_type=widget_standard&amp;embed_uuid=a562e040-b4ce-4abf-98fa-7396e710bd34&amp;feed=https%3A%2F%2Fwww.mixcloud.com%2Fdottblanchard%2Fdott-blanchard-discovering-music%2F&amp;hide_cover=1&amp;hide_tracklist=1&amp;replace=0" frameborder="0"></iframe><div style="clear: both; height: 3px; width: 652px;"></div><p style="display: block; font-size: 11px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; margin: 0px; padding: 3px 4px; color: rgb(153, 153, 153); width: 652px;"><a href="https://www.mixcloud.com/dottblanchard/dott-blanchard-discovering-music/?utm_source=widget&amp;utm_medium=web&amp;utm_campaign=base_links&amp;utm_term=resource_link" target="_blank" style="color:#808080; font-weight:bold;">Dott. Blanchard - Discovering Music</a><span> by </span><a href="https://www.mixcloud.com/dottblanchard/?utm_source=widget&amp;utm_medium=web&amp;utm_campaign=base_links&amp;utm_term=profile_link" target="_blank" style="color:#808080; font-weight:bold;">Dott. Blanchard</a><span> on </span><a href="https://www.mixcloud.com/?utm_source=widget&amp;utm_medium=web&amp;utm_campaign=base_links&amp;utm_term=homepage_link" target="_blank" style="color:#808080; font-weight:bold;"> Mixcloud</a></p><div style="clear: both; height: 3px; width: 652px;"></div>
         * <iframe width='660' height='208' src='https://www.mixcloud.com/widget/iframe/?embed_type=widget_standard&amp;embed_uuid=a562e040-b4ce-4abf-98fa-7396e710bd34&amp;feed=%3Ca+href%3D%22https%3A%2F%2Fwww.mixcloud.com%2Fdjmarkomusic%2Fmark-o-alce-cantado-2-nov14%2F%255B%2Fmixcloud%255D%22+target%3D%22_blank%22+rel%3D%22noreferrer%22%3Ehttps%3A%2F%2Fwww.mixcloud.com%2Fdjmarkomusic%2Fmark-o-alce-cantado-2-nov14%2F%3C%2Fa%3E&amp;replace=0&amp;hide_cover=1&amp;hide_artwork=1&amp;hide_tracklist=1&amp;stylecolor=#fffff&amp;mini=&amp;light=' frameborder='0'></iframe>
		 */
    }



    public function getUuid()
    {
        return "a562e040-b4ce-4abf-98fa-7396e710bd34";
    }

    function __toString()
    {

        return "<iframe width='" . $this->width . "' height='" . $this->height . "' src='" . $this->movie . "' frameborder='0'></iframe>";
    }


}


class MixcloudMultiEmbedObject extends MixcloudEmbedObject
{
    public function getUuid()
    {
        return "24fa4730-87be-4a1a-a3cf-b83cc4b21651";
    }

}

class MixcloudMultiEmbedHtml5 extends MixcloudEmbedHtml5
{
    public function getUuid()
    {
        return "9e28450c-d230-4d75-8fa8-9f2841cb0165";
    }

}

class MixcloudProfile
{
    private $url;
    private $options;

    function __construct($options, $url)
    {
        $this->url = $url;
        if (count(explode("/", $this->url) ) > 5) throw new Exception("Error in a profile url, check the right url");
        $this->options = $options;
    }

    public function __toString()
    {
        $explode = explode("/", $this->url);
        $profile = $explode[0] . "//" . $explode[2] . "/" . $explode[3] . "/";
        $code = '<a class="mixcloud-follow-widget" href="' . $profile . '" data-h="' . $this->options["height"] . '"  data-w="' . $this->options["width"] . '" data-faces="on">Follow ' . $explode[3] . ' on Mixcloud</a><script type="text/javascript" src="http://widget.mixcloud.com/media/js/follow_embed.js"></script>';
        return $code;
    }

}

class MixcloudEmbedCore
{


    private $table_name;
    private $wpdb;
    private $_options;
    private $_ui;


    function MixcloudEmbedCore()
    {
        //constructor
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->errors = new WP_Error();
        $this->table_name = $this->wpdb->prefix . "mixcloudPlaylist";
        $this->LoadOptions();
    }

    /**
     * Enables the Mixcloud Embed and registers the WordPress hooks
     *
     * @since 1.5
     * @access public
     * @static
     */
    static function Enable()
    {

        if (!isset($GLOBALS["me_instance"])) {
            $GLOBALS["me_instance"] = new MixcloudEmbedCore();
        }

    }

    /**
     * Returns the instance of the Sitemap Generator
     *
     * @since 1.5
     * @access public
     * @return MixcloudEmbedCore The instance or null if not available.
     */
    static function &GetInstance()
    {
        if (isset($GLOBALS["me_instance"])) {
            return $GLOBALS["me_instance"];
        } else return null;
    }

    /**
     * Returns a link pointing back to the plugin page in WordPress
     *
     * @since 1.5
     * @return string The full url
     */
    function GetBackLink()
    {
        global $wp_version;
        //admin_url was added in WP 2.6.0
        if (function_exists("admin_url")) $url = admin_url("options-general.php?page=" . MixcloudEmbed::GetBaseName());
        else $url = $_SERVER['PHP_SELF'] . "?page=" . MixcloudEmbed::GetBaseName();

        //Some browser cache the page... great! So lets add some no caching params depending on the WP and plugin version
        $url .= '&me_wpv=' . $wp_version . '&me_pv=' . MixcloudEmbed::GetVersion();

        return $url;
    }

    /**
     * Shows the option page of the plugin. Before 3.1.1, this function was basically the UI, afterwards the UI was outsourced to another class
     *
     * @see MixcloudEmbedUI
     * @since 1.5
     * @return bool
     */
    function HtmlShowOptionsPage()
    {
        if (!empty($_POST['mixcloud-embed_update'])) { //Pressed Button: Update Config
            // @TODO verificare il funzionamento
            // check_admin_referer('mixcloud-embed');
            $this->_options = $_POST;
            $message = "";
            if ($this->SaveOptions()) $message .= __('Configuration updated', 'mixcloud-embed') . "<br />";
            else $message .= __('Error while saving options', 'mixcloud-embed') . "<br />";
        }
        $ui = $this->GetUI();
        if ($ui) {
            $ui->HtmlShowOptionsPage();
            return true;
        }

        return false;
    }

    /**
     * Includes the user interface class and intializes it
     *
     * @since 1.5
     * @see MixcloudEmbedUI
     * @return MixcloudEmbedUI
     */
    function GetUI()
    {

        if ($this->_ui === null) {

            $className = 'MixcloudEmbedUI';
            $fileName = 'mixcloud-embed-ui.php';

            if (!class_exists($className)) {

                $path = trailingslashit(dirname(__FILE__));

                if (!file_exists($path . $fileName)) return false;
                require_once($path . $fileName);
            }

            $this->_ui = new $className($this);

        }

        return $this->_ui;
    }


    /**
     * [mixcloud height="int value" width="int value" iframe="boolean value"]
     * The following function creates a "[mixcloud]" shortcode that supports two attributes: ["height" and "width"].
     * Both attributes are optional and will take on default options [height="300" width="300" iframe="true"] if they are not provided.
     * This shortcode handler function accepts two arguments:
     * $atts, an associative array of attributes
     * $content, the enclosed content (if the shortcode is used in its enclosing form)
     */
    function CreateShortcode($options, $content = null)
    {
        $urlMaker = $options[0];
        // read if there are a default value or a customizated value
        $options = array(
            'height' 	=> (isset($options["height"]) != "") 	? $options["height"] 	: $this->getOption("player_height"),
            'width' 	=> (isset($options["width"]) != "") 	? $options["width"] 	: $this->getOption("player_width"),
            'color' 	=> (isset($options["color"]) != "") 	? $options["color"] 	: $this->getOption("player_color"),
            'iframe' 	=> (isset($options["iframe"]) != "") 	? $options["iframe"] 	: $this->getOption("player_iframe"),
            'playlist' 	=> (isset($options["playlist"]) != "") 	? $options["playlist"] 	: $this->getOption("player_playlist"),
			'cover' 	=> (isset($options["cover"]) != "") 	? $options["cover"] 	: $this->getOption("player_cover"),
			'artwork' 	=> (isset($options["artwork"]) != "") 	? $options["artwork"] 	: $this->getOption("player_artwork"),
			'tracklist' => (isset($options["tracklist"]) != "") ? $options["tracklist"] : $this->getOption("player_tracklist"),
			'mini' 		=> (isset($options["mini"]) != "") 		? $options["mini"] 		: $this->getOption("player_mini"),
			'light' 	=> (isset($options["light"]) != "") 	? $options["light"] 	: $this->getOption("player_light"),
			'autoplay' 	=> (isset($options["autoplay"]) != "") 	? $options["autoplay"] 	: $this->getOption("player_autoplay"),
            'profile' 	=> (isset($options["profile"]) != "") 	? $options["profile"] 	: $this->getOption("widget_profile"),
        );



        // clear a width or height value
        $options["width"] = str_replace("px", "", $options["width"]);
        $options["height"] = str_replace("px", "", $options["height"]);

        // the content are required
        if ($content == "") {
            if ($urlMaker == "") {
                $this->errors->add('no_url', __('The url to mixcloud stream are required!'));
            }else{
                $content = $urlMaker;
            }
        }

        if ($options["profile"] == "true") {
            $object = $this->makeProfileWidget($options, $content);
        } else {
            $object = $this->makeEmbedWidget($options, $content);
        }

        //print_R(debug_backtrace());
        return $object;
    }

    /**
     * @param $options
     * @param $content string
     * @return MixcloudEmbedObject|MixcloudMultiEmbedObject
     */
    private function makeEmbedWidget($options, $content)
    {
        $multiEmbed = false;
        /**
         * @var MixcloudPlaylist
         */
        $playlistCode = null;
        // se ho il content che contiene un url alla playlists allora sto includendo una playlist
        if (strpos($content, "/playlists/") !== false) {
            // se ho il codice HTML di LINK AHREF allora estraggo solo l'url
            if (strpos($content, "<a ") !== false){
                // while matches found
                while(preg_match('/<a href="([^"]*)">/', $content, $matches)){
                    // print captured group that's actually the url your searching for
                    $content = $matches[1];
                }
            }

            $multiEmbed = true;
            if ($options["iframe"] == "true") {
                $object = new MixcloudMultiEmbedHtml5($options, $content);
            } else {
                $object = new MixcloudMultiEmbedObject($options, $content);
            }

        } else {

            // se ho il codice HTML di LINK AHREF allora estraggo solo l'url
            if (strpos($content, "<a ") !== false){
                // while matches found
                while(preg_match('/<a href="([^"]*)">/', $content, $matches)){
                    // print captured group that's actually the url your searching for
                    $content = $matches[1];
                }
            }


            if ($options["iframe"] == "true") {
                $object = new MixcloudEmbedHtml5($options, $content);
            } else {
                $object = new MixcloudEmbedObject($options, $content);
            }
        }



        if ($options["playlist"] == "true"  && !$multiEmbed) {
            // get a playlist information
        //    $playlistCode = new MixcloudPlaylist($content);
        }

        if (sizeof($this->errors->get_error_messages()) > 0) {
            $code = "<b>##############<br/>Cannot generate a Mixcloud Embed because: <ul>";
            for ($i = 0; $i < sizeof($this->errors->get_error_messages()); $i++) {
                $code .= "<li>" . $this->errors->get_error_message("no_url") . "</li>";
            }
            $code .= "</ul>##############</b>";
        }

        if ($playlistCode != null)
            return $object->__toString() . " " . $playlistCode->__toString();
        return $object->__toString();
    }

    private function makeProfileWidget($atts, $content)
    {
        return new MixcloudProfile($atts, $content);
    }


    /**
     * Sets up the default configuration
     *
     * @since 1.5
     * @access private
     */
    private function InitOptions()
    {

        $this->_options = array();

        $this->_options['mixcloud-embed_player_height'] = '200';
        $this->_options['mixcloud-embed_player_width'] = '100%';
        $this->_options['mixcloud-embed_player_iframe'] = true;
        $this->_options['mixcloud-embed_player_color'] = '#fffff';
        $this->_options['mixcloud-embed_player_playlist'] = false;
        $this->_options['mixcloud-embed_player_mini'] = false;
        $this->_options['mixcloud-embed_player_light'] = false;
        $this->_options['mixcloud-embed_widget_profile'] = "";
    }

    /**
     * Loads the configuration from the database
     *
     * @since 1.5
     * @access private
     */
    private function LoadOptions()
    {

        $this->InitOptions();

        //First init default values, then overwrite it with stored values so we can add default
        //values with an update which get stored by the next edit.
        $storedoptions = get_option("mixcloud-embed-settings");
        if ($storedoptions && is_array($storedoptions)) {
            foreach ($storedoptions AS $k => $v) {
                $this->_options[$k] = $v;
            }
        } else update_option("mixcloud-embed-settings", $this->_options); //First time use, store default values

    }

    /**
     * Returns the option value for the given key
     *
     * @since 1.5
     * @access public
     * @param $key string The Configuration Key
     * @return mixed The value
     */
    public function GetOption($key)
    {
        $key = "mixcloud-embed_" . $key;
        if (array_key_exists($key, $this->_options)) {
            return $this->_options[$key];
        } else return null;
    }

    /**
     * Sets an option to a new value
     *
     * @since 1.5
     * @access public
     * @param $key string The configuration key
     * @param $value mixed The new object
     */
    public function SetOption($key, $value)
    {
        if (strstr($key, "mixcloud-embed_") !== 0) $key = "mixcloud-embed_" . $key;

        $this->_options[$key] = $value;
    }

    /**
     * Saves the options back to the database
     *
     * @since 1.5
     * @access private
     * @return bool true on success
     */
    private function SaveOptions()
    {
        $oldvalue = get_option("mixcloud-embed-settings");

        if ($oldvalue == $this->_options) {
            return true;
        } else return update_option("mixcloud-embed-settings", $this->_options);
    }


}
?>